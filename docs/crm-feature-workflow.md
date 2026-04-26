# CRM Feature Workflow Blueprint

## Purpose
Use this blueprint to add CRM features in `packages/workdo/Lead` without breaking existing behavior.

## Scope Guardrails
- Keep CRM feature code inside `packages/workdo/Lead/*`.
- Do not change unrelated core flows while adding one feature.
- Preserve tenant boundaries (`created_by`, `creator_id`, `creatorId()` checks).
- Preserve permission checks on every protected action.

## Where To Add What
- Routes: `packages/workdo/Lead/src/Routes/web.php` (and `api.php` if API needed)
- Controller: `packages/workdo/Lead/src/Http/Controllers/*`
- Request validation: `packages/workdo/Lead/src/Http/Requests/*`
- Models: `packages/workdo/Lead/src/Models/*`
- Migrations: `packages/workdo/Lead/src/Database/Migrations/*`
- Permission seed: `packages/workdo/Lead/src/Database/Seeders/PermissionTableSeeder.php`
- Frontend pages: `packages/workdo/Lead/src/Resources/js/Pages/*`
- Menu entries: `packages/workdo/Lead/src/Resources/js/menus/company-menu.ts`

## Standard Implementation Flow
1. Create migration and model updates (if schema needed).
2. Add permission names and seeder mapping.
3. Add FormRequest validation.
4. Add controller actions with permission + tenant scoping.
5. Add routes.
6. Add frontend page/components.
7. Add menu item (if needed), permission-gated.
8. Verify role behavior (company/staff/client).
9. Run deployment-readiness checks.

## Template: Permission Entries
Add in CRM permission seeder:

```php
[
    'name' => 'manage-crm-feature-x',
    'guard_name' => 'web',
    'module' => 'Lead',
]
```

Suggested permission set pattern:
- `manage-feature-x`
- `create-feature-x`
- `edit-feature-x`
- `delete-feature-x`
- `view-feature-x`

## Template: Route (Web)
Add in `packages/workdo/Lead/src/Routes/web.php` inside CRM middleware group.

```php
Route::prefix('crm/feature-x')->name('lead.feature-x.')->group(function () {
    Route::get('/', [FeatureXController::class, 'index'])->name('index');
    Route::post('/', [FeatureXController::class, 'store'])->name('store');
    Route::put('/{featureX}', [FeatureXController::class, 'update'])->name('update');
    Route::delete('/{featureX}', [FeatureXController::class, 'destroy'])->name('destroy');
});
```

## Template: FormRequest
Create `StoreFeatureXRequest` and `UpdateFeatureXRequest`.

```php
<?php

namespace Workdo\Lead\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeatureXRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
```

## Template: Controller (Permission + Tenant Safe)

```php
<?php

namespace Workdo\Lead\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Lead\Http\Requests\StoreFeatureXRequest;
use Workdo\Lead\Http\Requests\UpdateFeatureXRequest;
use Workdo\Lead\Models\FeatureX;

class FeatureXController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage-feature-x')) {
            return back()->with('error', __('Permission denied'));
        }

        $rows = FeatureX::where(function ($q) {
            if (Auth::user()->can('manage-any-feature-x')) {
                $q->where('created_by', creatorId());
            } elseif (Auth::user()->can('manage-own-feature-x')) {
                $q->where('creator_id', Auth::id());
            } else {
                $q->whereRaw('1 = 0');
            }
        })->latest()->get();

        return Inertia::render('Lead/FeatureX/Index', [
            'rows' => $rows,
        ]);
    }

    public function store(StoreFeatureXRequest $request)
    {
        if (!Auth::user()->can('create-feature-x')) {
            return back()->with('error', __('Permission denied'));
        }

        $data = $request->validated();
        $row = new FeatureX();
        $row->name = $data['name'];
        $row->creator_id = Auth::id();
        $row->created_by = creatorId();
        $row->save();

        return back()->with('success', __('Created successfully'));
    }

    public function update(UpdateFeatureXRequest $request, FeatureX $featureX)
    {
        if (!Auth::user()->can('edit-feature-x')) {
            return back()->with('error', __('Permission denied'));
        }

        if ((int)$featureX->created_by !== (int)creatorId()) {
            return back()->with('error', __('Not found'));
        }

        $featureX->fill($request->validated());
        $featureX->save();

        return back()->with('success', __('Updated successfully'));
    }

    public function destroy(FeatureX $featureX)
    {
        if (!Auth::user()->can('delete-feature-x')) {
            return back()->with('error', __('Permission denied'));
        }

        if ((int)$featureX->created_by !== (int)creatorId()) {
            return back()->with('error', __('Not found'));
        }

        $featureX->delete();
        return back()->with('success', __('Deleted successfully'));
    }
}
```

## Template: Query Patterns (Safe Defaults)
- Always filter tenant scope:

```php
->where('created_by', creatorId())
```

- For own-vs-any access:

```php
->where(function ($q) {
    if (Auth::user()->can('manage-any-...')) {
        $q->where('created_by', creatorId());
    } elseif (Auth::user()->can('manage-own-...')) {
        $q->where('creator_id', Auth::id());
    } else {
        $q->whereRaw('1 = 0');
    }
})
```

- Validate membership for relation-driven access (like `user_leads` / `user_deals`).

## Template: Frontend Page (Inertia + Permission-aware UI)
Create page at `Lead/FeatureX/Index.tsx`:

```tsx
import { Head, usePage, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/layouts/authenticated-layout';
import { Button } from '@/components/ui/button';

export default function Index() {
  const { rows, auth } = usePage().props as any;

  return (
    <AuthenticatedLayout
      breadcrumbs={[
        { label: 'CRM', url: route('lead.index') },
        { label: 'Feature X' },
      ]}
      pageTitle="Feature X"
      pageActions={
        auth?.user?.permissions?.includes('create-feature-x') ? (
          <Button onClick={() => router.post(route('lead.feature-x.store'), { name: 'New' })}>
            Create
          </Button>
        ) : null
      }
    >
      <Head title="Feature X" />
      {/* render table/list safely */}
    </AuthenticatedLayout>
  );
}
```

## Template: Menu Registration
Add under CRM children in `company-menu.ts`:

```ts
{
  title: t('Feature X'),
  href: route('lead.feature-x.index'),
  permission: 'manage-feature-x',
}
```

## Event/Activity Logging Pattern
When feature affects timeline-sensitive entities (Lead/Deal), append activity logs consistently:

```php
LeadActivityLog::create([
    'user_id' => Auth::id(),
    'lead_id' => $lead->id,
    'log_type' => 'Feature X Updated',
    'remark' => json_encode(['title' => $lead->name]),
]);
```

## Regression-Safe Checklist
- Permissions added and seeded.
- Route names follow `lead.*` naming conventions.
- Controller actions enforce `can(...)`.
- Queries include tenant boundaries.
- No cross-tenant data in responses.
- UI actions hidden when permission missing.
- Both list and kanban flows still work for Leads/Deals.
- Lead-to-deal conversion still works unchanged.

## Deployment Readiness (GitHub -> cPanel)
- Confirm changed files are package-scoped unless explicitly cross-cutting.
- Confirm no secrets or local paths added.
- Confirm build artifacts strategy is respected (`public/build` committed when needed).
- Ask before running build commands.

## Suggested Feature Branch Naming
- `feature/crm-feature-x`
- `fix/crm-feature-x-permission`
- `refactor/crm-feature-x-query-scope` (only when explicitly approved)
