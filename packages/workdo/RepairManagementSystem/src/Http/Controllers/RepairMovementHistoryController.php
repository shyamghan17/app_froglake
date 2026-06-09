<?php

namespace Workdo\RepairManagementSystem\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\RepairManagementSystem\Models\RepairMovementHistory;
use Inertia\Inertia;
use Workdo\RepairManagementSystem\Models\RepairOrderRequest;

class RepairMovementHistoryController extends Controller
{
    public function index($id)
    {
        if (Auth::user()->can('view-history-repair-order-requests')) {
            $repairOrderRequest = RepairOrderRequest::where('id', $id)
                ->where(function($q) {
                    if(Auth::user()->can('manage-any-repair-order-requests')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-repair-order-requests')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->first();
            
            if (!$repairOrderRequest) {
                return redirect()->route('repair-management-system.repair-order-requests.index')
                    ->with('error', __('Repair order request not found or permission denied'));
            }
            
            $query = RepairMovementHistory::query()
                ->where('repair_order_request_id', $id)
                ->where('created_by', creatorId());

            // Apply search
            if (request('search')) {
                $searchTerm = request('search');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('movement_from', 'like', '%' . $searchTerm . '%')
                      ->orWhere('movement_to', 'like', '%' . $searchTerm . '%')
                      ->orWhere('movement_reason', 'like', '%' . $searchTerm . '%');
                });
            }

            // Apply sorting
            $sortField = request('sort', 'date_time');
            $sortDirection = request('direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            $movementHistories = $query->paginate(request('per_page', 10))
                ->withQueryString();

            // Transform data for frontend
            $movementHistories->getCollection()->transform(function($history) {
                return [
                    'id' => $history->id,
                    'movement_from' => $history->movement_from,
                    'movement_to' => $history->movement_to,
                    'movement_reason' => $history->movement_reason,
                    'date_time' => $history->date_time ? $history->date_time->format('Y-m-d H:i:s') : $history->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return Inertia::render('RepairManagementSystem/RepairMovementHistories/Index', [
                'movementHistories' => $movementHistories,
                'repairOrderRequest' => [
                    'id' => $repairOrderRequest->id,
                    'product_name' => $repairOrderRequest->product_name,
                ],
            ]);
        } else {
            return back()->with('error', __('Permission denied.'));
        }
    }
}