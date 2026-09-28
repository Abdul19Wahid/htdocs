<?php

namespace App\Providers;

use App\Mail\Transport\BrevoApiTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Registers the "brevo" mail transport (see config/mail.php and
        // BrevoApiTransport) — sends over HTTPS instead of SMTP, since
        // this app's host (InfinityFree) blocks outbound SMTP ports.
        Mail::extend('brevo', function () {
            return new BrevoApiTransport(config('services.brevo.key'));
        });

        // Default password rule was previously just "8 characters, nothing
        // else" (Password::defaults() uncustomized). For a finance app,
        // require a mix of cases and at least one number, and reject
        // passwords found in known breach lists — applies everywhere
        // Password::defaults() is used (registration, password reset,
        // password change).
        Password::defaults(function () {
            return Password::min(8)->mixedCase()->numbers()->uncompromised();
        });

        // Feeds the quick-add bottom sheet (in the shared layout) with the
        // logged-in user's categories/income sources, so it doesn't need
        // an extra request just to open. Cheap per-user queries, only run
        // for authenticated requests that actually render the layout.
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $view->with('quickAddCategories', $user ? $user->categories()->orderBy('name')->get(['id', 'name']) : collect());
            $view->with('quickAddIncomeSources', $user ? $user->incomeSources()->orderBy('name')->get(['id', 'name']) : collect());
        });
    }
}
