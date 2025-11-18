<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapyReport extends Model
{
    protected $fillable = ['medicalRecord_id', 'content'];

    public function therapyReport() {
        return $this->belongsTo(TherapyRecord::class);
    }

}
