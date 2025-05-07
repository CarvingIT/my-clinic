<?php

namespace CarvingIT\LaravelUserRoles\App\Traits;

use CarvingIT\LaravelUserRoles\App\Models\Role;
use CarvingIT\LaravelUserRoles\App\Models\UserRole;

trait LaravelUserRoles{
    public function user_roles(){
            return $this->hasMany(UserRole::class);
    }

    public function roles(){
        $user_roles = $this->user_roles;
         return $user_roles->map(function($user_role){
                return $user_role->role;
         });
    }

    public function hasRole($role_name){
        $roles = $this->roles();
        foreach($roles as $r){
            if($r->name == $role_name) return true;
        }
        return false;
    }
    
    public function assignRoles($role_names){
        foreach($role_names as $role_name){
            $role = Role::where('name', $role_name)->first();
            if($role && !empty($this->id)){
                $user_role = new UserRole;
                $user_role->user_id = $this->id;
                $user_role->role_id = $role->id;
                try{
                    $user_role->save();
                }
                catch(\Exception $e){
                    // some error like violation of unique constraint
                    echo $e->getMessage();
                    return false; 
                }
            }
        }
    }

    public function unassignRoles($role_names){
        foreach($role_names as $role_name){
            $role = Role::where('name', $role_name)->first();
            UserRole::where('role_id', $role->id)->delete();
        }
    }
}
