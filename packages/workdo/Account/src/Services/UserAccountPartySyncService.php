<?php

namespace Workdo\Account\Services;

use App\Models\User;
use Workdo\Account\Models\Customer;
use Workdo\Account\Models\Vendor;

class UserAccountPartySyncService
{
    public function syncForUser(User $user): void
    {
        if ($user->type === 'client') {
            $this->createCustomerFromUser($user);
        }

        if ($user->type === 'vendor') {
            $this->createVendorFromUser($user);
        }
    }

    public function syncCustomersForTenant(int $tenantId): void
    {
        $linkedUserIds = Customer::where('created_by', $tenantId)
            ->whereNotNull('user_id')
            ->pluck('user_id');

        User::where('type', 'client')
            ->where('created_by', $tenantId)
            ->whereNotIn('id', $linkedUserIds)
            ->get()
            ->each(fn (User $user) => $this->createCustomerFromUser($user));
    }

    public function syncVendorsForTenant(int $tenantId): void
    {
        $linkedUserIds = Vendor::where('created_by', $tenantId)
            ->whereNotNull('user_id')
            ->pluck('user_id');

        User::where('type', 'vendor')
            ->where('created_by', $tenantId)
            ->whereNotIn('id', $linkedUserIds)
            ->get()
            ->each(fn (User $user) => $this->createVendorFromUser($user));
    }

    private function createCustomerFromUser(User $user): void
    {
        Customer::firstOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $user->name,
                'contact_person_name' => $user->name,
                'contact_person_email' => $user->email ?? '',
                'contact_person_mobile' => $user->mobile_no,
                'same_as_billing' => false,
                'creator_id' => $user->creator_id,
                'created_by' => $user->created_by,
            ]
        );
    }

    private function createVendorFromUser(User $user): void
    {
        Vendor::firstOrCreate(
            ['user_id' => $user->id],
            [
                'company_name' => $user->name,
                'contact_person_name' => $user->name,
                'contact_person_email' => $user->email,
                'contact_person_mobile' => $user->mobile_no,
                'same_as_billing' => false,
                'creator_id' => $user->creator_id,
                'created_by' => $user->created_by,
            ]
        );
    }
}
