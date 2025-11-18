<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapyRecord extends Model
{
     protected $fillable = ['patient_id', 'therapy_record_content'];

     public function patient() {
        return $this->belongsTo(Patient::class);
     }

     public function therapyReports() {
        return $this->hasMany(TherapyReport::class);
     }
}
