<?php

namespace Workdo\OpticalAndEyeCareCenter\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\OpticalAndEyeCareCenter\Models\EyePatient;

class EyeTestPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_name',
        'test_date',
        'test_results',
        'prescription_details',
        'prescription_expiry_date',
        'notes',
        'patient_id',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'test_date' => 'date',
            'prescription_expiry_date' => 'date'
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
