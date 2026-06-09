<?php

namespace Workdo\EBilling\Http\Controllers\Api;

use App\Traits\ApiResponseTrait;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\EBilling\Models\EBillingItem;

class EBillingItemApiController extends Controller
{
    use ApiResponseTrait;

    private function canAccessItem(EBillingItem $item): bool
    {
        if ($item->created_by !== creatorId()) {
            return false;
        }

        if (Auth::user()->can('manage-any-ebilling')) {
            return true;
        }

        if (Auth::user()->can('manage-own-ebilling')) {
            return (int) $item->creator_id === (int) Auth::id();
        }

        return false;
    }

    public function index()
    {
        if (!Auth::user()->can('manage-ebilling')) {
            return $this->errorResponse(__('Permission denied'), null, 403);
        }

        $items = EBillingItem::query()
            ->select('id', 'name', 'description', 'is_active', 'created_at')
            ->where(function ($q) {
                if (Auth::user()->can('manage-any-ebilling')) {
                    $q->where('created_by', creatorId());
                    return;
                }

                if (Auth::user()->can('manage-own-ebilling')) {
                    $q->where('created_by', creatorId())->where('creator_id', Auth::id());
                    return;
                }

                $q->whereRaw('1 = 0');
            })
            ->latest()
            ->paginate(request('per_page', 10));

        return $this->paginatedResponse($items, __('Fetched successfully'));
    }

    public function store()
    {
        if (!Auth::user()->can('create-ebilling')) {
            return $this->errorResponse(__('Permission denied'), null, 403);
        }

        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors(), __('Validation error'));
        }

        $validated = $validator->validated();

        $item = new EBillingItem();
        $item->name = $validated['name'];
        $item->description = $validated['description'] ?? null;
        $item->is_active = (bool) ($validated['is_active'] ?? true);
        $item->creator_id = Auth::id();
        $item->created_by = creatorId();
        $item->save();

        return $this->successResponse($item->only(['id', 'name', 'description', 'is_active', 'created_at']), __('Created successfully'));
    }

    public function update(EBillingItem $item)
    {
        if (!Auth::user()->can('edit-ebilling')) {
            return $this->errorResponse(__('Permission denied'), null, 403);
        }

        if (!$this->canAccessItem($item)) {
            return $this->errorResponse(__('Permission denied'), null, 403);
        }

        $validator = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors(), __('Validation error'));
        }

        $validated = $validator->validated();

        $item->name = $validated['name'];
        $item->description = $validated['description'] ?? null;
        $item->is_active = (bool) ($validated['is_active'] ?? $item->is_active);
        $item->save();

        return $this->successResponse($item->only(['id', 'name', 'description', 'is_active', 'created_at']), __('Updated successfully'));
    }

    public function destroy(EBillingItem $item)
    {
        if (!Auth::user()->can('delete-ebilling')) {
            return $this->errorResponse(__('Permission denied'), null, 403);
        }

        if (!$this->canAccessItem($item)) {
            return $this->errorResponse(__('Permission denied'), null, 403);
        }

        $item->delete();

        return $this->successResponse(null, __('Deleted successfully'));
    }
}

