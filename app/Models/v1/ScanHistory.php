<?php

namespace App\Models\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'qrcode',
        'form_id',
        'user_id',
    ];

    /**
     * Get the form that owns the GenericFormData
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }

    /**
     * Get the form that owns the GenericFormData
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function form_data(): BelongsTo
    {
        return $this->belongsTo(GenericFormData::class, 'form_data_id', 'id');
    }
}
