<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

use Illuminate\Support\Facades\Config;

use App\MITD\URLBuilder;

class EmailVerificationProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        VerifyEmail::toMailUsing(function (object $notifiable) {
            $url = $this->createVerificationUrl($notifiable);
            return (new MailMessage())
                ->subject("Verify Email Address")
                ->line("Click the button below to verify your email address.")
                ->action("Verify Email Address", $url);
        });
    }

    private function createVerificationUrl(object $notifiable): string {
        $id = $notifiable->hash ?? $notifiable->getKey();
        $expiration = Carbon::now()->addMinutes(Config::get("auth.verification.expire", 60));
        $hash = sha1($id . "_" . $notifiable->getEmailForVerification() . "_" . $expiration->getTimestamp());
        $builder = new URLBuilder(config("mitd.email.domain_url"));
        $builder->paths(config("mitd.email.verification.path"), $id, $hash)->signUrl($expiration);
        return $builder->build();
    }
}
