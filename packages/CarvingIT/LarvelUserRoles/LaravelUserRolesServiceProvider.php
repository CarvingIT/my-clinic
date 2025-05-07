<?php
namespace CarvingIT\LaravelUserRoles;

class LaravelUserRolesServiceProvider extends ServiceProvider{
    public function boot(){
        $this->publishes([ __DIR__.'/database/migrations' => database_path('migrations')], 'user-roles-migrations');
        $this->publishes([ __DIR__.'/app/Traits' => app_path('Traits')], 'user-roles-models');
    }
    public function register(){
    }
}


