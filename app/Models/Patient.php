<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone_number',
        'birth_date',
        'address',
        'document',
        'user_id'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function getRouteKeyName()
    {
        return 'username';
    }
}
