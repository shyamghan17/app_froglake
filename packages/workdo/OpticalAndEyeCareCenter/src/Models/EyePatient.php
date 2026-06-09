<?php

namespace Workdo\OpticalAndEyeCareCenter\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class EyePatient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_name',
        'dob',
        'gender',
        'contact_no',
        'address',
        'medical_history',
        'previous_prescriptions',
        'preferred_doctor',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'dob' => 'date'
        ];
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'preferred_doctor');
    }
}
