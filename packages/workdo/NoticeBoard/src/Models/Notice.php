<?php

namespace Workdo\NoticeBoard\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Workdo\NoticeBoard\Models\NoticeRead;

class Notice extends Model
{
    use HasFactory;

    protected $appends = ['target_ids'];

    protected $fillable = [
        'title',
        'description',
        'attachments',
        'start_date',
        'expiry_date',
        'is_pinned',
        'priority',
        'require_acknowledgment',
        'target_type',
        'allow_comments',
        'status',
        'creator_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'attachments'            => 'array',
            'start_date'             => 'date',
            'expiry_date'            => 'date',
            'is_pinned'              => 'boolean',
            'require_acknowledgment' => 'boolean',
            'allow_comments'         => 'boolean',
        ];
    }

    public function getTargetIdsAttribute(): array
    {
        $columnMap = [
            'department'     => 'department_id',
            'role'           => 'role_id',
            'specific_users' => 'user_id',
        ];

        $column = $columnMap[$this->target_type] ?? null;

        if (!$column) {
            return [];
        }

        return $this->targets()->whereNotNull($column)->pluck($column)->toArray();
    }

    public function targets(): HasMany
    {
        return $this->hasMany(NoticeTarget::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(NoticeRead::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(NoticeComment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public static function GivePermissionToRoles($role_id = null, $rolename = null)
    {
        $staff_permissions = [
            'manage-notice-board',
            'delete-own-notices-comments',
        ];

        if ($rolename == 'staff') {
            $role = Role::where('name', 'staff')->where('id', $role_id)->first();
            if ($role) {
                foreach ($staff_permissions as $perm) {
                    $permission = Permission::where('name', $perm)->first();
                    if ($permission && !$role->hasPermissionTo($perm)) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }
    }

    public function scopeVisibleTo($query, $user = null)
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        $query->where('created_by', creatorId())
            ->where('status', 'published')
            ->where('start_date', '<=', today())
            ->where(fn($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', today()));

        // manage-any-notices sees all published+active company notices
        if ($user->can('manage-any-notices')) {
            return $query;
        }

        $userRoleIds  = $user->roles->pluck('id')->toArray();
        $departmentId = null;

        if (Module_is_active('Hrm')) {
            $departmentId = \Workdo\Hrm\Models\Employee::where('user_id', $user->id)->value('department_id');
        }

        return $query->where(function ($q) use ($user, $userRoleIds, $departmentId) {

            // manage-own-notices sees own created notices
            if ($user->can('manage-own-notices')) {
                $q->orWhere('creator_id', $user->id);
            }

            // Target-based visibility
            $q->orWhere(function ($q) use ($user, $userRoleIds, $departmentId) {
                $q->where('target_type', 'all')
                    ->orWhere(function ($q) use ($user) {
                        $q->where('target_type', 'specific_users')
                            ->whereHas('targets', fn($t) => $t->where('user_id', $user->id));
                    })
                    ->orWhere(function ($q) use ($userRoleIds) {
                        $q->where('target_type', 'role')
                            ->whereHas('targets', fn($t) => $t->whereIn('role_id', $userRoleIds));
                    });

                if ($departmentId) {
                    $q->orWhere(function ($q) use ($departmentId) {
                        $q->where('target_type', 'department')
                            ->whereHas('targets', fn($t) => $t->where('department_id', $departmentId));
                    });
                }
            });
        });
    }
}
