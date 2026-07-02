<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Workdo\Account\Models\Customer;
use Workdo\Account\Models\Vendor;
use Workdo\Account\Services\AccountPartyUserProvisioningService;

class LinkAccountPartyUsers extends Command
{
    protected $signature = 'account:link-party-users
        {--tenant= : Company user id (tenant scope via created_by)}
        {--only=both : customers|vendors|both}
        {--chunk=200 : Chunk size for processing}
        {--dry-run : Show counts without writing}';

    protected $description = 'Link Account customers/vendors with null user_id by creating login-disabled users (tenant scoped).';

    public function handle(): int
    {
        $only = strtolower((string) ($this->option('only') ?? 'both'));
        if (!in_array($only, ['customers', 'vendors', 'both'], true)) {
            $this->error('Invalid --only value. Allowed: customers, vendors, both.');
            return self::INVALID;
        }

        $chunkSize = max(1, (int) ($this->option('chunk') ?? 200));
        $tenantOption = $this->option('tenant');
        $tenantIds = $tenantOption ? [(int) $tenantOption] : $this->discoverTenantIds($only);

        if ($tenantOption && $tenantIds[0] <= 0) {
            $this->error('Invalid --tenant value.');
            return self::INVALID;
        }

        if (empty($tenantIds)) {
            $this->info('No tenants found with customers/vendors missing user_id.');
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');

        $grand = [
            'tenants' => count($tenantIds),
            'customers_linked' => 0,
            'vendors_linked' => 0,
            'users_created' => 0,
        ];

        foreach ($tenantIds as $tenantId) {
            if ($tenantId <= 0) {
                continue;
            }

            $this->line('Tenant: ' . $tenantId);

            if ($only === 'customers' || $only === 'both') {
                $result = $this->processCustomersForTenant($tenantId, $chunkSize, $dryRun);
                $grand['customers_linked'] += $result['linked'];
                $grand['users_created'] += $result['users_created'];
            }

            if ($only === 'vendors' || $only === 'both') {
                $result = $this->processVendorsForTenant($tenantId, $chunkSize, $dryRun);
                $grand['vendors_linked'] += $result['linked'];
                $grand['users_created'] += $result['users_created'];
            }
        }

        $this->newLine();
        $this->info('Done.');
        $this->line('Tenants: ' . $grand['tenants']);
        $this->line('Customers linked: ' . $grand['customers_linked']);
        $this->line('Vendors linked: ' . $grand['vendors_linked']);
        $this->line('Users created: ' . $grand['users_created']);

        return self::SUCCESS;
    }

    private function discoverTenantIds(string $only): array
    {
        $tenantIds = [];

        if ($only === 'customers' || $only === 'both') {
            $tenantIds = array_merge(
                $tenantIds,
                Customer::query()
                    ->whereNull('user_id')
                    ->whereNotNull('created_by')
                    ->distinct()
                    ->pluck('created_by')
                    ->map(fn ($v) => (int) $v)
                    ->all()
            );
        }

        if ($only === 'vendors' || $only === 'both') {
            $tenantIds = array_merge(
                $tenantIds,
                Vendor::query()
                    ->whereNull('user_id')
                    ->whereNotNull('created_by')
                    ->distinct()
                    ->pluck('created_by')
                    ->map(fn ($v) => (int) $v)
                    ->all()
            );
        }

        $tenantIds = array_values(array_unique(array_filter($tenantIds, fn (int $id) => $id > 0)));
        sort($tenantIds);

        return $tenantIds;
    }

    private function processCustomersForTenant(int $tenantId, int $chunkSize, bool $dryRun): array
    {
        $query = Customer::query()
            ->where('created_by', $tenantId)
            ->whereNull('user_id')
            ->orderBy('id');

        $missing = (int) (clone $query)->count();

        if ($dryRun) {
            $this->line('Customers missing user_id: ' . $missing);
            return ['linked' => 0, 'users_created' => 0];
        }

        $this->line('Customers missing user_id: ' . $missing);

        $linked = 0;
        $usersCreated = 0;

        $query->chunkById($chunkSize, function ($customers) use ($tenantId, &$linked, &$usersCreated) {
            foreach ($customers as $customer) {
                DB::transaction(function () use ($tenantId, $customer, &$linked, &$usersCreated) {
                    $locked = Customer::query()->whereKey($customer->id)->lockForUpdate()->first();
                    if (!$locked || $locked->user_id) {
                        return;
                    }

                    $creatorId = $locked->creator_id ?: $tenantId;

                    $user = app(AccountPartyUserProvisioningService::class)->createLoginDisabledUserForTenant(
                        'client',
                        $locked->company_name,
                        $tenantId,
                        $creatorId,
                        $locked->contact_person_email ?: null,
                        $locked->contact_person_mobile ?: null
                    );

                    $locked->user_id = $user->id;

                    if (empty($locked->contact_person_email)) {
                        $locked->contact_person_email = $user->email;
                    }

                    $locked->save();

                    $linked++;
                    $usersCreated++;
                });
            }
        });

        $this->line('Customers linked: ' . $linked);

        return ['linked' => $linked, 'users_created' => $usersCreated];
    }

    private function processVendorsForTenant(int $tenantId, int $chunkSize, bool $dryRun): array
    {
        $query = Vendor::query()
            ->where('created_by', $tenantId)
            ->whereNull('user_id')
            ->orderBy('id');

        $missing = (int) (clone $query)->count();

        if ($dryRun) {
            $this->line('Vendors missing user_id: ' . $missing);
            return ['linked' => 0, 'users_created' => 0];
        }

        $this->line('Vendors missing user_id: ' . $missing);

        $linked = 0;
        $usersCreated = 0;

        $query->chunkById($chunkSize, function ($vendors) use ($tenantId, &$linked, &$usersCreated) {
            foreach ($vendors as $vendor) {
                DB::transaction(function () use ($tenantId, $vendor, &$linked, &$usersCreated) {
                    $locked = Vendor::query()->whereKey($vendor->id)->lockForUpdate()->first();
                    if (!$locked || $locked->user_id) {
                        return;
                    }

                    $creatorId = $locked->creator_id ?: $tenantId;

                    $user = app(AccountPartyUserProvisioningService::class)->createLoginDisabledUserForTenant(
                        'vendor',
                        $locked->company_name,
                        $tenantId,
                        $creatorId,
                        $locked->contact_person_email ?: null,
                        $locked->contact_person_mobile ?: null
                    );

                    $locked->user_id = $user->id;
                    $locked->save();

                    $linked++;
                    $usersCreated++;
                });
            }
        });

        $this->line('Vendors linked: ' . $linked);

        return ['linked' => $linked, 'users_created' => $usersCreated];
    }
}

