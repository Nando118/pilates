<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define("access-dashboard", function (User $user) {
            if ($user->hasRole("admin") || $user->hasRole("super_admin")) {
                return true;
            }
            return false;
        });

        Gate::define("super-admin", function (User $user) {
            if ($user->hasRole("super_admin")) {
                return true;
            }
            return false;
        });

        Gate::define("access-user-home", function (User $user) {
            if ($user->hasRole("client") || $user->hasRole("coach")) {
                return true;
            }
            return false;
        });

        Gate::define("access-coach-menu", function ($user) {
            return $user->hasRole("coach");
        });

        Gate::define("access-client-menu", function ($user) {
            return $user->hasRole("client");
        });
    }
}
