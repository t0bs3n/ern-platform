<?php

namespace App\Providers;

use App\Auth\Permissions;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        // One gate per area (R0-06). Templates use @can('<gate>'), routes use can:<gate>.
        foreach (Permissions::GATES as $gate => $roles) {
            Gate::define($gate, fn (User $user) => $user->hasRole(...$roles));
        }

        // Writing to another member through the directory (R1-13): never to oneself, only to listed members.
        Gate::define('contact-member', fn (User $sender, User $recipient) => $sender->hasRole(Role::Member)
            && ! $sender->is($recipient)
            && User::inDirectory()->whereKey($recipient->id)->exists());

        // Applications (R1-09): five sends an hour from one address on the internet,
        // three a day for one e-mail address. Every send counts, duplicates included.
        RateLimiter::for('join', fn (Request $request) => [
            Limit::perHour(5)->by('join-ip|'.$request->ip()),
            Limit::perDay(3)->by('join-mail|'.sha1(Str::lower(trim((string) $request->input('email'))))),
        ]);

        // Messages between members (R1-13): per sender, and per sender and recipient.
        RateLimiter::for('member-contact', fn (Request $request) => [
            Limit::perHour(5)->by('contact-hour|'.$request->user()?->id),
            Limit::perDay(20)->by('contact-day|'.$request->user()?->id),
            Limit::perDay(3)->by('contact-pair|'.$request->user()?->id.'|'.$request->route('slug')),
        ]);

        // The activation link (R1-11): ten requests a minute from one internet address.
        RateLimiter::for('activation', fn (Request $request) => Limit::perMinute(10)->by('activation|'.$request->ip()));

        // "Forgot your password?": per internet address, and per address asked for, so that
        // nobody can fill a member's inbox with reset e-mails.
        RateLimiter::for('password-reset', fn (Request $request) => [
            Limit::perHour(10)->by('reset-ip|'.$request->ip()),
            Limit::perDay(5)->by('reset-mail|'.sha1(Str::lower(trim((string) $request->input('email'))))),
        ]);
    }
}
