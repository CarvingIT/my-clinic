<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preset extends Model
{
    protected $fillable = [
        'field_id', 'button_text', 'preset_text', 'display_order'
    ];

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
