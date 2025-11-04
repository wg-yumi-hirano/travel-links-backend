<?php declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends BaseVerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        // Laravel で生成される URL からパラメーターを抽出
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('project.verification.expire_minutes', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        $parsed = parse_url($verificationUrl);
        parse_str($parsed['query'] ?? '', $queryParams);

        // URL を frontend 用に変換
        $frontendUrl = env('MAIL_VERIFY_EMAIL_URL', '');
        $frontendVerificationUrl = $frontendUrl . '?' . http_build_query([
            'id' => $queryParams['id'] ?? $notifiable->getKey(),
            'hash' => $queryParams['hash'] ?? sha1($notifiable->getEmailForVerification()),
            'expires' => $queryParams['expires'] ?? '',
            'signature' => $queryParams['signature'] ?? '',
        ]);

        return (new MailMessage)
            ->view('emails.verify', ['actionUrl' => $frontendVerificationUrl]);
    }
}