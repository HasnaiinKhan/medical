<?php

namespace App\Services;

use App\Jobs\SendWhatsAppOrderNotification;
use App\Mail\AdminOrderNotificationMail;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Notify admin of a new order via configured channels.
     * Never throws — errors are logged silently.
     */
    public static function notifyAdmin(Order $order): void
    {
        $emailEnabled     = (bool) Setting::get('admin_email_notifications', '1');
        $whatsappEnabled  = (bool) Setting::get('admin_whatsapp_notifications', '0');
        $adminEmail       = Setting::get('admin_email', config('mail.from.address'));

        // ── Email ──────────────────────────────────────────────────────────
        if ($emailEnabled && $adminEmail) {
            try {
                Mail::to($adminEmail)->send(new AdminOrderNotificationMail($order));
            } catch (\Throwable $e) {
                Log::error("Admin order email notification failed: {$e->getMessage()}");
            }
        }

        // ── WhatsApp ───────────────────────────────────────────────────────
        if ($whatsappEnabled) {
            try {
                SendWhatsAppOrderNotification::dispatch($order);
            } catch (\Throwable $e) {
                Log::error("Admin WhatsApp notification dispatch failed: {$e->getMessage()}");
            }
        }
    }
}
