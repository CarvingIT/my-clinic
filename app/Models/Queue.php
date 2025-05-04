<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    protected $fillable = ['patient_id', 'in_queue_at', 'added_by'];

    protected $casts = [
        'in_queue_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}

