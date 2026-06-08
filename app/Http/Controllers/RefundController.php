<?php

namespace App\Http\Controllers;

use App\Mail\RefundProcessed;
use App\Mail\RefundRequested;
use App\Models\Order;
use App\Models\Refund;
use App\Models\RefundAuditLog;
use App\Services\RefundServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function __construct(private RefundServiceInterface $refundService) {}

    /** Show the refund request form */
    public function create(Order $order): View|RedirectResponse
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if (! $order->canRequestRefund()) {
            return back()->with('error', $this->refundIneligibleReason($order));
        }

        return view('refunds.create', compact('order'));
    }

    /** Submit the refund request */
    public function store(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if (! $order->canRequestRefund()) {
            return back()->with('error', $this->refundIneligibleReason($order));
        }

        $data = $request->validate([
            'reason'              => ['required', 'string', 'max:1000'],
            'proof_image'         => ['nullable', 'image', 'max:4096'],
            // Bank details — required when bank transfer is chosen
            'bank_account_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{9,18}$/'],
            'bank_ifsc'           => ['nullable', 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'bank_account_name'   => ['nullable', 'string', 'max:100'],
            // UPI — alternative to bank
            'upi_id'              => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+$/'],
            'refund_method_choice'=> ['required', 'in:bank,upi'],
        ]);

        // Validate bank/UPI fields based on chosen method
        if ($data['refund_method_choice'] === 'bank') {
            $request->validate([
                'bank_account_number' => ['required', 'string', 'max:20', 'regex:/^[0-9]{9,18}$/'],
                'bank_ifsc'           => ['required', 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
                'bank_account_name'   => ['required', 'string', 'max:100'],
            ]);
        } else {
            $request->validate([
                'upi_id' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9]+$/'],
            ]);
        }

        DB::transaction(function () use ($order, $data, $request) {
            // Determine refund type based on payment method and chosen refund method
            $chosenMethod = $data['refund_method_choice'];
            if ($order->isCOD()) {
                $refundType = $chosenMethod === 'upi' ? 'cod_upi' : 'cod_bank_transfer';
            } else {
                $refundType = $chosenMethod === 'upi' ? 'online_upi' : 'online_bank_transfer';
            }

            // Handle proof image upload
            $proofPath = null;
            if ($request->hasFile('proof_image')) {
                $proofPath = $request->file('proof_image')->store('refund-proofs', 'public');
            }

            $refund = Refund::create([
                'order_id'            => $order->id,
                'refund_number'       => 'RFD-' . strtoupper(Str::random(8)),
                'amount_paise'        => $order->total_paise,
                'status'              => 'requested',
                'type'                => $refundType,
                'reason'              => $data['reason'],
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'bank_ifsc'           => strtoupper($data['bank_ifsc'] ?? ''),
                'bank_account_name'   => $data['bank_account_name'] ?? null,
                'upi_id'              => $data['upi_id'] ?? null,
                'proof_image_path'    => $proofPath,
            ]);

            // Audit log
            RefundAuditLog::create([
                'refund_id'  => $refund->id,
                'user_id'    => Auth::id(),
                'action'     => 'requested',
                'from_status'=> null,
                'to_status'  => 'requested',
                'notes'      => 'Customer submitted refund request.',
                'actor_type' => 'customer',
            ]);

            $order->update([
                'status'              => 'cancellation_requested',
                'cancellation_reason' => $data['reason'],
            ]);

            // Send confirmation email to customer
            try {
                $refund->load('order');
                Mail::to(Auth::user()->email)->send(new RefundRequested($refund));
            } catch (\Throwable $e) {
                Log::error('RefundRequested mail failed: ' . $e->getMessage());
            }

            // Notify admin
            try {
                $adminEmail = config('mail.from.address');
                Mail::to($adminEmail)->send(new RefundRequested($refund));
            } catch (\Throwable $e) {
                Log::error('Admin RefundRequested mail failed: ' . $e->getMessage());
            }
        });

        return redirect()->route('orders.show', $order)
            ->with('status', 'Refund request submitted. You will receive a confirmation email shortly.');
    }

    /** Process gateway refund — called internally or by admin */
    public function processGatewayRefund(Order $order, Refund $refund): void
    {
        $refund->transitionTo('processing', 'Gateway refund initiated.', 'system');
        $order->update(['status' => 'refund_initiated']);

        $response = $this->refundService->process(
            $order,
            $order->totalRupees(),
            $refund->reason ?? 'Customer requested refund'
        );

        if ($response['success']) {
            $refund->update([
                'refund_id_gateway' => $response['refund_id'],
                'metadata'          => $response['raw'],
                'processed_at'      => now(),
            ]);
            $refund->transitionTo('processed', 'Gateway refund successful. ID: ' . $response['refund_id'], 'system');

            $order->update(['status' => 'refunded', 'payment_status' => 'refunded']);

            // Restock only if medicines never left warehouse
            if (! $order->is_dispatched) {
                foreach ($order->items as $item) {
                    if ($item->medicine) {
                        $item->medicine->increment('stock', $item->quantity);
                    }
                }
            }

            // Notify customer
            try {
                $refund->load('order');
                if ($order->user) {
                    Mail::to($order->user->email)->send(new RefundProcessed($refund));
                }
            } catch (\Throwable $e) {
                Log::error('RefundProcessed mail failed: ' . $e->getMessage());
            }
        } else {
            $refund->transitionTo('failed', 'Gateway error: ' . ($response['error'] ?? 'Unknown'), 'system');
            Log::error('Refund processing failed', ['order' => $order->id, 'error' => $response['error']]);
        }
    }

    /** Human-readable reason why refund is not eligible */
    private function refundIneligibleReason(Order $order): string
    {
        if ($order->created_at->diffInDays(now()) > 30) {
            return 'Refund window has expired. Refunds are only allowed within 30 days of order placement.';
        }
        if ($order->refunds()->whereIn('status', ['requested', 'approved', 'processing', 'processed'])->exists()) {
            return 'A refund request already exists for this order.';
        }
        return 'This order is not eligible for a refund.';
    }
}
