<?php

namespace Workdo\OpticalAndEyeCareCenter\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;

class EyeCareAppoinment extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_name',
        'appointment_datetime',
        'status',
        'appointment_type',
        'notes',
        'patient_id',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'appointment_datetime' => 'datetime'
        ];
    }

    public function patient()
    {
        return $this->belongsTo(EyePatient::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_name');
    }
}
