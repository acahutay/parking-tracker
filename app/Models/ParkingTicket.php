<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ParkingTicket extends Model
{
    protected $fillable = [
        'parking_section_id',
        'plate_number',
        'card_number',
        'checked_in_at',
        'checked_out_at',
        'is_active',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ParkingSection::class, 'parking_section_id');
    }
}
