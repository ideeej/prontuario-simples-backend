<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TherapySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'therapy_record',
        'appointment_id',
        'charge_id'
    ];

    /**
     * UMA sessão tem VÁRIOS pacientes (Many-to-Many)
     */
    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class)
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * UMA sessão pertence a UM agendamento
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * UMA sessão pertence a UMA cobrança
     */
    public function charge(): BelongsTo
    {
        return $this->belongsTo(Charge::class);
    }

    /**
     * UMA sessão tem VÁRIOS relatórios
     */
    public function therapyReports(): HasMany
    {
        return $this->hasMany(TherapyReport::class);
    }
}
