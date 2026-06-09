<?php

namespace Workdo\Rotas\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Rota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rotas_date',
        'start_time',
        'end_time',
        'break_time',
        'time_diff_in_minutes',
        'branch_id',
        'department_id',
        'designation_id',
        'shift_id',
        'type',
        'is_published',
        'notes',
        'issued_by',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'rotas_date' => 'date',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'is_published' => 'boolean',
            'break_time' => 'integer',
            'time_diff_in_minutes' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'user_id', 'user_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function getTotalWorkingHoursAttribute(): float
    {
        if ($this->type !== 'shift') {
            return 0;
        }

        // time_diff_in_minutes already has break_time subtracted in controller
        return round($this->time_diff_in_minutes / 60, 2);
    }

    public static function GivePermissionToRoles($role_id = null, $rolename = null)
    {
        $staff_permission = [
            'manage-rotas',
            'manage-own-rotas',
            'create-rotas',
            'manage-rotas-dashboard',
            'manage-rotas-work-schedules',
            'manage-own-rotas-work-schedules',
            'manage-rotas-availabilities',
            'manage-own-rotas-availabilities',
            'create-rotas-availabilities',
            'edit-rotas-availabilities',
            'delete-rotas-availabilities',
        ];

        if ($rolename == 'staff') {
            $roles_v = Role::where('name', 'staff')->where('id', $role_id)->first();
            foreach ($staff_permission as $permission_v) {
                $permission = Permission::where('name', $permission_v)->first();
                if (!empty($permission)) {
                    if (!$roles_v->hasPermissionTo($permission_v)) {
                        $roles_v->givePermissionTo($permission);
                    }
                }
            }
        }
    }
}
