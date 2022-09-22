<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class GenericFormData extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'scan_date' => 'datetime',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'scan_date',
        'form_id',
        'data',
        'key',
    ];

    protected static function booted()
    {
        // static::creating(function ($item) {
        // });
    }

    /**
     * Get the form that owns the GenericFormData
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Route notifications for the mail channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForMail()
    {
        $email_field = $this->form->fields()->where('type', 'email')->first();
        $fname_field = $this->form->fields()->where('name', 'like', '%firstname%')->orWhere('name', 'like', '%first_name%')->first();
        $lname_field = $this->form->fields()->where('name', 'like', '%lastname%')->orWhere('name', 'like', '%last_name%')->first();
        $fullname_field = $this->form->fields()->where('name', 'like', '%fullname%')->orWhere('name', 'like', '%full_name%')->first();
        $name_field = $this->form->fields()->where('name', 'like', '%name%')->first();
        $name = collect([
            $fname_field ? $this->data[$fname_field->name] : '',
            $lname_field ? $this->data[$lname_field->name] : '',
            $fullname_field && !$fname_field && !$fname_field ? $this->data[$fullname_field->name] : '',
            $name_field && !$fname_field && !$fname_field && !$fullname_field ? $this->data[$name_field->name] : '',
        ])->filter(fn($name) => $name !=='')->implode(' ');

        // Return email address and name...
        return [$this->data[$email_field->name]??0 => $name];
    }

    /**
     * Route notifications for the twillio channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return array|string
     */
    public function routeNotificationForTwilio()
    {
        $phone_field = $this->form->fields()->where(function($query) {
            $query->where('type', 'like', '%phone%')
                ->orWhere('type', 'like', '%tel%')
                ->orWhere('type', 'like', '%mobile%')
                ->orWhere('type', 'like', '%number%')
                ->orWhere('type', 'like', '%cell%');
        })->where(function($query) {
            $query->where('name', 'like', '%phone%')
                ->orWhere('name', 'like', '%tel%')
                ->orWhere('name', 'like', '%mobile%')
                ->orWhere('name', 'like', '%number%')
                ->orWhere('name', 'like', '%cell%');
        })->first();
        return $this->data[$phone_field->name]??null;
    }

    // Load scans
    public function scans()
    {
        return $this->hasMany(ScanHistory::class, 'form_data_id', 'id');
    }
}