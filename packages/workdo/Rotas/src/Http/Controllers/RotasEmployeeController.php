<?php

namespace Workdo\Rotas\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\Rotas\Events\CreateEmployee;
use Workdo\Rotas\Events\DestroyEmployee;
use Workdo\Rotas\Events\UpdateEmployee;
use Workdo\Rotas\Http\Requests\StoreRotasEmployeeRequest;
use Workdo\Rotas\Http\Requests\UpdateRotasEmployeeRequest;
use Workdo\Rotas\Models\Branch;
use Workdo\Rotas\Models\Department;
use Workdo\Rotas\Models\Designation;
use Workdo\Rotas\Models\Employee;
use Workdo\Rotas\Models\EmployeeDocument;
use Workdo\Rotas\Models\EmployeeDocumentType;
use Workdo\Rotas\Models\Shift;

class RotasEmployeeController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage-rotas-employees')) {
            $employees = Employee::query()
                ->with(['user:id,name,avatar', 'branch', 'department', 'designation', 'shifts'])
                ->where(function ($q) {
                    if (Auth::user()->can('manage-any-employees')) {
                        $q->where('created_by', creatorId());
                    } elseif (Auth::user()->can('manage-own-employees')) {
                        $q->where('creator_id', Auth::id())->orWhere('user_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('employee_id'), function ($q) {
                    $q->where(function ($query) {
                        $query->where('employee_id', 'like', '%'.request('employee_id').'%');
                        $query->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('name', 'like', '%'.request('employee_id').'%');
                        });
                    });
                })
                ->when(request('branch_id') && request('branch_id') !== 'all', fn ($q) => $q->where('branch_id', request('branch_id')))
                ->when(request('department_id') && request('department_id') !== 'all', fn ($q) => $q->where('department_id', request('department_id')))
                ->when(request('employment_type') !== null && request('employment_type') !== '', fn ($q) => $q->where('employment_type', request('employment_type')))
                ->when(request('gender') !== null && request('gender') !== '', fn ($q) => $q->where('gender', request('gender')))
                ->when(request('sort'), fn ($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn ($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('Rotas/Employees/Index', [
                'employees' => $employees,
                'users' => User::emp()->where('created_by', creatorId())->select('id', 'name')->get(),
                'branches' => Branch::where('created_by', creatorId())->select('id', 'branch_name')->get(),
                'departments' => Department::where('created_by', creatorId())->select('id', 'department_name', 'branch_id')->get(),
                'designations' => Designation::where('created_by', creatorId())->select('id', 'designation_name', 'branch_id', 'department_id')->get(),
                'shifts' => Shift::where('created_by', creatorId())->select('id', 'shift_name')->get(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function create()
    {
        if (Auth::user()->can('create-rotas-employees')) {
            return Inertia::render('Rotas/Employees/Create', [
                'users' => User::emp()->where('created_by', creatorId())->whereNotIn('id', Employee::where('created_by', creatorId())->whereNotNull('user_id')->pluck('user_id'))->select('id', 'name')->get(),
                'branches' => Branch::where('created_by', creatorId())->select('id', 'branch_name')->get(),
                'departments' => Department::where('created_by', creatorId())->select('id', 'department_name', 'branch_id')->get(),
                'designations' => Designation::where('created_by', creatorId())->select('id', 'designation_name', 'branch_id', 'department_id')->get(),
                'shifts' => Shift::where('created_by', creatorId())->select('id', 'shift_name')->get(),
                'documentTypes' => EmployeeDocumentType::where('created_by', creatorId())->select('id', 'document_name', 'is_required')->get(),
                'generatedEmployeeId' => Employee::generateEmployeeId(),
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreRotasEmployeeRequest $request)
    {
        if (Auth::user()->can('create-rotas-employees')) {
            $validated = $request->validated();
            $employee = new Employee;
            $employee->employee_id = $validated['employee_id'];
            $employee->date_of_birth = $validated['date_of_birth'];
            $employee->gender = $validated['gender'];
            $employee->shift = $validated['shift_id'];
            $employee->date_of_joining = $validated['date_of_joining'];
            $employee->employment_type = $validated['employment_type'];
            $employee->address_line_1 = $validated['address_line_1'];
            $employee->address_line_2 = $validated['address_line_2'];
            $employee->city = $validated['city'];
            $employee->state = $validated['state'];
            $employee->country = $validated['country'];
            $employee->postal_code = $validated['postal_code'];
            $employee->emergency_contact_name = $validated['emergency_contact_name'];
            $employee->emergency_contact_relationship = $validated['emergency_contact_relationship'];
            $employee->emergency_contact_number = $validated['emergency_contact_number'];
            $employee->bank_name = $validated['bank_name'];
            $employee->account_holder_name = $validated['account_holder_name'];
            $employee->account_number = $validated['account_number'];
            $employee->bank_identifier_code = $validated['bank_identifier_code'];
            $employee->bank_branch = $validated['bank_branch'];
            $employee->tax_payer_id = $validated['tax_payer_id'];
            $employee->basic_salary = $validated['basic_salary'];
            $employee->hours_per_day = $validated['hours_per_day'];
            $employee->days_per_week = $validated['days_per_week'];
            $employee->rate_per_hour = $validated['rate_per_hour'];
            $employee->user_id = $validated['user_id'];
            $employee->branch_id = $validated['branch_id'];
            $employee->department_id = $validated['department_id'];
            $employee->designation_id = $validated['designation_id'];

            $employee->creator_id = Auth::id();
            $employee->created_by = creatorId();
            $employee->save();

            CreateEmployee::dispatch($request, $employee);

            // Store documents
            if ($request->has('documents')) {
                foreach ($request->input('documents', []) as $index => $document) {
                    if ($request->hasFile("documents.{$index}.file") && ! empty($document['document_type_id'])) {
                        $file = $request->file("documents.{$index}.file");

                        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();
                        $fileNameToStore = $filename.'_'.time().'.'.$extension;

                        $upload = upload_file($request, "documents.{$index}.file", $fileNameToStore, 'employee_documents');

                        if (isset($upload['flag']) && $upload['flag'] == 1 && isset($upload['url'])) {
                            EmployeeDocument::create([
                                'user_id' => $employee->id,
                                'document_type_id' => $document['document_type_id'],
                                'file_path' => $upload['url'],
                                'creator_id' => Auth::id(),
                                'created_by' => creatorId(),
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('rotas.employees.index')->with('success', __('The employee has been created successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function edit(Employee $employee)
    {
        if (Auth::user()->can('edit-rotas-employees')) {
            if ($employee->created_by != creatorId()) {
                return back()->with('error', __('Permission denied'));
            }

            $existingDocuments = EmployeeDocument::where('created_by', creatorId())
                ->where('user_id', $employee->id)
                ->with('documentType')
                ->get()
                ->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'document_type_id' => $doc->document_type_id,
                        'file_path' => $doc->file_path,
                        'document_name' => $doc->documentType->document_name ?? '',
                    ];
                });

            return Inertia::render('Rotas/Employees/Edit', [
                'employee' => $employee->load('user:id,name'),
                'users' => User::emp()->where('created_by', creatorId())->select('id', 'name')->get(),
                'branches' => Branch::where('created_by', creatorId())->select('id', 'branch_name')->get(),
                'departments' => Department::where('created_by', creatorId())->select('id', 'department_name', 'branch_id')->get(),
                'designations' => Designation::where('created_by', creatorId())->select('id', 'designation_name', 'branch_id', 'department_id')->get(),
                'shifts' => Shift::where('created_by', creatorId())->select('id', 'shift_name')->get(),
                'documentTypes' => EmployeeDocumentType::where('created_by', creatorId())->select('id', 'document_name', 'is_required')->get(),
                'existingDocuments' => $existingDocuments,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateRotasEmployeeRequest $request, Employee $employee)
    {
        if (Auth::user()->can('edit-rotas-employees')) {
            if ($employee->created_by != creatorId()) {
                return back()->with('error', __('Permission denied'));
            }

            $validated = $request->validated();

            // Allow user_id update only if not already set
            if (! $employee->user_id && isset($validated['user_id'])) {
                $employee->user_id = $validated['user_id'];
            }

            $employee->date_of_birth = $validated['date_of_birth'];
            $employee->gender = $validated['gender'];
            $employee->shift = $validated['shift_id'];
            $employee->date_of_joining = $validated['date_of_joining'];
            $employee->employment_type = $validated['employment_type'];
            $employee->address_line_1 = $validated['address_line_1'];
            $employee->address_line_2 = $validated['address_line_2'];
            $employee->city = $validated['city'];
            $employee->state = $validated['state'];
            $employee->country = $validated['country'];
            $employee->postal_code = $validated['postal_code'];
            $employee->emergency_contact_name = $validated['emergency_contact_name'];
            $employee->emergency_contact_relationship = $validated['emergency_contact_relationship'];
            $employee->emergency_contact_number = $validated['emergency_contact_number'];
            $employee->bank_name = $validated['bank_name'];
            $employee->account_holder_name = $validated['account_holder_name'];
            $employee->account_number = $validated['account_number'];
            $employee->bank_identifier_code = $validated['bank_identifier_code'];
            $employee->bank_branch = $validated['bank_branch'];
            $employee->tax_payer_id = $validated['tax_payer_id'];
            $employee->basic_salary = $validated['basic_salary'];
            $employee->hours_per_day = $validated['hours_per_day'];
            $employee->days_per_week = $validated['days_per_week'];
            $employee->rate_per_hour = $validated['rate_per_hour'];
            $employee->branch_id = $validated['branch_id'];
            $employee->department_id = $validated['department_id'];
            $employee->designation_id = $validated['designation_id'];

            $employee->save();

            UpdateEmployee::dispatch($request, $employee);

            // Handle document updates
            if ($request->has('documents')) {
                foreach ($request->input('documents', []) as $index => $document) {
                    if ($request->hasFile("documents.{$index}.file") && ! empty($document['document_type_id'])) {
                        $file = $request->file("documents.{$index}.file");

                        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $extension = $file->getClientOriginalExtension();
                        $fileNameToStore = $filename.'_'.time().'.'.$extension;

                        $upload = upload_file($request, "documents.{$index}.file", $fileNameToStore, 'employee_documents');

                        if (isset($upload['flag']) && $upload['flag'] == 1 && isset($upload['url'])) {
                            EmployeeDocument::create([
                                'user_id' => $employee->id,
                                'document_type_id' => $document['document_type_id'],
                                'file_path' => $upload['url'],
                                'creator_id' => Auth::id(),
                                'created_by' => creatorId(),
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('rotas.employees.index')->with('success', __('The employee details are updated successfully.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function destroy(Employee $employee)
    {
        if (Auth::user()->can('delete-rotas-employees')) {
            if ($employee->created_by != creatorId()) {
                return back()->with('error', __('Permission denied'));
            }

            DestroyEmployee::dispatch($employee);
            $employee->delete();

            return back()->with('success', __('The employee has been deleted.'));
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function show(Employee $employee)
    {
        if (Auth::user()->can('view-rotas-employees')) {
            if ($employee->created_by != creatorId()) {
                return back()->with('error', __('Permission denied'));
            }

            $employee->load(['user:id,name,email,avatar', 'branch', 'department', 'designation', 'shifts']);

            $documents = EmployeeDocument::where('user_id', $employee->id)
                ->with('documentType')
                ->get()
                ->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'document_type_id' => $doc->document_type_id,
                        'file_path' => $doc->file_path,
                        'document_name' => $doc->documentType->document_name ?? '',
                    ];
                });

            return Inertia::render('Rotas/Employees/Show', [
                'employee' => $employee,
                'documents' => $documents,
            ]);
        } else {
            return back()->with('error', __('Permission denied'));
        }
    }

    public function deleteDocument($employeeId, EmployeeDocument $document)
    {
        if (Auth::user()->can('edit-rotas-employees')) {
            if ($document->user_id != $employeeId) {
                return redirect()->back()->with('error', __('Document not found'));
            }

            delete_file($document->file_path);
            $document->delete();

            return redirect()->back()->with('success', __('Document deleted successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }
}
