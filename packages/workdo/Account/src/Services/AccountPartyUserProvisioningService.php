<?php

namespace Workdo\Account\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AccountPartyUserProvisioningService
{
    public function createLoginDisabledUser(string $type, string $name, ?string $email = null, ?string $mobileNo = null): User
    {
        $user = new User();
        $user->name = $name;
        $user->email = $this->normalizeEmail($email, $name, $type);
        $user->mobile_no = $mobileNo;
        $user->password = null;
        $user->type = $type;
        $user->is_enable_login = 0;
        $user->email_verified_at = now();
        $user->lang = company_setting('defaultLanguage') ?? 'en';
        $user->creator_id = Auth::id();
        $user->created_by = creatorId();
        $user->save();

        $role = Role::query()
            ->where('name', $type)
            ->where('created_by', creatorId())
            ->where('guard_name', 'web')
            ->first();

        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }

    public function createLoginDisabledUserForTenant(
        string $type,
        string $name,
        int $tenantId,
        ?int $creatorId = null,
        ?string $email = null,
        ?string $mobileNo = null
    ): User {
        $user = new User();
        $user->name = $name;
        $user->email = $this->normalizeEmail($email, $name, $type);
        $user->mobile_no = $mobileNo;
        $user->password = null;
        $user->type = $type;
        $user->is_enable_login = 0;
        $user->email_verified_at = now();
        $user->lang = company_setting('defaultLanguage', $tenantId) ?? 'en';
        $user->creator_id = $creatorId ?? $tenantId;
        $user->created_by = $tenantId;
        $user->save();

        $role = Role::query()
            ->where('name', $type)
            ->where('created_by', $tenantId)
            ->where('guard_name', 'web')
            ->first();

        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }

    private function normalizeEmail(?string $email, string $name, string $type): string
    {
        if ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        $base = Str::slug($name);
        if ($base === '') {
            $base = $type;
        }

        $local = substr($base, 0, 40) . '.' . now()->timestamp . '.' . Str::lower(Str::random(6));

        return $local . '@example.invalid';
    }
}
