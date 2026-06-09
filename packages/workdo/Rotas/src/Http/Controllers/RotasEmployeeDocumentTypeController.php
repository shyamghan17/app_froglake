<?php

namespace Workdo\Rotas\Http\Controllers;

use Workdo\Rotas\Models\EmployeeDocumentType;
use Workdo\Rotas\Http\Requests\StoreRotasEmployeeDocumentTypeRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasEmployeeDocumentTypeRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class RotasEmployeeDocumentTypeController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-rotas-employee-document-types')) {
            $employeedocumenttypes = EmployeeDocumentType::query()
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-rotas-employee-document-types')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-rotas-employee-document-types')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->latest()
                ->get();

            return Inertia::render('Rotas/SystemSetup/EmployeeDocumentTypes/Index', [
                'employeedocumenttypes' => $employeedocumenttypes,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasEmployeeDocumentTypeRequest $request)
    {
        if (Auth::user()->can('create-rotas-employee-document-types')) {
            $validated = $request->validated();

            $validated['is_required'] = $request->boolean('is_required', false);

            $employeedocumenttype = new EmployeeDocumentType();
            $employeedocumenttype->document_name = $validated['document_name'];
            $employeedocumenttype->description = $validated['description'];
            $employeedocumenttype->is_required = $validated['is_required'];

            $employeedocumenttype->creator_id = Auth::id();
            $employeedocumenttype->created_by = creatorId();
            $employeedocumenttype->save();

            return back()->with('success', __('The document type has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasEmployeeDocumentTypeRequest $request, EmployeeDocumentType $employeeDocumentType)
    {
        if (Auth::user()->can('edit-rotas-employee-document-types')) {
            $validated = $request->validated();

            $validated['is_required'] = $request->boolean('is_required', false);

            $employeeDocumentType->document_name = $validated['document_name'];
            $employeeDocumentType->description = $validated['description'];
            $employeeDocumentType->is_required = $validated['is_required'];

            $employeeDocumentType->save();

            return back()->with('success', __('The document type has been updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(EmployeeDocumentType $employeeDocumentType)
    {
        if (Auth::user()->can('delete-rotas-employee-document-types')) {
            $employeeDocumentType->delete();

            return back()->with('success', __('The document type has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }
}
