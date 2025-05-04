<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = ['patient_id', 'in_queue_at', 'added_by'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

