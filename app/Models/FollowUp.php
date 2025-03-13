<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'check_up_info',
        'diagnosis',
        'treatment',
        'amount_billed',
        'amount_paid',
    ];


    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function getPreviousDueAttribute()
    {
        // Get the last follow-up for the same patient
        $lastFollowUp = FollowUp::where('patient_id', $this->patient_id)
            ->where('id', '<', $this->id)
            ->latest()
            ->first();

        // If there was a previous follow-up, return its due amount
        return $lastFollowUp ? $lastFollowUp->total_due : 0;
    }

    // Caltulate total_due to include previous_due
    public function getTotalDueAttribute()
    {
        return ($this->amount_billed + $this->previous_due) - $this->amount_paid;
    }
}
