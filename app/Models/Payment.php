<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'follow_up_id',
        'received_by',
        'amount',
        'payment_method',
        'paid_at',
        'status',
        'reference_no',
        'notes',
        'branch_id',
        'branch_name',
        'source',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function followUp()
    {
        return $this->belongsTo(FollowUp::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
