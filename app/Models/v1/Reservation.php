<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'space_id',
        'user_id',
        'start_date',
        'end_date',
    ];

    /**
     * Get the space that owns the Reservation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }

    /**
     * Get the user that owns the Reservation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     *  Get the duration of this reservation.
     *
     * @return Attribute
     */
    public function duration(): Attribute
    {
        return new Attribute(
            get: fn () => $this->start_date->diffInDays($this->end_date),
        );
    }

    /**
     * Get the total price for this reservation.
     *
     * @return Attribute
     */
    public function cost(): Attribute
    {
        return new Attribute(
            get: fn () => $this->space->price * $this->getDuration(),
        );
    }
}