<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * UM agendamento pode ter VÁRIAS sessões
     */
    public function therapySessions(): HasMany
    {
        return $this->hasMany(TherapySession::class);
    }
}
