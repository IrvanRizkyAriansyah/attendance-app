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
        //
        Gate::define('akses-superadmin', function (User $user) {
        return $user->role === 'admin';
        });

        Gate::define('akses-manager', function (User $user) {
            return $user->role === 'manager';
        });

        Gate::define('akses-karyawan', function (User $user) {
            return $user->role === 'karyawan';
        });

        Gate::define('akses-keuangan', function (User $user) {
            return $user->role === 'keuangan';
        });

    }
}
