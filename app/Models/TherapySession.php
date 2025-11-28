<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TherapySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * UMA sessão percente a um paciente
     */
    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class);
    }

    /**
     * UMA sessão tem UM agendamento
     */
    public function appointment(): HasOne
    {
        return $this->hasOne(Appointment::class);
    }

    /**
     * UMA sessão tem UMA cobrança
     */
    public function charge(): HasOne
    {
        return $this->hasOne(Charge::class);
    }
}
