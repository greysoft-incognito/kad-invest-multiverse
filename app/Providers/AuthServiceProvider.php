<?php

namespace App\Providers;

use App\Models\v1\User as V1User;
use App\Traits\Permissions;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    use Permissions;

    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('usable', function (V1User $user, $permission) {
            return ($check = $this->setPermissionsUser($user)->checkPermissions($permission)) === true
                ? Response::allow()
                : Response::deny($check);
        });

        Gate::define('can-do', function (V1User $user, $permission, $item = null) {
            return ($check = $this->setPermissionsUser($user)->checkPermissions($permission)) === true
                ? Response::allow()
                : Response::deny($check);
        });
    }
}
