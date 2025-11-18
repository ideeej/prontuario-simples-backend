<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'birth_date',
        'address'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * UM paciente participa de VÁRIAS sessões (Many-to-Many)
     */
    public function therapySessions(): BelongsToMany
    {
        return $this->belongsToMany(TherapySession::class)
                    ->withPivot('role')  // Inclui o campo 'role' da tabela pivot
                    ->withTimestamps();  // Inclui created_at/updated_at da pivot
    }
}
