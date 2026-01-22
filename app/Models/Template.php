<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'content',
        'placeholders',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'placeholders' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Find a template by its slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Render the template by replacing placeholders with actual values.
     *
     * @param array $data Key-value pairs for placeholder replacement
     * @return string The rendered content
     */
    public function render(array $data): string
    {
        $content = $this->content;

        foreach ($data as $key => $value) {
            $content = str_replace('{' . $key . '}', $value ?? '', $content);
        }

        return $content;
    }
}
