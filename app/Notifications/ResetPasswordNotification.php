<?php declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(private string $token) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Laravel で生成される URL からパラメーターを抽出
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $parsed = parse_url($resetUrl);
        parse_str($parsed['query'] ?? '', $queryParams);

        // URL を frontend 用に変換
        $frontendUrl = env('MAIL_RESET_PASSWORD_URL', '');
        $frontendResetUrl = $frontendUrl . '?' . http_build_query([
            'token' => $queryParams['token'] ?? $this->token,
            'email' => $queryParams['email'] ?? $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject(__('project.reset_password.title'))
            ->view('emails.reset-password', [
                'actionUrl' => $frontendResetUrl,
                'user' => $notifiable,
            ]);
    }
}
