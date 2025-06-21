<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\FollowUp;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guid',
        'name',
        'address',
        'occupation',
        'mobile_phone',
        'remark',
        'gender',
        'birthdate',
        'email_id',
        'vishesh',
        'balance',
        'patient_id',
        'height',
        'weight',
        'occupation',
        'reference',
        'created_at',
        'updated_at',

    ];

    protected $casts = [
        'birthdate' => 'date',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->guid = Str::uuid();
            //  $model->patient_id = Str::uuid();


        });
    }
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class, 'patient_id');
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // Queue
    public function queues()
    {
        return $this->hasMany(Queue::class);
    }

    /**
     * Get the latest follow-up timestamp for the patient.
     *
     * @return \Illuminate\Support\Carbon|null
     */
    public function getLatestFollowUpAtAttribute()
    {
        return $this->followUps()->latest('created_at')->value('created_at');
    }

    /**
     * Scope to sort patients by their latest follow-up time.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrderByLatestFollowUp($query)
    {
        return $query->select('patients.*')
            ->leftJoinSub(
                FollowUp::select('patient_id', \DB::raw('MAX(created_at) as last_follow_up'))
                    ->groupBy('patient_id'),
                'latest_follow_up',
                'patients.id',
                '=',
                'latest_follow_up.patient_id'
            )
            ->orderByRaw('COALESCE(latest_follow_up.last_follow_up, patients.created_at) DESC')
            ->orderBy('patients.created_at', 'desc');
    }
}
