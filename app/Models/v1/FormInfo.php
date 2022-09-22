<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class FormInfo extends Model
{
    use HasFactory, Fileable;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'list' => 'array',
    ];

    public function registerFileable()
    {
        $this->fileableLoader('image', 'default');
    }

    /**
     * Get the URL to the fruit bay category's photo.
     *
     * @return string
     */
    protected function image_url(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->images['image'],
        );
    }
}