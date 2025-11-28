<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapy_session_id',
        'name',
        'username',
        'email',
        'phone_number',
        'birth_date',
        'address',
        'document',
        'notes'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function therapy_sessions(): BelongsToMany
    {
        return $this->belongsToMany(related: TherapySession::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(Charge::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
