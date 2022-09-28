<?php

namespace App\Models\v1\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class Slider extends Model
{
    use HasFactory, Fileable;

    public function registerFileable()
    {
        $this->fileableLoader([
            'image1' => 'banner',
            'image2' => 'banner',
            'image3' => 'banner',
            'image4' => 'banner',
            'image5' => 'banner',
            'image6' => 'banner',
            'image7' => 'banner',
            'image8' => 'banner',
            'image9' => 'banner',
            'image10' => 'banner',
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
