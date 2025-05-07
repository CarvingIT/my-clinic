<?php

namespace CarvingIT\LaravelUserRoles\App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';

    public function role(){
            return $this->belongsTo(Role::class,'role_id');
        }
    
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }   
}
