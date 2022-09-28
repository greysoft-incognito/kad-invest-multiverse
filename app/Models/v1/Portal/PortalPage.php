<?php

namespace App\Models\v1\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class PortalPage extends Model
{
    use HasFactory, Fileable;

    protected $casts = [
        'index' => 'boolean',
        'in_footer' => 'boolean',
        'in_navbar' => 'boolean',
    ];

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('slug', $value)
            ->firstOrFail();
    }

    public function registerFileable()
    {
        $this->fileableLoader([
            'image' => 'banner',
        ]);
    }

    /**
     * Get the portal that owns the PortalPage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function portal(): BelongsTo
    {
        return $this->belongsTo(Portal::class);
    }

    /**
     * Get all of the sections for the PortalPage
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
