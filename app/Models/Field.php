<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = [
        'name', 'category', 'display_order', 'extra_attributes'
    ];

    protected $casts = [
        'extra_attributes' => 'array',
    ];

    public function presets()
    {
        return $this->hasMany(Preset::class);
    }
}
