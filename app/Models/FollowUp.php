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
   ];

  public function patient()
  {
      return $this->belongsTo(Patient::class);
  }
}
