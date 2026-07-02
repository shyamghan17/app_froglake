<?php

namespace Workdo\PettyCashManagement\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\PettyCashManagement\Models\PettyCashAuditLog;

class PettyCashAuditLogController extends Controller
{
    public function index()
    {
        if (!Auth::user()->can('manage-petty-cash-expenses')) {
            return back()->with('error', __('Permission denied'));
        }

        $auditLogs = PettyCashAuditLog::query()
            ->with(['actor:id,name'])
            ->where('created_by', creatorId())
            ->when(request('action'), fn ($q) => $q->where('action', 'like', '%' . request('action') . '%'))
            ->when(request('subject_type'), fn ($q) => $q->where('subject_type', request('subject_type')))
            ->when(request('subject_id'), fn ($q) => $q->where('subject_id', request('subject_id')))
            ->latest('id')
            ->paginate(request('per_page', 10))
            ->withQueryString();

        $subjectTypes = PettyCashAuditLog::query()
            ->where('created_by', creatorId())
            ->whereNotNull('subject_type')
            ->distinct()
            ->orderBy('subject_type')
            ->pluck('subject_type')
            ->all();

        return Inertia::render('PettyCashManagement/AuditLogs/Index', [
            'auditLogs' => $auditLogs,
            'subjectTypes' => $subjectTypes,
            'filters' => [
                'action' => request('action', ''),
                'subject_type' => request('subject_type', ''),
                'subject_id' => request('subject_id', ''),
            ],
        ]);
    }
}

