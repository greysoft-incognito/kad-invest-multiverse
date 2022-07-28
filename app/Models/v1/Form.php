<?php

namespace App\Models\v1;

use App\Services\Media;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'deadline' => 'datetime',
        'socials' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'banner_url',
        'logo_url'
    ];

    protected static function booted()
    {
        static::creating(function ($item) {
            $slug = str($item->title)->slug();
            $item->slug = (string) self::whereSlug($slug)->exists() ? $slug->append(rand()) : $slug;
        });

        static::saving(function ($item) {
            $item->banner = (new Media)->save('banner', 'banner', $item->banner);
            $item->logo = (new Media)->save('logo', 'logo', $item->logo);
        });

        static::deleted(function ($item) {
            (new Media)->delete('banner', $item->banner);
            (new Media)->delete('logo', $item->logo);
        });
    }

    /**
     * Get the URL to the fruit bay category's photo.
     *
     * @return string
     */
    protected function bannerUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => (new Media)->image('banner', $this->banner),
        );
    }

    /**
     * Get all of the data for the Form
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function data(): HasMany
    {
        return $this->hasMany(GenericFormData::class);
    }

    /**
     * Get all of the fields for the Form
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields(): HasMany
    {
        return $this->hasMany(GenericFormField::class);
    }

    /**
     * Get all of the infos for the Form
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function infos(): HasMany
    {
        return $this->hasMany(FormInfo::class)->orderBy('priority');
    }

    /**
     * Get the URL to the fruit bay category's photo.
     *
     * @return string
     */
    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => (new Media)->image('logo', $this->logo),
        );
    }

    public function socials(): Attribute
    {
        return new Attribute(
            get: fn ($value) => collect($value)->map(function($value, $key) {
                return [
                    'url' => $value,
                    'icon' => "fas fa-$key",
                    'label' => "@".str($value)->explode('/')->last(),
                ];
            })->toArray(),
        );
    }
}