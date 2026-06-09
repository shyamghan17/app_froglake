<?php

namespace Workdo\OpticalAndEyeCareCenter\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OpticalDoctor extends Model
{
    use HasFactory;

    protected $table = 'hospital_doctors';

    protected $fillable = [
        'doctor_code',
        'license_number',
        'gender',
        'years_of_experience',
        'consultation_fee',
        'qualifications',
        'status',
        'user_id',
        'hospital_specialization_id',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'consultation_fee'    => 'decimal:2',
            'years_of_experience' => 'integer',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->doctor_code)) {
                $model->doctor_code = self::generateDoctorCode($model->created_by);
            }
        });
    }

    public static function generateDoctorCode($createdBy = null)
    {
        $createdBy = $createdBy ?: creatorId();
        if (!$createdBy) {
            return '#DOC0001';
        }

        $prefix     = company_setting('hospital_doctor_prefix', $createdBy) ?: '#DOC';
        $lastDoctor = self::where('created_by', $createdBy)
            ->where('doctor_code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastDoctor ? (int) substr($lastDoctor->doctor_code, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function eyeCareAppoinments()
    {
        return $this->hasMany(EyeCareAppoinment::class, 'doctor_id');
    }
}
