<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmployeeDocument;
use App\Models\EmployeeCertification;
use App\Models\PayrollRecord;
use App\Exports\SalaryRegisterExport;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class HrController extends Controller
{
    public function index()
    {
        $stats = [
            'total'        => Employee::count(),
            'active'       => Employee::where('is_active', true)->count(),
            'teaching'     => Employee::where('employee_type', 'teaching')->where('is_active', true)->count(),
            'non_teaching' => Employee::where('employee_type', 'non_teaching')->where('is_active', true)->count(),
            'driver'       => Employee::where('employee_type', 'driver')->where('is_active', true)->count(),
            'cleaner'      => Employee::where('employee_type', 'cleaner')->where('is_active', true)->count(),
            'nanny'        => Employee::whereIn('employee_type', ['nanny', 'naani'])->where('is_active', true)->count(),
        ];

        // Pending leave requests
        $pendingLeaves = DB::table('leave_requests')
            ->where('status', 'pending')->count();

        // Today's staff attendance (table may not be migrated yet)
        try {
            $todayPresent = DB::table('staff_attendances')
                ->whereDate('date', today())->whereIn('status', ['present', 'late'])->count();
            $todayAbsent = DB::table('staff_attendances')
                ->whereDate('date', today())->where('status', 'absent')->count();
        } catch (\Exception $e) {
            $todayPresent = 0;
            $todayAbsent  = 0;
        }

        // Current month payroll status
        $currentMonth = now()->format('Y-m');
        $payrollProcessed = PayrollRecord::where('month', $currentMonth)->count();
        $payrollTotal = Employee::where('is_active', true)->count();

        // Recent hires (last 30 days)
        $recentHires = Employee::where('joining_date', '>=', now()->subDays(30)->toDateString())
            ->orderByDesc('joining_date')->limit(5)->get();

        return view('hr.index', compact(
            'stats', 'pendingLeaves',
            'todayPresent', 'todayAbsent',
            'payrollProcessed', 'payrollTotal', 'currentMonth',
            'recentHires'
        ));
    }

    public function employees(Request $request)
    {
        $categoryCounts = [
            'total'        => Employee::count(),
            'teaching'     => Employee::where('employee_type', 'teaching')->count(),
            'non_teaching' => Employee::where('employee_type', 'non_teaching')->count(),
            'driver'       => Employee::where('employee_type', 'driver')->count(),
            'cleaner'      => Employee::where('employee_type', 'cleaner')->count(),
            'nanny'        => Employee::whereIn('employee_type', ['nanny', 'naani'])->count(),
        ];

        $typeFilter = $request->get('type');

        $employees = Employee::when($request->search, fn($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('first_name', 'like', "%$v%")
                  ->orWhere('last_name', 'like', "%$v%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$v%"])
                  ->orWhere('employee_code', 'like', "%$v%")
                  ->orWhere('mobile', 'like', "%$v%")
                  ->orWhere('designation', 'like', "%$v%")
                  ->orWhere('department', 'like', "%$v%");
            }))
            ->when($request->department, fn($q, $v) => $q->where('department', $v))
            ->when($typeFilter && $typeFilter !== 'all', function ($q) use ($typeFilter) {
                if (in_array($typeFilter, ['nanny', 'naani'])) {
                    $q->whereIn('employee_type', ['nanny', 'naani']);
                } else {
                    $q->where('employee_type', $typeFilter);
                }
            })
            ->when(!$request->show_all, fn($q) => $q->where('is_active', true))
            ->orderBy('id')->paginate(36)->withQueryString();

        $departments = Employee::select('department')->distinct()->pluck('department')->filter();

        return view('hr.employees', compact('employees', 'departments', 'categoryCounts', 'typeFilter'));
    }

    public function createEmployee()
    {
        return view('hr.employees-create');
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'first_name'             => 'required|string|max:60',
            'last_name'              => 'required|string|max:60',
            'dob'                    => 'nullable|date|before:today',
            'gender'                 => 'required|in:male,female,other',
            'mobile'                 => 'required|string|max:15',
            'official_email'         => 'nullable|email|max:100',
            'email'                  => 'nullable|email|max:100',
            'designation'            => 'required|string|max:100',
            'department'             => 'nullable|string|max:100',
            'employee_type'          => 'required|string|in:teaching,non_teaching,driver,cleaner,nanny,naani,contract,part_time',
            'joining_date'           => 'required|date',
            'qualification'          => 'nullable|string|max:200',
            'address'                => 'nullable|string|max:500',
            'residential_address'    => 'nullable|string|max:500',
            'pf_account_no'          => 'nullable|string|max:30',
            'esi_no'                 => 'nullable|string|max:30',
            'pan_no'                 => 'nullable|string|max:15',
            'aadhaar_no'             => 'nullable|string|max:15',
            'photo'                  => 'nullable|image|max:2048',
            'basic_salary'           => 'nullable|numeric|min:0',
            'hra'                    => 'nullable|numeric|min:0',
            'da'                     => 'nullable|numeric|min:0',
            'ta'                     => 'nullable|numeric|min:0',
            'medical_allowance'      => 'nullable|numeric|min:0',
            'other_allowances'       => 'nullable|numeric|min:0',
            'tds'                    => 'nullable|numeric|min:0',
            // Category specific fields
            'license_number'         => 'nullable|string|max:100',
            'license_expiry'         => 'nullable|date',
            'assigned_vehicle'       => 'nullable|string|max:100',
            'assigned_block'         => 'nullable|string|max:100',
            'shift_timing'           => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_mobile'=> 'nullable|string|max:15',
        ]);

        $salaryFields = ['hra', 'da', 'ta', 'medical_allowance', 'other_allowances', 'tds'];
        $salaryData = array_filter(
            array_intersect_key($validated, array_flip($salaryFields)),
            fn($v) => $v !== null
        );

        $data = array_diff_key($validated, array_flip($salaryFields));
        unset($data['photo']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employee-photos', 'public');
        }

        // Fill email aliases
        if (!empty($data['official_email']) && empty($data['email'])) {
            $data['email'] = $data['official_email'];
        }

        $employee = Employee::create(array_merge($data, [
            'employee_code' => $this->generateEmployeeNumber(),
            'is_active'     => true,
        ]));

        // Create salary structure if any salary data was provided
        if ($employee->basic_salary > 0 || count($salaryData) > 0) {
            DB::table('salary_structures')->insert(array_merge([
                'employee_id'    => $employee->id,
                'basic_salary'   => $employee->basic_salary ?? 0,
                'effective_from' => $employee->joining_date ?? today(),
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ], $salaryData));
        }

        // Auto-assign Spatie role based on designation
        if ($employee->designation_id && $employee->user_id) {
            $designation = Designation::find($employee->designation_id);
            if ($designation?->spatie_role && $employee->user) {
                $employee->user->assignRole($designation->spatie_role);
            }
        }

        return redirect()->route('hr.employees', ['type' => $employee->employee_type])
            ->with('success', 'Employee ' . $employee->full_name . ' (' . $employee->category_label . ') added successfully!');
    }

    public function showEmployee(int $id)
    {
        $employee        = Employee::findOrFail($id);
        $linkedUser      = $employee->user_id ? \App\Models\User::find($employee->user_id) : null;
        $staffRoles      = Role::pluck('name');
        $payroll         = PayrollRecord::where('employee_id', $id)->latest('month')->take(6)->get();
        $qualifications  = \App\Models\EmployeeQualification::where('employee_id', $id)->orderByDesc('year_of_passing')->get();
        $experiences     = \App\Models\EmployeeExperience::where('employee_id', $id)->orderByDesc('from_date')->get();
        $empDocuments    = EmployeeDocument::where('employee_id', $id)->orderBy('document_type')->get();
        $certifications  = EmployeeCertification::where('employee_id', $id)->orderByDesc('issue_date')->get();
        return view('hr.employees-show', compact('employee', 'linkedUser', 'staffRoles', 'payroll', 'qualifications', 'experiences', 'empDocuments', 'certifications'));
    }

    public function singleEmployeeIdCard(int $id)
    {
        $employee = Employee::findOrFail($id);
        $school   = \App\Models\SchoolSetting::first();
        return view('hr.employee-id-card-studio', compact('employee', 'school'));
    }

    public function updateEmployeePhoto(Request $request, int $id)
    {
        $employee = Employee::findOrFail($id);
        $request->validate([
            'photo' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($employee->photo && Storage::disk('public')->exists($employee->photo)) {
                Storage::disk('public')->delete($employee->photo);
            }
            $photoPath = $request->file('photo')->store('employee-photos', 'public');
            $employee->update(['photo' => $photoPath]);
        }

        return redirect()->back()->with('success', 'Employee photo updated successfully on ID card!');
    }

    public function uploadEmployeeDocument(Request $request, int $id)
    {
        $employee = Employee::findOrFail($id);
        $request->validate([
            'document_type' => 'required|string',
            'document_name' => 'required|string|max:150',
            'file'          => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);
        $path = $request->file('file')->store('employee-documents/' . $id, 'local');
        EmployeeDocument::create([
            'employee_id'       => $id,
            'document_type'     => $request->document_type,
            'document_name'     => $request->document_name,
            'file_path'         => $path,
            'file_original_name'=> $request->file('file')->getClientOriginalName(),
            'notes'             => $request->notes,
        ]);
        return back()->with('success', 'Document uploaded.');
    }

    public function downloadEmployeeDocument(int $employeeId, int $docId)
    {
        $doc = EmployeeDocument::where('employee_id', $employeeId)->findOrFail($docId);
        abort_unless(Storage::disk('local')->exists($doc->file_path), 404);
        return Storage::disk('local')->download($doc->file_path, $doc->file_original_name ?? basename($doc->file_path));
    }

    public function previewEmployeeDocument(int $employeeId, int $docId)
    {
        $doc = EmployeeDocument::where('employee_id', $employeeId)->findOrFail($docId);
        abort_unless(Storage::disk('local')->exists($doc->file_path), 404);
        return response()->file(Storage::disk('local')->path($doc->file_path));
    }

    public function deleteEmployeeDocument(int $employeeId, int $docId)
    {
        $doc = EmployeeDocument::where('employee_id', $employeeId)->findOrFail($docId);
        Storage::disk('local')->delete($doc->file_path);
        $doc->delete();
        return back()->with('success', 'Document deleted.');
    }

    public function verifyEmployeeDocument(Request $request, int $employeeId, int $docId)
    {
        $doc = EmployeeDocument::where('employee_id', $employeeId)->findOrFail($docId);
        $doc->update([
            'verification_status' => $request->status,
            'verified_by'         => Auth::id(),
            'verified_at'         => now(),
            'notes'               => $request->notes ?? $doc->notes,
        ]);
        return back()->with('success', 'Document status updated.');
    }

    public function storeCertification(Request $request, int $id)
    {
        Employee::findOrFail($id);
        $request->validate([
            'certification_name' => 'required|string|max:200',
            'issuing_authority'  => 'nullable|string|max:200',
            'issue_date'         => 'nullable|date',
            'certificate_number' => 'nullable|string|max:100',
            'file'               => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('employee-certifications/' . $id, 'local');
        }
        EmployeeCertification::create([
            'employee_id'        => $id,
            'certification_name' => $request->certification_name,
            'issuing_authority'  => $request->issuing_authority,
            'issue_date'         => $request->issue_date,
            'certificate_number' => $request->certificate_number,
            'file_path'          => $filePath,
        ]);
        return back()->with('success', 'Certification added successfully.');
    }

    public function downloadCertification(int $employeeId, int $certId)
    {
        $cert = EmployeeCertification::where('employee_id', $employeeId)->findOrFail($certId);
        abort_unless($cert->file_path && Storage::disk('local')->exists($cert->file_path), 404);
        return Storage::disk('local')->download($cert->file_path, basename($cert->file_path));
    }

    public function previewCertification(int $employeeId, int $certId)
    {
        $cert = EmployeeCertification::where('employee_id', $employeeId)->findOrFail($certId);
        abort_unless($cert->file_path && Storage::disk('local')->exists($cert->file_path), 404);
        return response()->file(Storage::disk('local')->path($cert->file_path));
    }

    public function deleteCertification(int $employeeId, int $certId)
    {
        $cert = EmployeeCertification::where('employee_id', $employeeId)->findOrFail($certId);
        if ($cert->file_path) Storage::disk('local')->delete($cert->file_path);
        $cert->delete();
        return back()->with('success', 'Certification deleted.');
    }

    public function storeQualification(Request $request, int $id)
    {
        $request->validate([
            'degree'             => 'required|string|max:100',
            'institution'        => 'required|string|max:200',
            'subject'            => 'nullable|string|max:100',
            'university'         => 'nullable|string|max:200',
            'year_of_passing'    => 'nullable|digits:4|integer|min:1970|max:' . (date('Y') + 1),
            'grade_or_percentage'=> 'nullable|string|max:30',
            'education_level'    => 'required|in:secondary,higher_secondary,diploma,graduate,post_graduate,doctorate,other',
        ]);
        \App\Models\EmployeeQualification::create(array_merge($request->only([
            'degree','institution','subject','university','year_of_passing','grade_or_percentage','education_level',
        ]), ['employee_id' => $id]));
        return back()->with('success', 'Qualification added.');
    }

    public function deleteQualification(int $id, int $qualId)
    {
        \App\Models\EmployeeQualification::where('employee_id', $id)->where('id', $qualId)->delete();
        return back()->with('success', 'Qualification removed.');
    }

    public function storeExperience(Request $request, int $id)
    {
        $request->validate([
            'organisation'     => 'required|string|max:200',
            'role'             => 'required|string|max:100',
            'from_date'        => 'required|date',
            'to_date'          => 'nullable|date|after_or_equal:from_date',
            'is_current'       => 'nullable|boolean',
            'reason_for_leaving'=> 'nullable|string|max:200',
            'responsibilities' => 'nullable|string|max:500',
            'reference_contact'=> 'nullable|string|max:100',
        ]);
        \App\Models\EmployeeExperience::create(array_merge($request->only([
            'organisation','role','from_date','to_date',
            'reason_for_leaving','responsibilities','reference_contact',
        ]), ['employee_id' => $id, 'is_current' => $request->boolean('is_current')]));
        return back()->with('success', 'Experience added.');
    }

    public function deleteExperience(int $id, int $expId)
    {
        \App\Models\EmployeeExperience::where('employee_id', $id)->where('id', $expId)->delete();
        return back()->with('success', 'Experience removed.');
    }

    public function editEmployee(int $id)
    {
        $employee = Employee::findOrFail($id);
        return view('hr.employees-edit', compact('employee'));
    }

    public function updateEmployee(Request $request, int $id)
    {
        $employee = Employee::findOrFail($id);
        $validated = $request->validate([
            'first_name'    => 'required|string|max:60',
            'last_name'     => 'required|string|max:60',
            'mobile'        => 'required|string|max:15',
            'email'         => 'nullable|email|max:100',
            'designation'   => 'required|string|max:100',
            'department'    => 'nullable|string|max:100',
            'employee_type' => 'required|in:teaching,non_teaching,admin,support',
            'is_active'     => 'boolean',
            'pf_account_no' => 'nullable|string|max:30',
            'esi_no'        => 'nullable|string|max:30',
            'pan_no'        => 'nullable|string|max:15',
            'photo'         => 'nullable|image|max:2048',
        ]);

        $data = $validated;
        unset($data['photo']);

        if ($request->hasFile('photo')) {
            if ($employee->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->photo);
            }
            $data['photo'] = $request->file('photo')->store('employee-photos', 'public');
        }

        $employee->update($data);
        return redirect()->route('hr.employees.show', $employee->id)
            ->with('success', 'Employee updated.');
    }

    public function payroll(Request $request)
    {
        $month     = $request->month ?? now()->format('Y-m');
        $records   = PayrollRecord::with('employee')
            ->where('month', $month)->get();
        $employees = Employee::where('is_active', true)->orderBy('first_name')->get();
        return view('hr.payroll', compact('records', 'employees', 'month'));
    }

    public function processPayroll(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);

        // Cannot reprocess if any record for this month is locked
        $lockedCount = PayrollRecord::where('month', $request->month)->where('is_locked', true)->count();
        if ($lockedCount > 0) {
            return back()->with('error', "Payroll for {$request->month} is locked. Unlock records first.");
        }

        $employees   = Employee::where('is_active', true)->get();
        [$year, $mon] = explode('-', $request->month);
        $workingDays = (int) \App\Models\SchoolSetting::get('payroll_working_days', 26);

        DB::transaction(function () use ($request, $employees, $year, $mon, $workingDays) {
            foreach ($employees as $emp) {
                // Fetch gross salary from salary_structures
                $salaryStruct = DB::table('salary_structures')
                    ->where('employee_id', $emp->id)
                    ->where('is_active', true)
                    ->orderByDesc('effective_from')
                    ->first();
                $grossSalary = $salaryStruct
                    ? (float)($salaryStruct->basic_salary + $salaryStruct->hra + $salaryStruct->da +
                       $salaryStruct->ta + $salaryStruct->medical_allowance + $salaryStruct->other_allowances)
                    : 0;

                // Count LOP (Loss of Pay) days: StaffAttendance absent + no approved leave
                $lopDays = \App\Models\StaffAttendance::where('employee_id', $emp->id)
                    ->whereYear('date', $year)
                    ->whereMonth('date', $mon)
                    ->where('status', 'absent')
                    ->count();

                // Subtract approved leave days from LOP
                $leaveDays = \App\Models\LeaveRequest::where('employee_id', $emp->id)
                    ->where('status', 'approved')
                    ->where('leave_type_id', function ($q) {
                        $q->select('id')->from('leave_types')->where('is_paid', true)->limit(1);
                    })
                    ->whereMonth('from_date', $mon)->whereYear('from_date', $year)
                    ->count();
                $lopDays = max(0, $lopDays - $leaveDays);

                $dailyRate = $workingDays > 0 ? ($grossSalary / $workingDays) : 0;
                $lopAmount = round($dailyRate * $lopDays, 2);

                // Statutory deductions from salary structure
                $pfEmployee  = $salaryStruct ? (float) $salaryStruct->pf_employee  : 0;
                $esiEmployee = $salaryStruct ? (float) $salaryStruct->esi_employee : 0;
                $tdsMonthly  = $salaryStruct ? (float) $salaryStruct->tds          : 0;

                // Active loan EMI deduction
                $loanEmi = \App\Models\EmployeeLoan::where('employee_id', $emp->id)
                    ->where('status', 'active')
                    ->where('emi_start_month', '<=', $request->month . '-01')
                    ->sum('emi_amount');

                // Overtime earnings
                $overtimeAmount = DB::table('overtime_entries')
                    ->where('employee_id', $emp->id)
                    ->where('month', $request->month)
                    ->sum('amount');

                // Arrears / bonus / other additions
                $arrearsAmount = DB::table('arrears_bonus_entries')
                    ->where('employee_id', $emp->id)
                    ->where('month', $request->month)
                    ->where('type', 'arrears')
                    ->sum('amount');
                $bonusAmount = DB::table('arrears_bonus_entries')
                    ->where('employee_id', $emp->id)
                    ->where('month', $request->month)
                    ->whereIn('type', ['bonus', 'other_addition'])
                    ->sum('amount');

                $totalDeductions = round($lopAmount + $loanEmi + $pfEmployee + $esiEmployee + $tdsMonthly, 2);
                $netSalary = max(0, $grossSalary - $totalDeductions + $overtimeAmount + $arrearsAmount + $bonusAmount);

                PayrollRecord::updateOrCreate(
                    ['employee_id' => $emp->id, 'month' => $request->month],
                    [
                        'working_days'    => $workingDays,
                        'present_days'    => max(0, $workingDays - $lopDays),
                        'lop_days'        => $lopDays,
                        'lop_amount'      => $lopAmount,
                        'gross_salary'    => $grossSalary,
                        'deductions'      => $totalDeductions,
                        'overtime_amount' => $overtimeAmount,
                        'arrears_amount'  => $arrearsAmount,
                        'bonus_amount'    => $bonusAmount,
                        'net_salary'      => $netSalary,
                        'loan_emi'        => $loanEmi,
                        'status'          => 'draft',
                        'generated_by'    => auth()->id(),
                    ]
                );
            }
        });

        return redirect()->route('hr.payroll', ['month' => $request->month])
            ->with('success', 'Payroll generated for ' . $employees->count() . ' employees.');
    }

    public function approvePayroll(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);
        $count = PayrollRecord::where('month', $request->month)
            ->update(['status' => 'approved', 'is_locked' => true, 'locked_at' => now(), 'approved_by' => auth()->id()]);
        return back()->with('success', "{$count} payroll record(s) approved and locked for {$request->month}.");
    }

    public function unlockPayroll(Request $request, int $id)
    {
        PayrollRecord::findOrFail($id)->update(['is_locked' => false, 'locked_at' => null]);
        return back()->with('success', 'Payroll record unlocked.');
    }

    public function leaves(Request $request)
    {
        $leaves = \App\Models\LeaveRequest::with(['employee', 'leaveType'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->latest()->paginate(20);
        return view('hr.leaves', compact('leaves'));
    }

    private function generateEmployeeNumber(): string
    {
        $year = date('Y');
        $max  = Employee::whereYear('joining_date', $year)->count();
        return 'EMP-' . $year . '-' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }

    public function applyLeaveForm()
    {
        $employees  = Employee::where('is_active', true)->orderBy('first_name')->get();
        $leaveTypes = \App\Models\LeaveType::orderBy('name')->get();
        return view('hr.leave-apply', compact('employees', 'leaveTypes'));
    }

    public function storeLeave(Request $request)
    {
        $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'leave_type_id'    => 'required|exists:leave_types,id',
            'from_date'        => 'required|date',
            'to_date'          => 'required|date|after_or_equal:from_date',
            'reason'           => 'required|string|max:500',
            'is_half_day'      => 'boolean',
            'half_day_session' => 'nullable|in:morning,afternoon',
            'attachment'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $isHalfDay  = $request->boolean('is_half_day');
        $totalDays  = $isHalfDay ? 0.5 : (
            \Carbon\Carbon::parse($request->from_date)->diffInDays(\Carbon\Carbon::parse($request->to_date)) + 1
        );

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        \App\Models\LeaveRequest::create([
            'employee_id'       => $request->employee_id,
            'leave_type_id'     => $request->leave_type_id,
            'from_date'         => $request->from_date,
            'to_date'           => $request->to_date,
            'total_days'        => $totalDays,
            'reason'            => $request->reason,
            'status'            => 'pending',
            'is_half_day'       => $isHalfDay,
            'half_day_session'  => $request->half_day_session,
            'attachment'        => $attachmentPath,
        ]);

        return redirect()->route('hr.leaves')->with('success', 'Leave application submitted.');
    }

    public function approveLeave(int $id)
    {
        \App\Models\LeaveRequest::findOrFail($id)->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        return back()->with('success', 'Leave approved.');
    }

    public function rejectLeave(int $id)
    {
        \App\Models\LeaveRequest::findOrFail($id)->update(['status' => 'rejected', 'approved_by' => auth()->id()]);
        return back()->with('success', 'Leave rejected.');
    }

    public function downloadPayslip(int $id)
    {
        $payroll  = PayrollRecord::with('employee')->findOrFail($id);
        $employee = $payroll->employee;
        [$year, $month] = explode('-', $payroll->month . '-01') + [null, null, null];
        $school   = (object)['name' => config('school.name', 'School Name'), 'address' => config('school.address', ''), 'logo' => null];
        $pdf = Pdf::loadView('pdf.payslip', compact('school', 'employee', 'payroll', 'month', 'year'));
        return $pdf->download('payslip-' . $employee->employee_code . '-' . $payroll->month . '.pdf');
    }

    public function emailPayslip(int $id)
    {
        $payroll  = PayrollRecord::with('employee')->findOrFail($id);
        $employee = $payroll->employee;
        $email    = $employee->official_email ?? $employee->personal_email ?? null;
        if (!$email) {
            return back()->with('error', 'No email address found for ' . $employee->full_name . '.');
        }
        [$year, $month] = explode('-', $payroll->month . '-01') + [null, null, null];
        $school   = (object)['name' => config('school.name', 'School Name'), 'address' => config('school.address', ''), 'logo' => null];
        $pdf      = Pdf::loadView('pdf.payslip', compact('school', 'employee', 'payroll', 'month', 'year'));
        $fileName = 'payslip-' . $employee->employee_code . '-' . $payroll->month . '.pdf';
        $pdfPath  = storage_path('app/temp/' . $fileName);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $pdf->save($pdfPath);
        try {
            \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\PayslipMail($employee, $payroll, $pdfPath, $fileName));
            @unlink($pdfPath);
            return back()->with('success', "Payslip emailed to {$email}.");
        } catch (\Exception $e) {
            @unlink($pdfPath);
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function downloadPayslipProtected(int $id)
    {
        $payroll  = PayrollRecord::with('employee')->findOrFail($id);
        $employee = $payroll->employee;
        [$year, $month] = explode('-', $payroll->month . '-01') + [null, null, null];
        $school   = (object)['name' => config('school.name', 'School Name'), 'address' => config('school.address', ''), 'logo' => null];
        $pdf      = Pdf::loadView('pdf.payslip', compact('school', 'employee', 'payroll', 'month', 'year'));
        $fileName = 'payslip-' . $employee->employee_code . '-' . $payroll->month . '.pdf';
        $pdfPath  = storage_path('app/temp/' . $fileName);
        $zipName  = str_replace('.pdf', '.zip', $fileName);
        $zipPath  = storage_path('app/temp/' . $zipName);
        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        $pdf->save($pdfPath);
        // Password = employee DOB in DDMMYYYY, fallback to last 4 of PAN or '1234'
        $dob      = $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('dmY') : null;
        $pan      = $employee->pan_number ? substr($employee->pan_number, -4) : null;
        $password = $dob ?? $pan ?? '1234';
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->setPassword($password);
        $zip->addFile($pdfPath, $fileName);
        if (defined('ZipArchive::EM_AES_256')) {
            $zip->setEncryptionName($fileName, \ZipArchive::EM_AES_256);
        }
        $zip->close();
        @unlink($pdfPath);
        return response()->download($zipPath, $zipName, ['Content-Type' => 'application/zip'])
            ->deleteFileAfterSend(true);
    }

    public function bankTransfer(Request $request)
    {
        $month  = $request->month ?? now()->month;
        $year   = $request->year ?? now()->year;
        $format = $request->format ?? 'neft';

        $query = PayrollRecord::with('employee')
            ->where('month', "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT))
            ->where('status', 'approved');

        if ($request->bank) {
            $query->whereHas('employee', fn($q) => $q->where('bank_name', $request->bank));
        }

        $records = $query->get();
        $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT))->format('M_Y');

        return match($format) {
            'sbi'   => $this->generateSbiFormat($records, $monthLabel),
            'hdfc'  => $this->generateHdfcFormat($records, $monthLabel),
            'icici' => $this->generateIciciFormat($records, $monthLabel),
            'axis'  => $this->generateAxisFormat($records, $monthLabel),
            default => $this->generateNeftCsv($records, $monthLabel),
        };
    }

    private function generateNeftCsv($records, string $monthLabel)
    {
        $rows = ["Account_No,Name,IFSC,Amount,Remarks"];
        foreach ($records as $p) {
            $emp = $p->employee;
            $rows[] = implode(',', [
                '"' . ($emp?->bank_account_no ?? '') . '"',
                '"' . ($emp?->first_name . ' ' . $emp?->last_name) . '"',
                '"' . ($emp?->bank_ifsc ?? '') . '"',
                number_format($p->net_salary, 2, '.', ''),
                '"Salary ' . $monthLabel . '"',
            ]);
        }
        return response(implode("\n", $rows))
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="neft-transfer-' . $monthLabel . '.csv"');
    }

    private function generateSbiFormat($records, string $monthLabel)
    {
        // SBI bulk upload format: TXN_TYPE|BENE_ACNO|BENE_NAME|BENE_IFSC|AMT|REM_INFO
        $rows = ["TXN_TYPE|BENE_ACNO|BENE_NAME|BENE_IFSC|AMT|REM_INFO"];
        foreach ($records as $p) {
            $emp = $p->employee;
            $rows[] = implode('|', [
                'NEFT',
                $emp?->bank_account_no ?? '',
                $emp?->first_name . ' ' . $emp?->last_name,
                $emp?->bank_ifsc ?? '',
                number_format($p->net_salary, 2, '.', ''),
                'SALARY ' . $monthLabel,
            ]);
        }
        return response(implode("\n", $rows))
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="sbi-transfer-' . $monthLabel . '.txt"');
    }

    private function generateHdfcFormat($records, string $monthLabel)
    {
        // HDFC NetBanking bulk salary upload CSV
        $rows = ["SR No,Beneficiary Account No,Beneficiary Name,IFSC Code,Amount,Payment Type,Remarks"];
        $i = 1;
        foreach ($records as $p) {
            $emp = $p->employee;
            $rows[] = implode(',', [
                $i++,
                '"' . ($emp?->bank_account_no ?? '') . '"',
                '"' . ($emp?->first_name . ' ' . $emp?->last_name) . '"',
                '"' . ($emp?->bank_ifsc ?? '') . '"',
                number_format($p->net_salary, 2, '.', ''),
                'NEFT',
                '"Salary ' . $monthLabel . '"',
            ]);
        }
        return response(implode("\n", $rows))
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="hdfc-transfer-' . $monthLabel . '.csv"');
    }

    private function generateIciciFormat($records, string $monthLabel)
    {
        // ICICI Corporate Internet Banking bulk payment
        $rows = ["RecordType,Payee Name,Account No,IFSC,Amount,Payment Mode,Narration"];
        foreach ($records as $p) {
            $emp = $p->employee;
            $rows[] = implode(',', [
                'SALARY',
                '"' . ($emp?->first_name . ' ' . $emp?->last_name) . '"',
                '"' . ($emp?->bank_account_no ?? '') . '"',
                '"' . ($emp?->bank_ifsc ?? '') . '"',
                number_format($p->net_salary, 2, '.', ''),
                'NEFT',
                '"SALARY ' . $monthLabel . '"',
            ]);
        }
        return response(implode("\n", $rows))
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="icici-transfer-' . $monthLabel . '.csv"');
    }

    private function generateAxisFormat($records, string $monthLabel)
    {
        // Axis Bank bulk payment format
        $rows = ["Debit Acct No,Credit Acct No,Beneficiary Name,IFSC Code,Amount,Payment Type,Remarks"];
        $schoolAccount = \App\Models\SchoolSetting::get('bank_account_number', 'XXXXXXXXXX');
        foreach ($records as $p) {
            $emp = $p->employee;
            $rows[] = implode(',', [
                '"' . $schoolAccount . '"',
                '"' . ($emp?->bank_account_no ?? '') . '"',
                '"' . ($emp?->first_name . ' ' . $emp?->last_name) . '"',
                '"' . ($emp?->bank_ifsc ?? '') . '"',
                number_format($p->net_salary, 2, '.', ''),
                'NEFT',
                '"SALARY ' . $monthLabel . '"',
            ]);
        }
        return response(implode("\n", $rows))
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="axis-transfer-' . $monthLabel . '.csv"');
    }

    public function bankTransferView(Request $request)
    {
        $month  = $request->month ?? now()->month;
        $year   = $request->year ?? now()->year;
        $payrollSummary = PayrollRecord::with('employee')
            ->where('month', "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT))->get();
        $banks = $payrollSummary->map(fn($p) => $p->employee?->bank_name)->unique()->filter()->values();
        $payrollMonth = $month;
        $payrollYear  = $year;
        return view('hr.bank-transfer', compact('payrollSummary', 'banks', 'payrollMonth', 'payrollYear'));
    }

    public function salaryRegister(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year  = $request->year ?? now()->year;
        return Excel::download(new SalaryRegisterExport($month, $year), 'salary-register.xlsx');
    }

    public function challanGeneration(Request $request)
    {
        $month  = $request->month ?? now()->format('Y-m');
        $type   = $request->type ?? 'pf'; // pf | esi | pt

        $records = PayrollRecord::with('employee')
            ->where('month', $month)
            ->where('status', '!=', 'draft')
            ->get();

        $school = \App\Models\SchoolSetting::first();

        // PT slabs (Tamil Nadu)
        $ptSlabs = [
            ['min' => 0,     'max' => 3500,  'pt' => 0],
            ['min' => 3501,  'max' => 5000,  'pt' => 45],
            ['min' => 5001,  'max' => 7500,  'pt' => 90],
            ['min' => 7501,  'max' => 10000, 'pt' => 135],
            ['min' => 10001, 'max' => 12500, 'pt' => 180],
            ['min' => 12501, 'max' => PHP_INT_MAX, 'pt' => 208],
        ];
        $getPT = function(float $gross) use ($ptSlabs) {
            foreach ($ptSlabs as $slab) {
                if ($gross >= $slab['min'] && $gross <= $slab['max']) return $slab['pt'];
            }
            return 208;
        };

        $challanData = $records->map(function ($r) use ($getPT) {
            $pfEmployee = $r->pf_deduction ?? 0;
            $pfEmployer = round(($r->basic_salary ?? 0) * 0.12, 2);
            $esiEmployee = $r->esi_deduction ?? 0;
            $esiEmployer = round(($r->gross_salary ?? 0) * 0.0325, 2);
            $pt = $getPT($r->gross_salary ?? 0);
            return [
                'record'        => $r,
                'employee'      => $r->employee,
                'pf_employee'   => $pfEmployee,
                'pf_employer'   => $pfEmployer,
                'pf_total'      => round($pfEmployee + $pfEmployer, 2),
                'esi_employee'  => $esiEmployee,
                'esi_employer'  => $esiEmployer,
                'esi_total'     => round($esiEmployee + $esiEmployer, 2),
                'pt'            => $pt,
            ];
        });

        $totals = [
            'pf_employee'  => $challanData->sum('pf_employee'),
            'pf_employer'  => $challanData->sum('pf_employer'),
            'pf_total'     => $challanData->sum('pf_total'),
            'esi_employee' => $challanData->sum('esi_employee'),
            'esi_employer' => $challanData->sum('esi_employer'),
            'esi_total'    => $challanData->sum('esi_total'),
            'pt_total'     => $challanData->sum('pt'),
        ];

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('pdf.statutory-challan', compact('challanData', 'totals', 'type', 'month', 'school'));
            return $pdf->download("challan-{$type}-{$month}.pdf");
        }

        return view('hr.challan', compact('challanData', 'totals', 'type', 'month', 'school'));
    }

    public function statutoryReport(Request $request)
    {
        $financialYear = $request->financial_year ?? date('Y') . '-' . (date('Y') + 1);
        [$fyStart, $fyEnd] = $this->parseFY($financialYear);

        $employees = Employee::where('is_active', true)
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->orderBy('first_name')->get();
        $departments = Department::where('is_active', true)->get();

        $ptSlabs = [
            ['min' => 0,    'max' => 3500,  'pt' => 0],
            ['min' => 3501, 'max' => 5000,  'pt' => 45],
            ['min' => 5001, 'max' => 7500,  'pt' => 90],
            ['min' => 7501, 'max' => 10000, 'pt' => 135],
            ['min' => 10001,'max' => 12500, 'pt' => 180],
            ['min' => 12501,'max' => PHP_INT_MAX,'pt' => 208],
        ];
        $getPT = function(float $gross) use ($ptSlabs) {
            foreach ($ptSlabs as $s) { if ($gross >= $s['min'] && $gross <= $s['max']) return $s['pt']; }
            return 208;
        };

        $report = collect();
        if ($request->filled('action') || $request->filled('financial_year')) {
            $payrolls = PayrollRecord::with('employee')
                ->whereBetween('month', [$fyStart, $fyEnd])
                ->whereIn('employee_id', $employees->pluck('id'))
                ->get()->groupBy('employee_id');

            foreach ($employees as $emp) {
                $empPayrolls = $payrolls->get($emp->id, collect());
                $annualGross = $empPayrolls->sum('gross_salary');
                $annualPfEmp = $empPayrolls->sum('pf_deduction');
                $annualPfEr  = round($annualPfEmp * 1.1, 2); // employer typically ~1.1x
                $annualEsiEmp= $empPayrolls->sum('esi_deduction');
                $annualEsiEr = round($annualEsiEmp * (3.25/1.75), 2);
                $annualPT    = $empPayrolls->reduce(fn($carry, $p) => $carry + $getPT($p->gross_salary ?? 0), 0);

                $tds = $this->computeAnnualTax($emp, $annualGross);

                $report->push([
                    'employee'    => $emp,
                    'annual_gross'=> $annualGross,
                    'pf_employee' => $annualPfEmp,
                    'pf_employer' => $annualPfEr,
                    'esi_employee'=> $annualEsiEmp,
                    'esi_employer'=> $annualEsiEr,
                    'pt_annual'   => $annualPT,
                    'tds_annual'  => $tds['annual_tax'],
                    'months'      => $empPayrolls->count(),
                ]);
            }
        }

        if ($request->format === 'excel') {
            $headers = ['Employee', 'PAN', 'PF No', 'ESI No', 'Gross (Annual)', 'PF (Employee)', 'PF (Employer)', 'ESI (Employee)', 'ESI (Employer)', 'PT (Annual)', 'TDS (Annual)'];
            $rows = $report->map(fn($r) => [
                $r['employee']->first_name . ' ' . $r['employee']->last_name,
                $r['employee']->pan_no ?? '',
                $r['employee']->pf_account_no ?? '',
                $r['employee']->esi_no ?? '',
                $r['annual_gross'], $r['pf_employee'], $r['pf_employer'],
                $r['esi_employee'], $r['esi_employer'], $r['pt_annual'], $r['tds_annual'],
            ])->prepend($headers);

            return Excel::download(new \App\Exports\CollectionExport($rows, 'Statutory Report'), 'statutory-report-' . $financialYear . '.xlsx');
        }

        return view('hr.statutory-report', compact('departments', 'report', 'financialYear'));
    }

    public function departments()
    {
        $departments  = Department::withCount(['designations', 'employees'])->orderBy('name')->get();
        $designations = Designation::with('department')->orderBy('name')->get();
        return view('hr.departments', compact('departments', 'designations'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        Department::create($request->only(['name', 'code', 'description']) + ['is_active' => true]);
        return back()->with('success', 'Department added.');
    }

    public function updateDepartment(Request $request, int $id)
    {
        Department::findOrFail($id)->update($request->only(['name', 'code', 'description', 'is_active']));
        return back()->with('success', 'Department updated.');
    }

    public function storeDesignation(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100', 'department_id' => 'required|exists:departments,id']);
        $designation = Designation::create(
            $request->only(['name', 'department_id', 'grade', 'pay_band_min', 'pay_band_max', 'pay_scale', 'description', 'spatie_role'])
            + ['is_active' => true]
        );
        // Ensure the Spatie role exists if specified
        if ($designation->spatie_role) {
            Role::findOrCreate($designation->spatie_role, 'web');
        }
        return back()->with('success', 'Designation added.');
    }

    public function updateDesignation(Request $request, int $id)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $designation = Designation::findOrFail($id);
        $oldRole = $designation->spatie_role;
        $designation->update(array_merge(
            $request->only(['name', 'department_id', 'grade', 'pay_band_min', 'pay_band_max', 'pay_scale', 'description', 'spatie_role']),
            ['is_active' => $request->boolean('is_active')]
        ));
        $newRole = $designation->fresh()->spatie_role;
        // Ensure new role exists
        if ($newRole) {
            Role::findOrCreate($newRole, 'web');
        }
        // Re-sync all employees with this designation if role changed
        if ($oldRole !== $newRole) {
            $this->syncDesignationRoles($designation->id, $oldRole, $newRole);
        }
        return back()->with('success', 'Designation updated.');
    }

    protected function syncDesignationRoles(int $designationId, ?string $oldRole, ?string $newRole): void
    {
        $employees = Employee::where('designation_id', $designationId)
            ->whereNotNull('user_id')->with('user')->get();
        foreach ($employees as $emp) {
            if (!$emp->user) continue;
            if ($oldRole) $emp->user->removeRole($oldRole);
            if ($newRole) $emp->user->assignRole($newRole);
        }
    }

    public function appointmentLetter(int $id)
    {
        $employee  = Employee::findOrFail($id);
        $school    = \App\Models\SchoolSetting::first() ?? (object)[
            'school_name' => config('app.name', 'DASA EduERP'),
            'address'     => 'School Main Campus, Knowledge Park',
            'phone'       => '+91 9876543210',
            'email'       => 'admin@dasaeduerp.com',
            'logo'        => null,
        ];
        $refNumber = 'APT-' . date('Y') . '-' . str_pad($employee->id, 4, '0', STR_PAD_LEFT);
        $pdf       = Pdf::loadView('pdf.appointment-letter', compact('school', 'employee', 'refNumber'));
        return $pdf->download('appointment-letter-' . ($employee->employee_code ?? $employee->id) . '.pdf');
    }

    public function experienceCertificate(int $id)
    {
        $employee = Employee::findOrFail($id);
        $school   = \App\Models\SchoolSetting::first() ?? (object)[
            'school_name' => config('app.name', 'DASA EduERP'),
            'address'     => 'School Main Campus, Knowledge Park',
            'phone'       => '+91 9876543210',
            'email'       => 'admin@dasaeduerp.com',
            'logo'        => null,
        ];
        $pdf      = Pdf::loadView('pdf.experience-certificate', compact('school', 'employee'));
        return $pdf->download('experience-certificate-' . ($employee->employee_code ?? $employee->id) . '.pdf');
    }

    public function leaveBalance(Request $request)
    {
        $leaveTypes = \App\Models\LeaveType::where('is_active', true)->get();
        $year       = $request->year ?? now()->year;
        $employees  = Employee::where('is_active', true)
            ->when($request->department, fn($q, $v) => $q->where('department', $v))
            ->orderBy('first_name')->get();
        $usedMap = \App\Models\LeaveRequest::whereIn('employee_id', $employees->pluck('id'))
            ->where('status', 'approved')
            ->whereYear('from_date', $year)
            ->selectRaw('employee_id, leave_type_id, SUM(days) as total_days')
            ->groupBy('employee_id', 'leave_type_id')
            ->get()->groupBy('employee_id')->map(fn($g) => $g->keyBy('leave_type_id'));
        $departments = Employee::select('department')->distinct()->pluck('department')->filter();
        return view('hr.leave-balance', compact('employees', 'leaveTypes', 'usedMap', 'year', 'departments'));
    }

    public function cancelLeave(int $id)
    {
        $leave = \App\Models\LeaveRequest::findOrFail($id);
        if (in_array($leave->status, ['pending', 'approved'])) {
            $leave->update(['status' => 'cancelled', 'approved_by' => auth()->id()]);
        }
        return back()->with('success', 'Leave cancelled.');
    }

    public function carryForwardLeaves(Request $request)
    {
        $request->validate(['from_year' => 'required|integer', 'to_year' => 'required|integer|gt:from_year']);
        $leaveTypes = \App\Models\LeaveType::where('is_active', true)->get();
        $employees  = Employee::where('is_active', true)->get();
        $carried    = 0;
        foreach ($employees as $emp) {
            foreach ($leaveTypes as $lt) {
                $used = \App\Models\LeaveRequest::where('employee_id', $emp->id)
                    ->where('leave_type_id', $lt->id)->where('status', 'approved')
                    ->whereYear('from_date', $request->from_year)->sum('days');
                $remaining = max(0, $lt->days_allowed - $used);
                if ($remaining > 0) {
                    \App\Models\LeaveRequest::create([
                        'employee_id'   => $emp->id,
                        'leave_type_id' => $lt->id,
                        'from_date'     => $request->to_year . '-01-01',
                        'to_date'       => $request->to_year . '-01-01',
                        'days'          => $remaining,
                        'reason'        => 'Carry-forward from ' . $request->from_year,
                        'status'        => 'approved',
                        'approved_by'   => auth()->id(),
                    ]);
                    $carried++;
                }
            }
        }
        return back()->with('success', "Carry-forward done. $carried entries created.");
    }

    public function bulkPayslipPdf(Request $request)
    {
        $request->validate(['month' => 'required|date_format:Y-m']);
        $records = PayrollRecord::with('employee')->where('month', $request->month)->get();
        if ($records->isEmpty()) return back()->with('error', 'No payroll records found for this month.');
        [$year, $mo] = explode('-', $request->month);
        $month = $mo;
        $school = (object)['name' => config('school.name', 'School Name'), 'address' => config('school.address', ''), 'logo' => null];
        $pdf = Pdf::loadView('pdf.bulk-payslips', compact('records', 'school', 'month', 'year'));
        return $pdf->download('payslips-' . $request->month . '.pdf');
    }

    public function relievingLetter(int $id)
    {
        $employee  = Employee::findOrFail($id);
        $school    = \App\Models\SchoolSetting::first() ?? (object)['school_name' => config('school.name', 'School Name'), 'address' => config('school.address', '')];
        $refNumber = 'REL-' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
        $pdf = Pdf::loadView('pdf.relieving-letter', compact('school', 'employee', 'refNumber'));
        return $pdf->download('relieving-letter-' . $employee->employee_code . '.pdf');
    }

    public function nocLetter(int $id)
    {
        $employee  = Employee::findOrFail($id);
        $school    = \App\Models\SchoolSetting::first() ?? (object)['school_name' => config('school.name', 'School Name'), 'address' => config('school.address', '')];
        $refNumber = 'NOC-' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
        $pdf = Pdf::loadView('pdf.noc-letter', compact('school', 'employee', 'refNumber'));
        return $pdf->download('noc-' . $employee->employee_code . '.pdf');
    }

    public function incrementLetter(int $id)
    {
        $employee  = Employee::findOrFail($id);
        $school    = \App\Models\SchoolSetting::first() ?? (object)['school_name' => config('school.name', 'School Name'), 'address' => config('school.address', '')];
        $refNumber = 'INC-' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
        $newSalary = ($employee->basic_salary ?? 0) * 1.05;
        $pdf = Pdf::loadView('pdf.increment-letter', compact('school', 'employee', 'refNumber', 'newSalary'));
        return $pdf->download('increment-letter-' . $employee->employee_code . '.pdf');
    }

    public function holdSalary(int $id)
    {
        Employee::findOrFail($id)->update(['salary_on_hold' => true]);
        return back()->with('success', 'Salary held for this employee.');
    }

    public function releaseSalary(int $id)
    {
        Employee::findOrFail($id)->update(['salary_on_hold' => false]);
        return back()->with('success', 'Salary released.');
    }

    public function leaveCalendar(Request $request)
    {
        $departments = Department::where('is_active', true)->get();
        $month       = $request->month ?? now()->format('Y-m');
        [$yr, $mo]   = explode('-', $month);
        $period      = CarbonPeriod::create("$yr-$mo-01", "last day of $yr-$mo");
        $dates        = collect($period)->map(fn($d) => $d);
        $employees    = Employee::with('department')->where('is_active', true)
            ->when($request->department_id, fn($q, $v) => $q->where('department_id', $v))
            ->orderBy('first_name')->get();
        $leaveMap = [];
        $leaves   = \App\Models\LeaveRequest::where('status', 'approved')
            ->whereIn('employee_id', $employees->pluck('id'))
            ->where(fn($q) => $q->whereMonth('from_date', $mo)->orWhereMonth('to_date', $mo))
            ->get();
        foreach ($leaves as $l) {
            $lp = CarbonPeriod::create($l->from_date, $l->to_date);
            foreach ($lp as $d) {
                if ($d->month == $mo) {
                    $leaveMap[$l->employee_id][$d->toDateString()] = 'approved';
                }
            }
        }
        return view('hr.leave-calendar', compact('departments', 'employees', 'dates', 'leaveMap', 'month'));
    }

    public function departmentSalary(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        [$yr, $mo] = explode('-', $month);
        $departments = Department::where('is_active', true)->get();
        $data = [];
        foreach ($departments as $dept) {
            $records = PayrollRecord::where('month', $month)
                ->whereHas('employee', fn($q) => $q->where('department_id', $dept->id))
                ->with('employee')
                ->get();
            $data[] = [
                'department'  => $dept,
                'count'       => $records->count(),
                'gross'       => $records->sum('gross_salary'),
                'deductions'  => $records->sum('total_deductions'),
                'net'         => $records->sum('net_salary'),
            ];
        }
        $grandTotal = [
            'count'      => array_sum(array_column($data, 'count')),
            'gross'      => array_sum(array_column($data, 'gross')),
            'deductions' => array_sum(array_column($data, 'deductions')),
            'net'        => array_sum(array_column($data, 'net')),
        ];
        return view('hr.department-salary', compact('data', 'grandTotal', 'month'));
    }

    public function leaveHistory(int $id)
    {
        $employee = Employee::findOrFail($id);
        $leaves   = \App\Models\LeaveRequest::where('employee_id', $id)
            ->with('leaveType')
            ->latest()
            ->paginate(20);
        return view('hr.leave-history', compact('employee', 'leaves'));
    }

    public function leaveEncashment(Request $request)
    {
        $employees  = Employee::where('is_active', true)->orderBy('first_name')->get();
        $elTypes    = \App\Models\LeaveType::where('is_active', true)->where('code', 'EL')->get();
        if ($elTypes->isEmpty()) {
            $elTypes = \App\Models\LeaveType::where('is_active', true)->get();
        }
        $history = \App\Models\LeaveEncashment::with(['employee', 'leaveType'])
            ->latest()->paginate(20);

        $calculation = null;
        if ($request->employee_id && $request->leave_type_id && $request->days) {
            $emp      = Employee::findOrFail($request->employee_id);
            $lt       = \App\Models\LeaveType::findOrFail($request->leave_type_id);
            $year     = $request->year ?? date('Y');
            $used     = \App\Models\LeaveRequest::where('employee_id', $emp->id)
                ->where('leave_type_id', $lt->id)->where('status', 'approved')
                ->whereYear('from_date', $year)->sum('total_days');
            $balance  = max(0, ($lt->days_allowed ?? 0) - $used);
            $days     = min((int)$request->days, $balance);
            $basicPerDay = ($emp->basic_salary ?? 0) / 26;
            $amount   = round($basicPerDay * $days, 2);
            $calculation = compact('emp', 'lt', 'year', 'balance', 'days', 'basicPerDay', 'amount');
        }

        return view('hr.leave-encashment', compact('employees', 'elTypes', 'history', 'calculation'));
    }

    public function processLeaveEncashment(Request $request)
    {
        $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'days_encashed' => 'required|integer|min:1|max:365',
            'year'          => 'required|digits:4',
            'encashment_date' => 'required|date',
            'remarks'       => 'nullable|string|max:500',
        ]);

        $emp    = Employee::findOrFail($request->employee_id);
        $basicPerDay = ($emp->basic_salary ?? 0) / 26;
        $amount = round($basicPerDay * $request->days_encashed, 2);

        \App\Models\LeaveEncashment::create([
            'employee_id'    => $request->employee_id,
            'leave_type_id'  => $request->leave_type_id,
            'days_encashed'  => $request->days_encashed,
            'basic_per_day'  => $basicPerDay,
            'amount'         => $amount,
            'year'           => $request->year,
            'remarks'        => $request->remarks,
            'processed_by'   => auth()->id(),
            'encashment_date'=> $request->encashment_date,
        ]);

        return back()->with('success', "Leave encashment of ₹" . number_format($amount, 2) . " processed for {$emp->full_name}.");
    }

    // ── Advance Salary ─────────────────────────────────────

    public function salaryAdvances(Request $request)
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        $advances  = \App\Models\SalaryAdvance::with('employee', 'approvedBy')
            ->when($request->employee_id, fn($q) => $q->where('employee_id', $request->employee_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()->paginate(25);
        return view('hr.salary-advances', compact('employees', 'advances'));
    }

    public function storeSalaryAdvance(Request $request)
    {
        $validated = $request->validate([
            'employee_id'      => 'required|exists:employees,id',
            'amount'           => 'required|numeric|min:1',
            'advance_date'     => 'required|date',
            'reason'           => 'nullable|string|max:500',
            'recovery_months'  => 'required|integer|min:1|max:24',
            'remarks'          => 'nullable|string|max:500',
        ]);
        $validated['monthly_deduction'] = round($validated['amount'] / $validated['recovery_months'], 2);
        \App\Models\SalaryAdvance::create($validated);
        return back()->with('success', 'Salary advance recorded.');
    }

    public function approveSalaryAdvance(int $id)
    {
        $advance = \App\Models\SalaryAdvance::findOrFail($id);
        $advance->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_date' => today()]);
        return back()->with('success', 'Advance approved.');
    }

    public function rejectSalaryAdvance(int $id)
    {
        \App\Models\SalaryAdvance::findOrFail($id)->update(['status' => 'rejected']);
        return back()->with('success', 'Advance rejected.');
    }

    public function recordAdvanceRecovery(Request $request, int $id)
    {
        $request->validate(['recovery_amount' => 'required|numeric|min:0.01']);
        $advance = \App\Models\SalaryAdvance::findOrFail($id);
        $newRecovered = $advance->recovered_amount + $request->recovery_amount;
        $status = $newRecovered >= $advance->amount ? 'recovered' : 'approved';
        $advance->update(['recovered_amount' => $newRecovered, 'status' => $status]);
        return back()->with('success', '₹' . number_format($request->recovery_amount, 2) . ' recovery recorded.');
    }

    public function appraisalForm(Request $request)
    {
        $employees = Employee::where('is_active', true)->orderBy('first_name')->get();

        $kpis = \App\Models\SchoolSetting::get('appraisal_kpis',
            'Punctuality,Subject Knowledge,Student Feedback,Lesson Plan Adherence,Teamwork,Communication,Professional Development');
        $kpiList = array_map('trim', explode(',', $kpis));

        $appraisals = collect();
        if ($request->year) {
            $appraisals = \App\Models\EmployeeAppraisal::with('employee')
                ->where('appraisal_year', $request->year)
                ->get()->keyBy('employee_id');
        }

        return view('hr.appraisal', compact('employees', 'kpiList', 'appraisals'));
    }

    public function saveAppraisal(Request $request)
    {
        $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'appraisal_year' => 'required|integer|min:2000|max:2100',
            'ratings'        => 'required|array',
        ]);

        $overallScore = collect($request->ratings)->avg();
        $rating = match(true) {
            $overallScore >= 4.5 => 'Outstanding',
            $overallScore >= 3.5 => 'Very Good',
            $overallScore >= 2.5 => 'Good',
            $overallScore >= 1.5 => 'Average',
            default              => 'Below Average',
        };

        \App\Models\EmployeeAppraisal::updateOrCreate(
            ['employee_id' => $request->employee_id, 'appraisal_year' => $request->appraisal_year],
            [
                'ratings'           => json_encode($request->ratings),
                'overall_score'     => round($overallScore, 2),
                'rating_label'      => $rating,
                'hod_remarks'       => $request->hod_remarks,
                'principal_remarks' => $request->principal_remarks,
                'appraised_by'      => auth()->id(),
                'appraised_at'      => now(),
            ]
        );
        return back()->with('success', 'Appraisal saved. Rating: ' . $rating);
    }

    public function selfAppraisalForm(Request $request)
    {
        $employees = Employee::where('is_active', true)->orderBy('first_name')->get();
        $kpis      = \App\Models\SchoolSetting::get('appraisal_kpis',
            'Punctuality,Subject Knowledge,Student Feedback,Lesson Plan Adherence,Teamwork,Communication,Professional Development');
        $kpiList   = array_map('trim', explode(',', $kpis));

        $appraisal = null;
        if ($request->employee_id && $request->year) {
            $appraisal = \App\Models\EmployeeAppraisal::where('employee_id', $request->employee_id)
                ->where('appraisal_year', $request->year)->first();
        }
        return view('hr.self-appraisal', compact('employees', 'kpiList', 'appraisal'));
    }

    public function saveSelfAppraisal(Request $request)
    {
        $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'appraisal_year' => 'required|integer|min:2000|max:2100',
            'self_ratings'   => 'required|array',
            'self_remarks'   => 'nullable|string|max:1000',
        ]);
        $selfScore = round(collect($request->self_ratings)->avg(), 2);

        \App\Models\EmployeeAppraisal::updateOrCreate(
            ['employee_id' => $request->employee_id, 'appraisal_year' => $request->appraisal_year],
            [
                'self_ratings'      => $request->self_ratings,
                'self_score'        => $selfScore,
                'self_remarks'      => $request->self_remarks,
                'self_submitted_at' => now(),
            ]
        );
        return back()->with('success', 'Self-appraisal saved. Score: ' . $selfScore . '/5');
    }

    public function processIncrement(Request $request, int $appraisalId)
    {
        $request->validate([
            'increment_type'           => 'required|in:percent,amount',
            'increment_value'          => 'required|numeric|min:0',
            'increment_effective_date' => 'required|date',
        ]);

        $appraisal = \App\Models\EmployeeAppraisal::with('employee')->findOrFail($appraisalId);
        $employee  = $appraisal->employee;
        $current   = $employee->gross_salary ?? 0;

        if ($request->increment_type === 'percent') {
            $amount  = round($current * $request->increment_value / 100, 2);
            $percent = $request->increment_value;
        } else {
            $amount  = $request->increment_value;
            $percent = $current > 0 ? round($amount / $current * 100, 2) : 0;
        }

        $appraisal->update([
            'increment_amount'         => $amount,
            'increment_percent'        => $percent,
            'increment_effective_date' => $request->increment_effective_date,
            'increment_approved_by'    => Auth::id(),
            'increment_approved_at'    => now(),
        ]);

        // Apply increment to employee salary
        $newGross = $current + $amount;
        $employee->update(['gross_salary' => $newGross]);

        return back()->with('success', "Increment of ₹{$amount} ({$percent}%) applied. New gross: ₹{$newGross}.");
    }

    // ── Loan Management ──────────────────────────────────────

    public function loans(Request $request)
    {
        $employees = Employee::where('is_active', true)->orderBy('first_name')->get();
        $query = \App\Models\EmployeeLoan::with('employee.department');
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $loans = $query->latest()->paginate(20);
        return view('hr.loans', compact('loans', 'employees'));
    }

    public function storeLoan(Request $request)
    {
        $request->validate([
            'employee_id'       => 'required|exists:employees,id',
            'loan_type'         => 'required|string',
            'principal_amount'  => 'required|numeric|min:1',
            'interest_rate'     => 'nullable|numeric|min:0|max:100',
            'emi_months'        => 'required|integer|min:1',
            'disbursement_date' => 'required|date',
            'emi_start_month'   => 'required|date',
            'purpose'           => 'nullable|string',
        ]);

        $principal = $request->principal_amount;
        $rate      = $request->interest_rate ?? 0;
        $months    = $request->emi_months;

        if ($rate > 0) {
            $monthlyRate = $rate / 100 / 12;
            $emi = round($principal * $monthlyRate * pow(1 + $monthlyRate, $months) / (pow(1 + $monthlyRate, $months) - 1), 2);
        } else {
            $emi = round($principal / $months, 2);
        }

        \App\Models\EmployeeLoan::create([
            'employee_id'         => $request->employee_id,
            'loan_type'           => $request->loan_type,
            'principal_amount'    => $principal,
            'interest_rate'       => $rate,
            'emi_months'          => $months,
            'emi_amount'          => $emi,
            'total_paid'          => 0,
            'outstanding_balance' => $principal,
            'disbursement_date'   => $request->disbursement_date,
            'emi_start_month'     => $request->emi_start_month,
            'status'              => 'active',
            'purpose'             => $request->purpose,
            'approved_by'         => auth()->id(),
            'approved_at'         => now(),
        ]);
        return back()->with('success', 'Loan created. EMI: ₹' . number_format($emi, 2) . '/month.');
    }

    public function closeLoan(int $id)
    {
        \App\Models\EmployeeLoan::findOrFail($id)->update(['status' => 'closed', 'outstanding_balance' => 0]);
        return back()->with('success', 'Loan marked as closed.');
    }

    public function recordLoanPayment(Request $request, int $id)
    {
        $loan = \App\Models\EmployeeLoan::findOrFail($id);
        $amount = min($request->amount, $loan->outstanding_balance);
        $loan->update([
            'total_paid'          => $loan->total_paid + $amount,
            'outstanding_balance' => max(0, $loan->outstanding_balance - $amount),
            'status'              => ($loan->outstanding_balance - $amount) <= 0 ? 'closed' : $loan->status,
        ]);
        return back()->with('success', 'Payment of ₹' . number_format($amount, 2) . ' recorded.');
    }

    // ── Staff Attendance vs Leave Reconciliation ─────────────

    public function attendanceLeaveReconciliation(Request $request)
    {
        $month = $request->month ?? date('Y-m');
        [$year, $mon] = explode('-', $month);

        $employees = Employee::where('is_active', true)
            ->with('department')
            ->orderBy('first_name')->get();

        $reconciliation = $employees->map(function ($emp) use ($year, $mon) {
            // Total working days attendance records
            $totalDays = \App\Models\StaffAttendance::where('employee_id', $emp->id)
                ->whereYear('date', $year)->whereMonth('date', $mon)->count();

            // Present/half-day count
            $presentDays = \App\Models\StaffAttendance::where('employee_id', $emp->id)
                ->whereYear('date', $year)->whereMonth('date', $mon)
                ->whereIn('status', ['present', 'half_day'])->count();

            // Absent count
            $absentDays = \App\Models\StaffAttendance::where('employee_id', $emp->id)
                ->whereYear('date', $year)->whereMonth('date', $mon)
                ->where('status', 'absent')->count();

            // Approved leave days
            $approvedLeaves = \App\Models\LeaveRequest::where('employee_id', $emp->id)
                ->where('status', 'approved')
                ->whereMonth('from_date', $mon)->whereYear('from_date', $year)
                ->sum('total_days');

            // LOP = absences not covered by approved leave
            $lopDays = max(0, $absentDays - $approvedLeaves);

            return [
                'employee'       => $emp,
                'total_days'     => $totalDays,
                'present_days'   => $presentDays,
                'absent_days'    => $absentDays,
                'approved_leave' => $approvedLeaves,
                'lop_days'       => $lopDays,
            ];
        });

        return view('hr.attendance-leave-reconciliation', compact('reconciliation', 'month'));
    }

    public function tdsComputation(Request $request)
    {
        $employees = Employee::where('is_active', true)
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->orderBy('first_name')->get();

        $financialYear = $request->financial_year ?? date('Y') . '-' . (date('Y') + 1);
        [$fyStart, $fyEnd] = $this->parseFY($financialYear);
        $departments = Department::where('is_active', true)->get();

        $tdsReport = collect();
        if ($request->filled('action') || $request->filled('financial_year')) {
            foreach ($employees as $emp) {
                $annualGross = PayrollRecord::where('employee_id', $emp->id)
                    ->whereBetween('month', [$fyStart, $fyEnd])
                    ->sum('gross_salary');

                $tds = $this->computeAnnualTax($emp, $annualGross);

                $totalTdsPaid = PayrollRecord::where('employee_id', $emp->id)
                    ->whereBetween('month', [$fyStart, $fyEnd])
                    ->sum('tds_amount');

                $tdsReport->push([
                    'employee'    => $emp,
                    'gross'       => $annualGross,
                    'std_ded'     => $tds['std_deduction'],
                    'invest_80c'  => $tds['invest_80c'],
                    'invest_80d'  => $tds['invest_80d'],
                    'hra_exempt'  => $tds['hra_exemption'],
                    'taxable'     => $tds['taxable_income'],
                    'annual_tax'  => $tds['annual_tax'],
                    'monthly_tds' => $tds['monthly_tds'],
                    'tds_paid'    => $totalTdsPaid,
                    'balance'     => max(0, $tds['annual_tax'] - $totalTdsPaid),
                    'regime'      => $emp->tax_regime ?? 'new',
                ]);
            }
        }

        return view('hr.tds-computation', compact('departments', 'tdsReport', 'financialYear'));
    }

    public function saveTdsDeclaration(Request $request, int $id)
    {
        $request->validate([
            'investment_80c' => 'nullable|numeric|min:0|max:150000',
            'investment_80d' => 'nullable|numeric|min:0|max:50000',
            'hra_exemption'  => 'nullable|numeric|min:0',
            'tax_regime'     => 'required|in:old,new',
        ]);
        Employee::findOrFail($id)->update($request->only(['investment_80c', 'investment_80d', 'hra_exemption', 'tax_regime']));
        return back()->with('success', 'TDS declaration saved.');
    }

    public function form16Pdf(int $id, Request $request)
    {
        $financialYear = $request->financial_year ?? date('Y') . '-' . (date('Y') + 1);
        [$fyStart, $fyEnd] = $this->parseFY($financialYear);

        $employee = Employee::findOrFail($id);
        $payrolls = PayrollRecord::where('employee_id', $id)
            ->whereBetween('month', [$fyStart, $fyEnd])
            ->orderBy('month')
            ->get();

        $annualGross = $payrolls->sum('gross_salary');
        $totalDeductions = $payrolls->sum('deductions');
        $netPaid = $payrolls->sum('net_salary');
        $totalTds = $payrolls->sum('tds_amount');

        $tds = $this->computeAnnualTax($employee, $annualGross);
        $school = \App\Models\SchoolSetting::first();

        $pdf = Pdf::loadView('pdf.form-16', compact(
            'employee', 'payrolls', 'financialYear', 'annualGross',
            'totalDeductions', 'netPaid', 'totalTds', 'tds', 'school'
        ))->setPaper('a4');

        return $pdf->download('form-16-' . ($employee->employee_code ?? $id) . '-' . str_replace('/', '-', $financialYear) . '.pdf');
    }

    private function parseFY(string $fy): array
    {
        [$startY, $endY] = explode('-', $fy);
        return ["{$startY}-04", "{$endY}-03"];
    }

    private function computeAnnualTax(Employee $emp, float $annualGross): array
    {
        $regime = $emp->tax_regime ?? 'new';
        $stdDeduction  = 50000;
        $invest80c     = min((float)($emp->investment_80c ?? 0), 150000);
        $invest80d     = min((float)($emp->investment_80d ?? 0), 50000);
        $hraExemption  = (float)($emp->hra_exemption ?? 0);

        if ($regime === 'old') {
            $taxable = max(0, $annualGross - $stdDeduction - $invest80c - $invest80d - $hraExemption);
            $tax = $this->calculateOldRegimeTax($taxable);
        } else {
            $taxable = max(0, $annualGross - $stdDeduction);
            $tax = $this->calculateNewRegimeTax($taxable);
        }

        $cess = $tax * 0.04;
        $annualTax = round($tax + $cess, 2);

        return [
            'std_deduction' => $stdDeduction,
            'invest_80c'    => $invest80c,
            'invest_80d'    => $invest80d,
            'hra_exemption' => $hraExemption,
            'taxable_income'=> round($taxable, 2),
            'base_tax'      => round($tax, 2),
            'cess'          => round($cess, 2),
            'annual_tax'    => $annualTax,
            'monthly_tds'   => round($annualTax / 12, 2),
        ];
    }

    private function calculateNewRegimeTax(float $income): float
    {
        if ($income <= 300000)  return 0;
        if ($income <= 600000)  return ($income - 300000) * 0.05;
        if ($income <= 900000)  return 15000 + ($income - 600000) * 0.10;
        if ($income <= 1200000) return 45000 + ($income - 900000) * 0.15;
        if ($income <= 1500000) return 90000 + ($income - 1200000) * 0.20;
        return 150000 + ($income - 1500000) * 0.30;
    }

    private function calculateOldRegimeTax(float $income): float
    {
        if ($income <= 250000)  return 0;
        if ($income <= 500000)  return ($income - 250000) * 0.05;
        if ($income <= 1000000) return 12500 + ($income - 500000) * 0.20;
        return 112500 + ($income - 1000000) * 0.30;
    }

    // ── Staff ID Cards ────────────────────────────────────

    public function staffIdCards(Request $request)
    {
        $departments = \App\Models\Department::orderBy('name')->get();
        $employees   = \App\Models\Employee::with('department')
            ->when($request->department_id, fn($q, $v) => $q->where('department_id', $v))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        return view('hr.staff-id-cards', compact('employees', 'departments'));
    }

    public function downloadStaffIdCards(Request $request)
    {
        $employees = \App\Models\Employee::with('department')
            ->when($request->department_id, fn($q, $v) => $q->where('department_id', $v))
            ->when($request->ids, fn($q, $v) => $q->whereIn('id', explode(',', $v)))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $school = \App\Models\SchoolSetting::first();
        $validUntil = now()->addYear()->format('M Y');

        foreach ($employees as $emp) {
            $qrData = implode(' | ', array_filter([
                'ID: ' . ($emp->employee_id ?? $emp->id),
                'Name: ' . $emp->name,
                'Dept: ' . ($emp->department?->name ?? ''),
                'Blood: ' . ($emp->blood_group ?? ''),
            ]));
            try {
                $emp->_qrCode = base64_encode(
                    \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(80)->generate($qrData)
                );
            } catch (\Exception $e) {
                $emp->_qrCode = null;
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.staff-id-cards', compact('employees', 'school', 'validUntil'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('staff-id-cards.pdf');
    }

    // ── Overtime Entry ────────────────────────────────────

    public function overtime(Request $request)
    {
        $month       = $request->month ?? now()->format('Y-m');
        $departments = \App\Models\Department::orderBy('name')->get();
        $employees   = \App\Models\Employee::with('department')
            ->where('is_active', true)
            ->when($request->department_id, fn($q, $v) => $q->where('department_id', $v))
            ->orderBy('name')->get();

        $entries = \App\Models\OvertimeEntry::with('employee.department')
            ->where('month', $month)
            ->orderBy('entry_date')
            ->get();

        return view('hr.overtime', compact('employees', 'departments', 'month', 'entries'));
    }

    public function storeOvertime(Request $request)
    {
        $request->validate([
            'employee_id'   => 'required|exists:employees,id',
            'entry_date'    => 'required|date',
            'hours'         => 'required|numeric|min:0.5|max:24',
            'rate_per_hour' => 'required|numeric|min:0',
            'remarks'       => 'nullable|string|max:200',
        ]);

        $amount = round($request->hours * $request->rate_per_hour, 2);
        $month  = \Carbon\Carbon::parse($request->entry_date)->format('Y-m');

        \App\Models\OvertimeEntry::create([
            'employee_id'   => $request->employee_id,
            'entry_date'    => $request->entry_date,
            'hours'         => $request->hours,
            'rate_per_hour' => $request->rate_per_hour,
            'amount'        => $amount,
            'month'         => $month,
            'remarks'       => $request->remarks,
            'created_by'    => auth()->id(),
        ]);

        return back()->with('success', 'Overtime entry saved.');
    }

    // ── Arrears & Bonus ───────────────────────────────────

    public function arrearsBonus(Request $request)
    {
        $month       = $request->month ?? now()->format('Y-m');
        $departments = \App\Models\Department::orderBy('name')->get();
        $employees   = \App\Models\Employee::with('department')
            ->where('is_active', true)
            ->orderBy('name')->get();

        $entries = \App\Models\ArrearsBonusEntry::with('employee.department')
            ->where('month', $month)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalMonth = $entries->sum('amount');

        return view('hr.arrears-bonus', compact('employees', 'departments', 'month', 'entries', 'totalMonth'));
    }

    public function storeArrearsBonus(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type'        => 'required|in:arrears,bonus,other_addition',
            'amount'      => 'required|numeric|min:0.01',
            'month'       => 'required|string|size:7',
            'description' => 'nullable|string|max:300',
        ]);

        \App\Models\ArrearsBonusEntry::create([
            'employee_id' => $request->employee_id,
            'type'        => $request->type,
            'amount'      => $request->amount,
            'month'       => $request->month,
            'description' => $request->description,
            'payment_mode'=> 'payroll',
            'created_by'  => auth()->id(),
        ]);

        return back()->with('success', ucfirst($request->type) . ' entry added.');
    }

    // ── Increment History Report ──────────────────────────

    public function incrementHistory(Request $request)
    {
        $fyStart = $request->from ? \Carbon\Carbon::parse($request->from) : now()->startOfYear();
        $fyEnd   = $request->to   ? \Carbon\Carbon::parse($request->to)   : now();

        $increments = \App\Models\EmployeeAppraisal::with('employee.department')
            ->whereNotNull('increment_amount')
            ->where('increment_amount', '>', 0)
            ->where(fn($q) => $q->whereBetween('increment_effective_date', [$fyStart, $fyEnd])
                               ->orWhereBetween('appraised_at', [$fyStart, $fyEnd]))
            ->orderByDesc('increment_effective_date')
            ->get();

        $totalIncrement = $increments->sum('increment_amount');

        return view('hr.increment-history', compact('increments', 'fyStart', 'fyEnd', 'totalIncrement'));
    }

    // ── Leave Encashment Report ───────────────────────────

    public function leaveEncashmentReport(Request $request)
    {
        $fyStart = $request->from ? \Carbon\Carbon::parse($request->from) : now()->startOfYear();
        $fyEnd   = $request->to   ? \Carbon\Carbon::parse($request->to)   : now();

        $encashments = \App\Models\LeaveEncashment::with('employee.department')
            ->whereBetween('encashment_date', [$fyStart, $fyEnd])
            ->orderByDesc('encashment_date')
            ->get();

        $totalPaid = $encashments->sum('amount');

        return view('hr.leave-encashment-report', compact('encashments', 'fyStart', 'fyEnd', 'totalPaid'));
    }

    // ── HR Report Exports ─────────────────────────────────

    public function incrementHistoryPdf(Request $request)
    {
        $fyStart = $request->from ? \Carbon\Carbon::parse($request->from) : now()->startOfYear();
        $fyEnd   = $request->to   ? \Carbon\Carbon::parse($request->to)   : now();
        $increments     = \App\Models\EmployeeAppraisal::with('employee.department')
            ->whereNotNull('increment_amount')->where('increment_amount', '>', 0)
            ->where(fn($q) => $q->whereBetween('increment_effective_date', [$fyStart, $fyEnd])
                               ->orWhereBetween('appraised_at', [$fyStart, $fyEnd]))
            ->orderByDesc('increment_effective_date')->get();
        $totalIncrement = $increments->sum('increment_amount');
        $school = \App\Models\SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.increment-history', compact('increments', 'fyStart', 'fyEnd', 'totalIncrement', 'school'))
            ->setPaper('a4', 'portrait');
        return $pdf->download('increment-history-'.now()->format('Y').'.pdf');
    }

    public function incrementHistoryExcel(Request $request)
    {
        $fyStart = $request->from ? \Carbon\Carbon::parse($request->from) : now()->startOfYear();
        $fyEnd   = $request->to   ? \Carbon\Carbon::parse($request->to)   : now();
        $increments = \App\Models\EmployeeAppraisal::with('employee.department')
            ->whereNotNull('increment_amount')->where('increment_amount', '>', 0)
            ->where(fn($q) => $q->whereBetween('increment_effective_date', [$fyStart, $fyEnd])
                               ->orWhereBetween('appraised_at', [$fyStart, $fyEnd]))
            ->orderByDesc('increment_effective_date')->get();
        $rows = $increments->map(fn($inc) => [
            'Employee'     => $inc->employee?->name,
            'Department'   => $inc->employee?->department?->name ?? '—',
            'Year'         => $inc->appraisal_year,
            'Increment (₹)'=> number_format($inc->increment_amount, 2),
            'Increment %'  => $inc->increment_percent ? number_format($inc->increment_percent, 1).'%' : '—',
            'Effective From' => $inc->increment_effective_date?->format('d M Y') ?? '—',
            'Rating'       => $inc->rating_label ?? '—',
        ])->values();
        return Excel::download(
            new \App\Exports\ArrayExport($rows->toArray(), ['Employee', 'Department', 'Year', 'Increment (₹)', 'Increment %', 'Effective From', 'Rating']),
            'increment-history-'.now()->format('Y').'.xlsx'
        );
    }

    public function leaveEncashmentReportPdf(Request $request)
    {
        $fyStart = $request->from ? \Carbon\Carbon::parse($request->from) : now()->startOfYear();
        $fyEnd   = $request->to   ? \Carbon\Carbon::parse($request->to)   : now();
        $encashments = \App\Models\LeaveEncashment::with('employee.department')
            ->whereBetween('encashment_date', [$fyStart, $fyEnd])
            ->orderByDesc('encashment_date')->get();
        $totalPaid = $encashments->sum('amount');
        $school = \App\Models\SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.leave-encashment-report', compact('encashments', 'fyStart', 'fyEnd', 'totalPaid', 'school'))
            ->setPaper('a4', 'portrait');
        return $pdf->download('leave-encashment-report.pdf');
    }

    public function leaveEncashmentReportExcel(Request $request)
    {
        $fyStart = $request->from ? \Carbon\Carbon::parse($request->from) : now()->startOfYear();
        $fyEnd   = $request->to   ? \Carbon\Carbon::parse($request->to)   : now();
        $encashments = \App\Models\LeaveEncashment::with('employee.department')
            ->whereBetween('encashment_date', [$fyStart, $fyEnd])
            ->orderByDesc('encashment_date')->get();
        $rows = $encashments->map(fn($enc) => [
            'Employee'   => $enc->employee?->name,
            'Department' => $enc->employee?->department?->name ?? '—',
            'Date'       => $enc->encashment_date?->format('d M Y'),
            'Days'       => $enc->days,
            'Amount (₹)' => number_format($enc->amount, 2),
            'Remarks'    => $enc->remarks ?? '—',
        ])->values();
        return Excel::download(
            new \App\Exports\ArrayExport($rows->toArray(), ['Employee', 'Department', 'Date', 'Days', 'Amount (₹)', 'Remarks']),
            'leave-encashment-report.xlsx'
        );
    }

    public function salaryRegisterPdf(Request $request)
    {
        $month   = $request->month ?? now()->format('Y-m');
        $records = PayrollRecord::with('employee.department')->where('month', $month)
            ->where('status', '!=', 'draft')->orderBy('employee_id')->get();
        $school  = \App\Models\SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.salary-register', compact('records', 'month', 'school'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('salary-register-'.$month.'.pdf');
    }

    public function salaryRegisterExcel(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');
        $year  = substr($month, 0, 4);
        $mon   = substr($month, 5, 2);
        return Excel::download(new SalaryRegisterExport($mon, $year), 'salary-register-'.$month.'.xlsx');
    }

    public function orgChart(Request $request)
    {
        $departments = Department::with([
            'employees' => function ($q) {
                $q->where('status', 'active')
                  ->with(['designation', 'manager', 'directReports' => fn($r) => $r->where('status', 'active')]);
            },
        ])->get();

        $allEmployees = Employee::where('status', 'active')
            ->with(['designation', 'department'])
            ->orderBy('first_name')
            ->get();

        // Build top-level employees (no manager or manager outside active workforce)
        $roots = $allEmployees->filter(fn($e) => !$e->manager_id || !$allEmployees->contains('id', $e->manager_id));

        return view('hr.org-chart', compact('departments', 'allEmployees', 'roots'));
    }

    public function setManager(Request $request, int $id)
    {
        $request->validate(['manager_id' => 'nullable|exists:employees,id']);
        $employee = Employee::findOrFail($id);
        if ($request->manager_id && (int)$request->manager_id === $id) {
            return back()->with('error', 'An employee cannot be their own manager.');
        }
        $employee->update(['manager_id' => $request->manager_id ?: null]);
        $managerName = $request->manager_id ? Employee::find($request->manager_id)?->full_name : 'none';
        return back()->with('success', "{$employee->full_name}'s reporting manager set to {$managerName}.");
    }
}
