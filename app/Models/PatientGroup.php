<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the patients belonging to this group.
     */
    public function members()
    {
        return $this->hasMany(Patient::class, 'patient_group_id');
    }
}
