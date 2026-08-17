<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exemption extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'amount',
        'reason',
        'exempted_at',
    ];

    protected $casts = [
        'exempted_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
