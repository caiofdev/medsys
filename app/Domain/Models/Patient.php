<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'gender',
        'phone',
        'birth_date',
        'emergency_contact',
        'medical_history',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
