<?php

namespace Workdo\Rotas\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date_of_birth',
        'gender',
        'shift',
        'date_of_joining',
        'employment_type',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_number',
        'bank_name',
        'account_holder_name',
        'account_number',
        'bank_identifier_code',
        'bank_branch',
        'tax_payer_id',
        'basic_salary',
        'hours_per_day',
        'days_per_week',
        'rate_per_hour',
        'user_id',
        'branch_id',
        'department_id',
        'designation_id',
        'creator_id',
        'created_by',
        'work_schedule',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_of_joining' => 'date',
            'work_schedule' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function shifts()
    {
        return $this->belongsTo(Shift::class, 'shift', 'id');
    }

    public function rotas()
    {
        return $this->hasMany(Rota::class, 'user_id', 'user_id');
    }

    public function availability()
    {
        return $this->hasMany(RotasAvailability::class, 'employee_id', 'id');
    }

    public static function generateEmployeeId()
    {
        $prefix = 'EMP';
        $year = date('Y');
        $lastEmployee = self::where('employee_id', 'like', $prefix.$year.'%')
            ->where('created_by', creatorId())
            ->orderBy('employee_id', 'desc')
            ->first();

        if ($lastEmployee) {
            $lastNumber = (int) substr($lastEmployee->employee_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix.$year.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}