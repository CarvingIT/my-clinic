<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;

    protected $table = 'uploads';

    protected $fillable = [
        'patient_id',
        'follow_up_id',
        'photo_type',
        'file_path',
    ];

    // Define the relationship with the Patient model
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Define the relationship with the FollowUp model
    public function followUp()
    {
        return $this->belongsTo(FollowUp::class);
    }
}
