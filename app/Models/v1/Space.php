<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Space extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'size',
        'info',
        'price',
        'data',
        'max_occupants',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get all of the reservations for the Space
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get all of the the users who booked for the Space
     *
     * @return HasManyThrough
     */
    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Reservation::class);
    }

    /**
     *  Get the total number of reservations for this space.
     *
     * @return Attribute
     */
    public function totalOccupants(): Attribute
    {
        return new Attribute(
            get: fn () => $this->users()->count(),
        );
    }

    /**
     * Show number of available spots in this space
     *
     * @return Attribute
     */
    public function availableSpots(): Attribute
    {
        return new Attribute(
            get: fn () => $this->max_occupants - $this->total_occupants,
        );
    }

    /**
     * Get all reserved dates for this space.
     *
     * @return Attribute
     */
    public function reservedDates(): Attribute
    {

        return new Attribute(
            get: (function () {
                $dates = [];

                $reservations = $this->reservations()->get();

                foreach ($reservations as $reservation) {
                    $dates[] = [
                        'start_date' => $reservation->start_date,
                        'end_date' => $reservation->end_date,
                    ];
                }

                return $dates;
            }),
        );
    }
}