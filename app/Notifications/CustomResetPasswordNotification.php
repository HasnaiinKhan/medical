<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $appName = config('app.name', 'Rx Plus 365');
        $expiry  = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject("Your {$appName} account access link")
            ->greeting("Hello,")
            ->line("We received a request to update the sign-in details for your {$appName} account.")
            ->line("Use the button below to complete the process. The link will remain active for {$expiry} minutes.")
            ->action('Update Account Access', $url)
            ->line("If you did not make this request, no changes have been made and you can safely ignore this message.")
            ->salutation("— The {$appName} Team");
    }
}