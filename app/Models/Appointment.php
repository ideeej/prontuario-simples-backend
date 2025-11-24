<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_id',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    public function therapySessions(): HasMany
    {
        return $this->hasMany(TherapySession::class);
    }
}
