<?php

namespace App\Providers;

use App\Actions\Fortify\ResetUserPassword;
use App\Http\Responses\LogoutResponse;
use App\Http\Responses\PasswordResetResponse;
use App\Http\Responses\ResetLinkRequestedResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Contracts\PasswordResetResponse as PasswordResetResponseContract;
use Laravel\Fortify\Contracts\SuccessfulPasswordResetLinkRequestResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Our routes are in routes/web.php, so only sign-in, sign-out and password reset exist.
        Fortify::ignoreRoutes();

        // A reset request answers the same whether or not the address belongs to a member.
        $this->app->bind(SuccessfulPasswordResetLinkRequestResponse::class, ResetLinkRequestedResponse::class);
        $this->app->bind(FailedPasswordResetLinkRequestResponse::class, ResetLinkRequestedResponse::class);
        $this->app->bind(PasswordResetResponseContract::class, PasswordResetResponse::class);
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    public function boot(): void
    {
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::loginView(fn () => view('auth.sign-in'));
        Fortify::requestPasswordResetLinkView(fn () => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn (Request $request) => view('auth.reset-password', [
            'token' => $request->route('token'),
            'email' => (string) $request->query('email', ''),
        ]));

        // Only approved members with a password sign in. Everyone else gets the same
        // answer as for a wrong password, so the form does not reveal who is a member.
        Fortify::authenticateUsing(function (Request $request): ?User {
            // No "remember me": only the session cookie may be set (docs/DECISIONS.md).
            $request->request->remove('remember');

            $user = User::where('email', Str::lower((string) $request->input('email')))->first();

            if ($user === null || $user->password === null || ! $user->isApproved()) {
                return null;
            }

            return Hash::check((string) $request->input('password'), $user->password) ? $user : null;
        });
    }
}
