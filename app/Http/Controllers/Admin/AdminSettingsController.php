<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function notifications(): View
    {
        $settings = Setting::getMany([
            'admin_email_notifications',
            'admin_whatsapp_notifications',
            'admin_email',
            'admin_whatsapp_number',
            'whatsapp_api_token',
            'whatsapp_phone_number_id',
        ]);

        return view('admin.settings.notifications', compact('settings'));
    }

    public function saveNotifications(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'admin_email_notifications'    => ['nullable', 'boolean'],
            'admin_whatsapp_notifications' => ['nullable', 'boolean'],
            'admin_email'                  => ['nullable', 'email', 'max:200'],
            'admin_whatsapp_number'        => ['nullable', 'string', 'max:20', 'regex:/^[0-9]{10,15}$/'],
            'whatsapp_api_token'           => ['nullable', 'string', 'max:500'],
            'whatsapp_phone_number_id'     => ['nullable', 'string', 'max:100'],
        ]);

        Setting::set('admin_email_notifications',    $request->boolean('admin_email_notifications') ? '1' : '0');
        Setting::set('admin_whatsapp_notifications', $request->boolean('admin_whatsapp_notifications') ? '1' : '0');
        Setting::set('admin_email',                  $data['admin_email'] ?? '');
        Setting::set('admin_whatsapp_number',        $data['admin_whatsapp_number'] ?? '');
        Setting::set('whatsapp_api_token',           $data['whatsapp_api_token'] ?? '');
        Setting::set('whatsapp_phone_number_id',     $data['whatsapp_phone_number_id'] ?? '');

        return back()->with('status', 'Notification settings saved successfully.');
    }

    public function testNotification(string $channel): RedirectResponse
    {
        $order = \App\Models\Order::with('items')->latest()->first();

        if (! $order) {
            return back()->with('error', 'No orders found to test with.');
        }

        if ($channel === 'email') {
            $adminEmail = Setting::get('admin_email', config('mail.from.address'));
            try {
                \Illuminate\Support\Facades\Mail::to($adminEmail)
                    ->send(new \App\Mail\AdminOrderNotificationMail($order));
                return back()->with('status', "Test email sent to {$adminEmail}.");
            } catch (\Throwable $e) {
                return back()->with('error', 'Email failed: ' . $e->getMessage());
            }
        }

        if ($channel === 'whatsapp') {
            \App\Jobs\SendWhatsAppOrderNotification::dispatch($order);
            return back()->with('status', 'WhatsApp notification job dispatched. Check queue worker output.');
        }

        return back()->with('error', 'Unknown channel.');
    }
}
