<?php

namespace App\Models\v1\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class Slider extends Model
{
    use HasFactory, Fileable;

    protected $casts = [
        'link' => 'array',
        'list' => 'array',
    ];

    public function registerFileable()
    {
        $this->fileableLoader([
            'image' => 'banner'
        ]);
    }

    /**
     * Get the section that owns the Slider
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
