<?php

namespace App\Traits;

use CarvingIT\LaravelUserRoles\App\Models\Role;
use CarvingIT\LaravelUserRoles\App\Models\UserRole;

class LaravelUserRoles{
    public function role(){
            return $this->belongsTo(Role::class,'role_id');
        }
    
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }   

    public function assignRole($role_names){
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
                    //return false; 
                }
            }
        }
    }

    public function unassignRole($role_names){
        foreach($role_names as $role_name){
            $role = Role::where('name', $role_name)->first();
            UserRole::where('role_id', $role->id)->delete();
        }
    }
}
