<?php

namespace App\Models\v1;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Permissions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Permissions, Fileable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'phone',
        'password',
        'firstname',
        'lastname',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'privileges' => 'array',
    ];

    public function registerFileable()
    {
        $this->fileableLoader('image', 'avatar');
    }

    /**
     * Get the URL to the fruit bay category's photo.
     *
     * @return string
     */
    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->images['image'],
        );
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => collect([
                $this->firstname,
                $this->lastname,
            ])->filter()->implode(' '),
        );
    }

    /**
     * Get all of the scan_history for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scan_history(): HasMany
    {
        return $this->hasMany(ScanHistory::class);
    }

    /**
     * Get all of the USER's Reservations.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all of the USER's TRANSACTIONS.
     */
    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactable');
    }
}