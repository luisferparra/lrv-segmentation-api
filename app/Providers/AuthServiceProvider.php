<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Spatie\Permission\Models\Role;

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
    public function boot()
    {
       
        $role = []; 
     try {
        $role = Role::all();//user->getRoleNames(); 
     } catch (\Throwable $t) {

     }
        
        $roles = [];
        foreach ($role as $k => $v) {
            # code...
           
            $roles[$v->name] = $v->name;
        }
       
        Passport::tokensCan(
            $roles
        );

        $this->registerPolicies();

        Passport::routes();

        //
    }
}
