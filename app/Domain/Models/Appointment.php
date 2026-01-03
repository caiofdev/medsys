<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_date',
        'status',
        'value',
        'doctor_id',
        'patient_id',
        'receptionist_id',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function receptionist()
    {
        return $this->belongsTo(Receptionist::class);
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class);
    }
}
