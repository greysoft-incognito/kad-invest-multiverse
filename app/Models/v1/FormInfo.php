<?php

namespace App\Models\v1;

use App\Services\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormInfo extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'list' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function ($item) {
            $item->image = (new Media)->save('default', 'image', $item->image);
        });

        static::deleted(function ($item) {
            (new Media)->delete('default', $item->image);
        });
    }

    /**
     * Get the URL to the fruit bay category's photo.
     *
     * @return string
     */
    protected function image_url(): Attribute
    {
        return Attribute::make(
            get: fn () => (new Media)->image('default', $this->image),
        );
    }
}