<?php

namespace App\Providers;

use App\Auth\Permissions;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
    }
}
