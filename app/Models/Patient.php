<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\FollowUp;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

     protected $fillable = [
         'guid',
         'name',
         'address',
         'occupation',
         'mobile_phone',
         'remark',
         'gender',
         'birthdate',
         'email_id',
         'vishesh',
         'balance',
         'patient_id'
     ];

     protected $casts = [
        'birthdate' => 'date',
    ];


     protected static function boot()
     {
       parent::boot();

       static::creating(function ($model) {
         $model->guid = Str::uuid();
        //  $model->patient_id = Str::uuid();


        });
     }
    public function followUps()
    {
       return $this->hasMany(FollowUp::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
