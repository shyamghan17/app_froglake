<?php

namespace Workdo\Account\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Workdo\Account\Models\Customer;
use Workdo\Account\Models\Vendor;

class AccountPartyUserOptionsService
{
    public function customerUsers(int $tenantId, array $columns = ['id', 'name', 'email'], ?string $search = null, bool $includeAccountDetails = false): Collection
    {
        app(UserAccountPartySyncService::class)->syncCustomersForTenant($tenantId);

        $columns = $this->normalizeColumns($columns);

        $users = User::query()
            ->select($columns)
            ->where('created_by', $tenantId)
            ->whereIn('id', Customer::query()->where('created_by', $tenantId)->whereNotNull('user_id')->select('user_id'))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->get();

        if (!$includeAccountDetails || $users->isEmpty()) {
            return $users;
        }

        return $this->attachCustomerAccountDetails($users, $tenantId);
    }

    public function customerUsersByIds(int $tenantId, array $userIds, array $columns = ['id', 'name', 'email'], bool $includeAccountDetails = false): Collection
    {
        app(UserAccountPartySyncService::class)->syncCustomersForTenant($tenantId);

        $columns = $this->normalizeColumns($columns);
        $userIds = array_values(array_unique(array_filter($userIds)));

        if (empty($userIds)) {
            return collect();
        }

        $users = User::query()
            ->select($columns)
            ->where('created_by', $tenantId)
            ->whereIn('id', $userIds)
            ->whereIn('id', Customer::query()->where('created_by', $tenantId)->whereNotNull('user_id')->select('user_id'))
            ->orderBy('name')
            ->get();

        if (!$includeAccountDetails || $users->isEmpty()) {
            return $users;
        }

        return $this->attachCustomerAccountDetails($users, $tenantId);
    }

    public function vendorUsers(int $tenantId, array $columns = ['id', 'name', 'email'], ?string $search = null, bool $includeAccountDetails = false): Collection
    {
        app(UserAccountPartySyncService::class)->syncVendorsForTenant($tenantId);

        $columns = $this->normalizeColumns($columns);

        $users = User::query()
            ->select($columns)
            ->where('created_by', $tenantId)
            ->whereIn('id', Vendor::query()->where('created_by', $tenantId)->whereNotNull('user_id')->select('user_id'))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->get();

        if (!$includeAccountDetails || $users->isEmpty()) {
            return $users;
        }

        return $this->attachVendorAccountDetails($users, $tenantId);
    }

    public function vendorUsersByIds(int $tenantId, array $userIds, array $columns = ['id', 'name', 'email'], bool $includeAccountDetails = false): Collection
    {
        app(UserAccountPartySyncService::class)->syncVendorsForTenant($tenantId);

        $columns = $this->normalizeColumns($columns);
        $userIds = array_values(array_unique(array_filter($userIds)));

        if (empty($userIds)) {
            return collect();
        }

        $users = User::query()
            ->select($columns)
            ->where('created_by', $tenantId)
            ->whereIn('id', $userIds)
            ->whereIn('id', Vendor::query()->where('created_by', $tenantId)->whereNotNull('user_id')->select('user_id'))
            ->orderBy('name')
            ->get();

        if (!$includeAccountDetails || $users->isEmpty()) {
            return $users;
        }

        return $this->attachVendorAccountDetails($users, $tenantId);
    }

    private function attachCustomerAccountDetails(Collection $users, int $tenantId): Collection
    {
        $detailsByUserId = Customer::query()
            ->select(['user_id', 'company_name', 'tax_number', 'payment_terms'])
            ->where('created_by', $tenantId)
            ->whereIn('user_id', $users->pluck('id')->all())
            ->get()
            ->keyBy('user_id');

        return $users->each(function (User $user) use ($detailsByUserId) {
            $details = $detailsByUserId->get($user->id);
            $user->setAttribute('company_name', $details?->company_name);
            $user->setAttribute('tax_number', $details?->tax_number);
            $user->setAttribute('payment_terms', $details?->payment_terms);
        });
    }

    private function attachVendorAccountDetails(Collection $users, int $tenantId): Collection
    {
        $detailsByUserId = Vendor::query()
            ->select(['user_id', 'company_name', 'tax_number', 'payment_terms'])
            ->where('created_by', $tenantId)
            ->whereIn('user_id', $users->pluck('id')->all())
            ->get()
            ->keyBy('user_id');

        return $users->each(function (User $user) use ($detailsByUserId) {
            $details = $detailsByUserId->get($user->id);
            $user->setAttribute('company_name', $details?->company_name);
            $user->setAttribute('tax_number', $details?->tax_number);
            $user->setAttribute('payment_terms', $details?->payment_terms);
        });
    }

    private function normalizeColumns(array $columns): array
    {
        $allowed = [
            'id',
            'name',
            'email',
            'mobile_no',
            'avatar',
            'is_disable',
            'type',
        ];

        $columns = array_values(array_unique(array_intersect($columns, $allowed)));

        if (!in_array('id', $columns, true)) {
            array_unshift($columns, 'id');
        }

        if ($columns === ['id']) {
            $columns[] = 'name';
        }

        return $columns;
    }
}
