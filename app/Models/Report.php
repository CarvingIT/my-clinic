<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id','path','name'];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

}
