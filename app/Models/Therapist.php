<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Therapist extends Model
{
    public function patients() {
        return $this->hasMany(Patient::class);
    }

    public function therapySessions(): HasMany {
        return $this->hasMany(TherapySession::class);
    }

}
