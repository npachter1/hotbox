<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(){
        
        $this->registerPolicies();


        // Super Admin Implicit Access - auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
        
        
        /* oauth api token scopes */
        Passport::tokensCan([
            'system-admin'      => 'System Administration',
            'api-partner'       => 'Relay data from client to 3rd party [Partner]'
        ]);
        
        
        
        
    }
    
}
