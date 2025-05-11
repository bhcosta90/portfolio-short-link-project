<?php

declare(strict_types = 1);

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class SendCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $code)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Your Verification Code'))
            ->greeting(__('Hello!'))
            ->line(__('We received a request to verify your email address.'))
            ->line(new \Illuminate\Support\HtmlString(__('Your verification code is: <b>:code</b>', ['code' => $this->code])))
            ->line(__('If you did not request this, please ignore this email.'))
            ->salutation(__('Thank you, :appName Team', ['appName' => config('app.name')]));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
