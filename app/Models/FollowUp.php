<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'check_up_info',
        'diagnosis',
        'treatment',
        'amount_billed',
        // 'patient_photos',
        'created_at',
        'updated_at',
    ];


    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(){
        return $this->belongsTo(User::class, 'doctor_id');
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

    protected static $allocatedPaidCache = [];

    public function getAmountPaidAttribute()
    {
        if (isset(self::$allocatedPaidCache[$this->id])) {
            return self::$allocatedPaidCache[$this->id];
        }

        $patientId = $this->patient_id;
        
        $chronoFollowUps = FollowUp::where('patient_id', $patientId)->orderBy('created_at', 'asc')->get();
        $allPayments = Payment::where('patient_id', $patientId)->where('status', 'posted')->orderBy('paid_at', 'asc')->orderBy('id', 'asc')->get();
        
        $paymentPool = $allPayments->map(function($p) {
            return [
                'model' => $p,
                'remaining' => (float)$p->amount,
            ];
        })->toArray();

        foreach ($chronoFollowUps as $fu) {
            $billed = (float)($fu->amount_billed ?? 0);
            $allocatedForFu = 0.0;
            
            // First, try to allocate payments that are EXPLICITLY linked to this follow-up
            foreach ($paymentPool as &$pItem) {
                if ($pItem['remaining'] > 0 && $pItem['model']->follow_up_id == $fu->id) {
                    $alloc = min($pItem['remaining'], $billed - $allocatedForFu);
                    if ($alloc > 0) {
                        $pItem['remaining'] -= $alloc;
                        $allocatedForFu += $alloc;
                    }
                }
            }
            unset($pItem);
            
            self::$allocatedPaidCache[$fu->id] = $allocatedForFu;
        }

        return self::$allocatedPaidCache[$this->id] ?? 0.0;
    }

    // Caltulate total_due to include previous_due
    public function getTotalDueAttribute()
    {
        return ($this->amount_billed + $this->previous_due) - $this->amount_paid;
    }

    public function getPaymentMethodAttribute()
    {
        $paymentMethods = $this->payments->pluck('payment_method')->filter()->unique()->map(function($method) {
            return ucfirst($method);
        })->implode(', ');

        if (empty($paymentMethods)) {
            $paymentMethods = Payment::where('patient_id', $this->patient_id)
                ->whereNull('follow_up_id')
                ->where('status', 'posted')
                ->pluck('payment_method')
                ->filter()
                ->unique()
                ->map(function($method) {
                    return ucfirst($method);
                })->implode(', ');
        }

        if (empty($paymentMethods)) {
            $checkUpInfo = json_decode($this->check_up_info, true);
            return $checkUpInfo['payment_method'] ?? 'N/A';
        }

        return $paymentMethods;
    }

    // Define the relationship with the Upload model
    public function uploads()
    {
        return $this->hasMany(Upload::class, 'follow_up_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'follow_up_id');
    }
}
