<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TherapySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'charge_id',
        'notes'];

    /**
     * UMA sessão tem VÁRIOS pacientes (Many-to-Many)
     */
    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class)
            ->withTimestamps();
    }

    /**
     * UMA sessão pertence a UM agendamento
     */
    public function appointments(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * UMA sessão pertence a UMA cobrança
     */
    public function charges(): BelongsTo
    {
        return $this->belongsTo(Charge::class);
    }
}
