<?php

namespace App\Http\Controllers;

use App\Models\PinCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PincodeController extends Controller
{
    public function lookup(Request $request): JsonResponse
    {
        $pin = preg_replace('/\D/', '', (string) $request->get('pin', ''));

        if (strlen($pin) !== 6) {
            return response()->json(['ok' => false, 'message' => 'Enter a 6-digit pin code.']);
        }

        $row = PinCode::query()->where('code', $pin)->first();

        if (! $row) {
            return response()->json([
                'ok' => false,
                'message' => 'We do not deliver to this pin code yet.',
            ]);
        }

        return response()->json([
            'ok' => true,
            'pin' => $row->code,
            'area' => $row->area,
            'post_office' => $row->post_office,
            'city' => $row->city,
            'state' => $row->state,
            'label' => $row->fullLabel(),
        ]);
    }

    public function setDelivery(Request $request): JsonResponse
    {
        $pin = preg_replace('/\D/', '', (string) $request->input('pin', ''));

        if (strlen($pin) !== 6) {
            return response()->json(['ok' => false, 'message' => 'Invalid pin code.'], 422);
        }

        $row = PinCode::query()->where('code', $pin)->first();

        if (! $row) {
            return response()->json(['ok' => false, 'message' => 'Service not available for this pin.'], 422);
        }

        $request->session()->put('delivery_pin', $row->code);
        $request->session()->put('delivery_area', $row->area);
        $request->session()->put('delivery_city', $row->city);

        return response()->json([
            'ok' => true,
            'label' => $row->fullLabel(),
        ]);
    }
}
