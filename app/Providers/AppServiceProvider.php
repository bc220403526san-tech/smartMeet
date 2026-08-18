<?php

namespace App\Providers;
use App\Models\MeetingParticipant;
use App\Models\Observers\MeetingParticipantObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        MeetingParticipant::observe(MeetingParticipantObserver::class);
        // Password reset email ka link custom route se banayega
        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            return url(route('reset.password', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        });

    }
}
