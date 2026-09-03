<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'image_urls',
    ];

    protected $casts = [
        'image_urls' => 'array',
    ];

    protected static function booted(): void
    {
        // Every gallery needs a slug for its public share link. Generating it
        // here (rather than in the controller) means it's impossible to create
        // a gallery without one, no matter which code path creates it.
        static::creating(function (Gallery $gallery) {
            if (empty($gallery->slug)) {
                $gallery->slug = Str::random(12);
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
