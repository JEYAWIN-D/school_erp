<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Enquiry;
use App\Models\EnquiryFollowUp;
use App\Models\AdmissionStage;
use App\Models\SeatCapacity;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Exports\EnquiriesExport;
use App\Exports\ArrayExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Enquiry::with(['class', 'academicYear'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
            ->when($request->date_from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->date_to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($request->search, fn($q, $v) => $q->where(function ($q) use ($v) {
                $q->where('student_name', 'like', "%$v%")
                  ->orWhere('parent_mobile', 'like', "%$v%")
                  ->orWhere('enquiry_number', 'like', "%$v%");
            }))
            ->latest();

        $enquiries = $query->paginate(20)->withQueryString();
        $classes   = Classes::active()->get();
        $stats     = [
            'total'      => Enquiry::count(),
            'new'        => Enquiry::where('status', 'new')->count(),
            'follow_up'  => Enquiry::where('status', 'follow_up')->count(),
            'converted'  => Enquiry::where('status', 'converted')->count(),
            'lost'       => Enquiry::where('status', 'lost')->count(),
        ];

        return view('admissions.index', compact('enquiries', 'classes', 'stats'));
    }

    public function create()
    {
        $classes      = Classes::active()->get();
        $academicYear = AcademicYear::current();
        $users        = \App\Models\User::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('admissions.create', compact('classes', 'academicYear', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_name'    => 'required|string|max:100',
            'dob'             => 'nullable|date|before:today',
            'gender'          => 'nullable|in:male,female,other',
            'class_id'        => 'required|exists:classes,id',
            'parent_name'     => 'required|string|max:100',
            'parent_mobile'   => 'required|string|max:15',
            'parent_email'    => 'nullable|email|max:100',
            'address'         => 'nullable|string|max:255',
            'source'          => 'nullable|string|max:50',
            'notes'           => 'nullable|string',
            'follow_up_date'  => 'nullable|date|after_or_equal:today',
            'previous_school' => 'nullable|string|max:150',
            'previous_class'  => 'nullable|string|max:50',
            'previous_percentage' => 'nullable|numeric|between:0,100',
            'assigned_to'         => 'nullable|exists:users,id',
        ]);

        $currentYear = AcademicYear::current();

        $enquiry = Enquiry::create(array_merge($validated, [
            'enquiry_number'  => Enquiry::generateNumber(),
            'status'          => 'new',
            'academic_year_id'=> $currentYear?->id,
            'created_by'      => Auth::id(),
        ]));

        return redirect()->route('admissions.show', $enquiry->id)
            ->with('success', 'Enquiry ' . $enquiry->enquiry_number . ' created successfully.');
    }

    public function show(int $id)
    {
        $enquiry  = Enquiry::with(['class', 'followUps.createdBy', 'createdBy'])->findOrFail($id);
        $classes  = Classes::active()->get();
        return view('admissions.show', compact('enquiry', 'classes'));
    }

    public function edit(int $id)
    {
        $enquiry      = Enquiry::findOrFail($id);
        $classes      = Classes::active()->get();
        $academicYear = AcademicYear::current();
        $users        = \App\Models\User::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return view('admissions.edit', compact('enquiry', 'classes', 'academicYear', 'users'));
    }

    public function update(Request $request, int $id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $validated = $request->validate([
            'student_name'    => 'required|string|max:100',
            'dob'             => 'nullable|date|before:today',
            'gender'          => 'nullable|in:male,female,other',
            'class_id'        => 'required|exists:classes,id',
            'parent_name'     => 'required|string|max:100',
            'parent_mobile'   => 'required|string|max:15',
            'parent_email'    => 'nullable|email|max:100',
            'address'         => 'nullable|string|max:255',
            'source'          => 'nullable|string|max:50',
            'notes'           => 'nullable|string',
            'follow_up_date'  => 'nullable|date',
            'previous_school' => 'nullable|string|max:150',
            'previous_class'  => 'nullable|string|max:50',
            'previous_percentage' => 'nullable|numeric|between:0,100',
            'assigned_to'         => 'nullable|exists:users,id',
        ]);

        $enquiry->update($validated);

        return redirect()->route('admissions.show', $enquiry->id)
            ->with('success', 'Enquiry updated successfully.');
    }

    public function destroy(int $id)
    {
        abort_unless(auth()->user()->can('delete admissions'), 403);
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->delete();
        return redirect()->route('admissions.index')
            ->with('success', 'Enquiry deleted.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status'          => 'required|in:new,follow_up,converted,lost',
            'notes'           => 'nullable|string',
            'follow_up_date'  => 'nullable|date',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        $enquiry->update([
            'status'         => $request->status,
            'follow_up_date' => $request->follow_up_date,
        ]);

        if ($request->notes) {
            EnquiryFollowUp::create([
                'enquiry_id'          => $enquiry->id,
                'notes'               => $request->notes,
                'next_follow_up_date' => $request->follow_up_date,
                'status'              => $request->status,
                'created_by'          => Auth::id(),
            ]);
        }

        return back()->with('success', 'Status updated to ' . $request->status . '.');
    }

    public function enquiryForm()
    {
        $classes      = Classes::active()->get();
        $academicYear = AcademicYear::current();
        return view('admissions.public-enquiry', compact('classes', 'academicYear'));
    }

    public function submitEnquiry(Request $request)
    {
        $validated = $request->validate([
            'student_name'  => 'required|string|max:100',
            'class_id'      => 'required|exists:classes,id',
            'parent_name'   => 'required|string|max:100',
            'parent_mobile' => 'required|string|max:15',
            'parent_email'  => 'nullable|email|max:100',
            'documents.*'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        $currentYear = AcademicYear::current();

        $docPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $docPaths[] = $file->store('enquiry-docs', 'public');
            }
        }

        $enquiry = Enquiry::create(array_merge($validated, [
            'enquiry_number'   => Enquiry::generateNumber(),
            'status'           => 'new',
            'academic_year_id' => $currentYear?->id,
            'source'           => $request->source ?? 'website',
            'referral_name'    => $request->referral_name,
            'documents'        => $docPaths ?: null,
        ]));

        // Notify admin
        $adminEmail = \App\Models\SchoolSetting::get('admin_notification_email', config('mail.from.address'));
        if ($adminEmail) {
            try {
                \Illuminate\Support\Facades\Mail::to($adminEmail)
                    ->send(new \App\Mail\NewEnquiryNotification($enquiry->load('class')));
            } catch (\Exception $e) {
                // Fail silently — don't block enquiry submission
            }
        }

        return back()->with('success', 'Thank you! Your enquiry number is ' . $enquiry->enquiry_number);
    }

    public function pipeline(Request $request)
    {
        $stages = ['enquiry', 'application', 'entrance_test', 'interview', 'document_verification', 'confirmed', 'enrolled', 'rejected'];
        $currentStage = $request->stage ?? 'enquiry';
        $pipeline     = collect($stages)->mapWithKeys(fn($s) => [$s => Enquiry::where('status', $s)->count()]);
        $enquiries    = Enquiry::with('class')->where('status', $currentStage)->latest()->paginate(20);
        return view('admissions.pipeline', compact('stages', 'currentStage', 'pipeline', 'enquiries'));
    }

    public function advanceStage(Request $request, int $id)
    {
        $stages  = ['enquiry', 'application', 'entrance_test', 'interview', 'document_verification', 'confirmed', 'enrolled'];
        $enquiry = Enquiry::findOrFail($id);
        $idx     = array_search($enquiry->status, $stages);
        if ($idx === false || $idx >= count($stages) - 1) {
            return back()->with('error', 'Cannot advance stage further.');
        }
        $nextStage = $stages[$idx + 1];

        // Over-admission check when confirming
        if ($nextStage === 'confirmed' && $enquiry->class_id) {
            $currentYear = AcademicYear::current();
            $totalCapacity = SeatCapacity::where('class_id', $enquiry->class_id)
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->sum('total_seats');
            $filled = SeatCapacity::where('class_id', $enquiry->class_id)
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->sum('filled_seats');
            $confirmed = Enquiry::where('class_id', $enquiry->class_id)
                ->whereIn('status', ['confirmed', 'enrolled'])
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->count();
            if ($totalCapacity > 0 && ($filled + $confirmed) >= $totalCapacity && !$request->boolean('override')) {
                return back()->with('error', "Seat limit reached for this class ({$totalCapacity} total). Add to waitlist or use override.");
            }
        }

        $enquiry->update(['status' => $nextStage]);
        return back()->with('success', 'Stage advanced to ' . ucwords(str_replace('_', ' ', $nextStage)) . '.');
    }

    public function seats(Request $request)
    {
        $classes      = Classes::active()->get();
        $academicYear = AcademicYear::current();
        $seats        = SeatCapacity::with('class')
            ->when($academicYear, fn($q) => $q->where('academic_year_id', $academicYear->id))
            ->get();
        return view('admissions.seats', compact('classes', 'seats', 'academicYear'));
    }

    public function saveSeats(Request $request)
    {
        $currentYear = AcademicYear::current();

        if ($request->has('seats') && is_array($request->seats)) {
            // Bulk array form
            DB::transaction(function () use ($request, $currentYear) {
                foreach ($request->seats as $classId => $data) {
                    SeatCapacity::updateOrCreate(
                        ['class_id' => $classId, 'academic_year_id' => $currentYear?->id, 'category' => $data['category'] ?? 'general'],
                        ['total_seats' => $data['total'] ?? 0, 'filled_seats' => $data['filled'] ?? 0]
                    );
                }
            });
        } else {
            // Single-entry form
            $request->validate([
                'class_id'    => 'required|exists:classes,id',
                'category'    => 'required|string',
                'total_seats' => 'required|integer|min:0',
            ]);
            SeatCapacity::updateOrCreate(
                ['class_id' => $request->class_id, 'academic_year_id' => $currentYear?->id, 'category' => $request->category],
                ['total_seats' => $request->total_seats]
            );
        }

        return back()->with('success', 'Seat capacities saved.');
    }

    public function bulkImport(Request $request)
    {
        $classes = Classes::active()->get();
        return view('admissions.bulk-import', compact('classes'));
    }

    public function processBulkImport(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:2048']);

        $file        = $request->file('file');
        $ext         = strtolower($file->getClientOriginalExtension());
        $currentYear = AcademicYear::current();
        $imported    = 0;
        $errors      = [];

        if ($ext === 'csv') {
            $rows = array_map('str_getcsv', file($file->getRealPath()));
            $header = array_map('trim', array_shift($rows));
        } else {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            $header = array_map('trim', array_shift($sheet));
            $rows = $sheet;
        }

        // Normalize header keys
        $header = array_map(fn($h) => strtolower(str_replace([' ', '-'], '_', $h)), $header);

        foreach ($rows as $i => $row) {
            if (empty(array_filter($row))) continue;
            $data = array_combine($header, array_pad($row, count($header), null));

            $name = trim($data['student_name'] ?? $data['name'] ?? '');
            if (!$name) { $errors[] = "Row " . ($i + 2) . ": missing student name"; continue; }

            $classId = null;
            if (!empty($data['class_id'])) {
                $classId = (int) $data['class_id'];
            } elseif (!empty($data['class'])) {
                $cls = Classes::where('name', 'like', '%' . trim($data['class']) . '%')->first();
                $classId = $cls?->id;
            }
            if (!$classId) { $errors[] = "Row " . ($i + 2) . ": invalid class"; continue; }

            try {
                Enquiry::create([
                    'enquiry_number'  => Enquiry::generateNumber(),
                    'student_name'    => $name,
                    'parent_name'     => $data['parent_name'] ?? $data['father_name'] ?? '',
                    'parent_mobile'   => $data['parent_mobile'] ?? $data['mobile'] ?? '',
                    'parent_email'    => $data['parent_email'] ?? $data['email'] ?? null,
                    'class_id'        => $classId,
                    'source'          => $data['source'] ?? 'bulk_import',
                    'notes'           => $data['notes'] ?? $data['remarks'] ?? null,
                    'status'          => 'new',
                    'academic_year_id'=> $currentYear?->id,
                    'created_by'      => auth()->id(),
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($i + 2) . ": " . $e->getMessage();
            }
        }

        $msg = "Imported {$imported} enquiries successfully.";
        if ($errors) $msg .= ' ' . count($errors) . ' rows skipped: ' . implode('; ', array_slice($errors, 0, 3));
        return back()->with('success', $msg);
    }

    public function analytics(Request $request)
    {
        $currentYear = AcademicYear::current();
        $bySource    = Enquiry::select('source', DB::raw('COUNT(*) as total'))
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->groupBy('source')->pluck('total', 'source');
        $byStatus    = Enquiry::select('status', DB::raw('COUNT(*) as total'))
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->groupBy('status')->pluck('total', 'status');
        $byClass     = Enquiry::with('class')->select('class_id', DB::raw('COUNT(*) as total'))
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->groupBy('class_id')->get();
        $monthlyTrend = Enquiry::select(DB::raw('MONTH(created_at) as m'), DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', now()->year)
            ->groupBy('m')->orderBy('m')->pluck('total', 'm');

        // Counsellor-wise conversion
        $byCounsellor = Enquiry::select('assigned_to', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN status IN (\'confirmed\',\'enrolled\') THEN 1 ELSE 0 END) as converted'))
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->whereNotNull('assigned_to')
            ->with('assignedTo')
            ->groupBy('assigned_to')->get();

        // Year-over-year
        $prevYear = AcademicYear::where('id', '<', $currentYear?->id)->orderByDesc('id')->first();
        $yoyData  = null;
        if ($currentYear && $prevYear) {
            $yoyData = [
                'current' => ['year' => $currentYear->name, 'enquiries' => Enquiry::where('academic_year_id', $currentYear->id)->count(), 'converted' => Enquiry::where('academic_year_id', $currentYear->id)->where('status', 'converted')->count()],
                'prev'    => ['year' => $prevYear->name, 'enquiries' => Enquiry::where('academic_year_id', $prevYear->id)->count(), 'converted' => Enquiry::where('academic_year_id', $prevYear->id)->where('status', 'converted')->count()],
            ];
        }

        return view('admissions.analytics', compact('bySource', 'byStatus', 'byClass', 'monthlyTrend', 'currentYear', 'yoyData', 'byCounsellor'));
    }

    public function exportEnquiries(Request $request)
    {
        $filters = $request->only(['status', 'class_id', 'search']);
        $filename = 'enquiries_' . now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new EnquiriesExport($filters), $filename);
    }

    public function bulkStatusUpdate(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:enquiries,id',
            'status' => 'required|in:new,follow_up,converted,lost',
        ]);

        Enquiry::whereIn('id', $request->ids)->update(['status' => $request->status]);

        return back()->with('success', count($request->ids) . ' enquiries updated to ' . $request->status . '.');
    }

    public function applicationForm()
    {
        $classes      = Classes::active()->get();
        $academicYear = AcademicYear::current();
        return view('admissions.application-form', compact('classes', 'academicYear'));
    }

    public function storeApplication(Request $request)
    {
        $validated = $request->validate([
            'student_name'  => 'required|string|max:100',
            'dob'           => 'nullable|date|before:today',
            'gender'        => 'nullable|in:male,female,other',
            'class_id'      => 'required|exists:classes,id',
            'parent_name'   => 'required|string|max:100',
            'parent_mobile' => 'required|string|max:15',
            'parent_email'  => 'nullable|email|max:100',
            'address'       => 'nullable|string|max:500',
            'previous_school' => 'nullable|string|max:150',
        ]);

        // Check duplicate
        $dup = Enquiry::where('parent_mobile', $validated['parent_mobile'])
            ->where('class_id', $validated['class_id'])
            ->whereYear('created_at', now()->year)
            ->first();
        if ($dup) {
            return back()->withInput()->with('error', 'An enquiry with the same mobile and class already exists: ' . $dup->enquiry_number);
        }

        $currentYear = AcademicYear::current();
        $enquiry = Enquiry::create(array_merge($validated, [
            'enquiry_number'   => Enquiry::generateNumber(),
            'status'           => 'application',
            'source'           => 'application_form',
            'academic_year_id' => $currentYear?->id,
        ]));

        return back()->with('success', 'Application submitted! Reference number: ' . $enquiry->enquiry_number);
    }

    public function confirmationLetter(int $id)
    {
        $enquiry = Enquiry::with(['class', 'academicYear'])->findOrFail($id);
        $school  = \App\Models\SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.admission-confirmation', compact('enquiry', 'school'));
        return $pdf->stream('admission-confirmation-' . $enquiry->enquiry_number . '.pdf');
    }

    public function rejectAdmission(Request $request, int $id)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        Enquiry::findOrFail($id)->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'Admission rejected with reason captured.');
    }

    public function waitlist(Request $request)
    {
        $currentYear = AcademicYear::current();
        $waitlisted  = Enquiry::where('status', 'waitlisted')
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->with('class')->orderBy('waitlist_position')->get();
        return view('admissions.waitlist', compact('waitlisted', 'currentYear'));
    }

    public function addToWaitlist(Request $request, int $id)
    {
        $currentYear = AcademicYear::current();
        $maxPos = Enquiry::where('status', 'waitlisted')
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->max('waitlist_position') ?? 0;
        Enquiry::findOrFail($id)->update([
            'status'            => 'waitlisted',
            'waitlist_position' => $maxPos + 1,
        ]);
        return back()->with('success', 'Added to waitlist at position ' . ($maxPos + 1) . '.');
    }

    public function promoteFromWaitlist(Request $request, int $id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $enquiry->update(['status' => 'confirmed', 'waitlist_position' => null]);
        // Shift remaining waitlist positions up
        Enquiry::where('status', 'waitlisted')
            ->where('waitlist_position', '>', $enquiry->waitlist_position)
            ->decrement('waitlist_position');
        return back()->with('success', 'Promoted from waitlist to confirmed.');
    }

    public function saveEntranceTest(Request $request, int $id)
    {
        $request->validate([
            'entrance_test_date'        => 'required|date',
            'entrance_test_time'        => 'nullable|string|max:10',
            'entrance_test_venue'       => 'nullable|string|max:200',
            'entrance_test_invigilator' => 'nullable|string|max:200',
            'entrance_test_marks'       => 'nullable|numeric|min:0|max:999',
        ]);
        Enquiry::findOrFail($id)->update($request->only([
            'entrance_test_date', 'entrance_test_time', 'entrance_test_venue',
            'entrance_test_invigilator', 'entrance_test_marks',
        ]));
        return back()->with('success', 'Entrance test details saved.');
    }

    public function saveInterview(Request $request, int $id)
    {
        $request->validate([
            'interview_date'        => 'required|date',
            'interview_time'        => 'nullable|string|max:10',
            'interview_interviewer' => 'nullable|string|max:200',
            'interview_feedback'    => 'nullable|string|max:1000',
        ]);
        Enquiry::findOrFail($id)->update($request->only([
            'interview_date', 'interview_time', 'interview_interviewer', 'interview_feedback',
        ]));
        return back()->with('success', 'Interview details saved.');
    }

    public function saveDocChecklist(Request $request, int $id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $docs    = $request->input('docs', []);
        $enquiry->update(['doc_checklist' => $docs]);
        return back()->with('success', 'Document checklist updated.');
    }

    public function flagMissingDocs(Request $request, int $id)
    {
        $enquiry = Enquiry::findOrFail($id);
        $missing = $request->input('missing_docs', []);
        $enquiry->update([
            'missing_docs'   => $missing,
            'docs_flag_note' => $request->docs_flag_note,
        ]);
        return back()->with('success', empty($missing) ? 'Missing document flags cleared.' : count($missing) . ' document(s) flagged as missing.');
    }

    public function entranceTestHallTicket(int $id)
    {
        $enquiry = \App\Models\Enquiry::with(['class', 'academicYear'])->findOrFail($id);

        abort_if(! $enquiry->entrance_test_date, 404, 'Entrance test not yet scheduled.');

        $school = \App\Models\SchoolSetting::first();

        $qrData = implode(' | ', array_filter([
            'ENQ: ' . $enquiry->enquiry_number,
            'Name: ' . $enquiry->student_name,
            'Test: ' . $enquiry->entrance_test_date->format('d M Y'),
            $enquiry->entrance_test_time ? 'Time: ' . $enquiry->entrance_test_time : null,
            $enquiry->entrance_test_venue ? 'Venue: ' . $enquiry->entrance_test_venue : null,
        ]));

        $qrCode = null;
        try {
            $qrCode = base64_encode(
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(120)->generate($qrData)
            );
        } catch (\Exception $e) {
            // QR optional
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.entrance-hall-ticket', compact('enquiry', 'school', 'qrCode'));
        $pdf->setPaper('A5', 'portrait');

        return $pdf->stream('entrance-hall-ticket-' . $enquiry->enquiry_number . '.pdf');
    }

    /* ------------------------------------------------------------------ */
    /*  Analytics Report Exports                                            */
    /* ------------------------------------------------------------------ */

    private function buildAnalyticsData(): array
    {
        $currentYear = AcademicYear::current();

        $bySource = Enquiry::select('source', DB::raw('COUNT(*) as total'))
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->groupBy('source')->pluck('total', 'source');

        $byStatus = Enquiry::select('status', DB::raw('COUNT(*) as total'))
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->groupBy('status')->pluck('total', 'status');

        $byClass = Enquiry::with('class')
            ->select('class_id', DB::raw('COUNT(*) as total'))
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->groupBy('class_id')->get();

        // Class-wise vs seat capacity
        $seats = SeatCapacity::with('class')
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->get()->groupBy('class_id');
        $classSeats = $byClass->map(function ($row) use ($seats) {
            $cap = $seats->get($row->class_id)?->sum('total_seats') ?? 0;
            return [
                'class'      => $row->class?->name ?? 'Unknown',
                'enquiries'  => $row->total,
                'seats'      => $cap,
                'fill_pct'   => $cap > 0 ? round($row->total / $cap * 100, 1) : null,
            ];
        })->sortBy('class')->values();

        $monthlyTrend = Enquiry::select(DB::raw('MONTH(created_at) as m'), DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', now()->year)
            ->groupBy('m')->orderBy('m')->pluck('total', 'm');

        $byCounsellor = Enquiry::select('assigned_to', DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status IN (\'confirmed\',\'enrolled\',\'converted\') THEN 1 ELSE 0 END) as converted'))
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->whereNotNull('assigned_to')
            ->with('assignedTo')
            ->groupBy('assigned_to')->get();

        $prevYear = AcademicYear::where('id', '<', $currentYear?->id)->orderByDesc('id')->first();
        $yoyData  = null;
        if ($currentYear && $prevYear) {
            $yoyData = [
                'current' => [
                    'year'      => $currentYear->name,
                    'enquiries' => Enquiry::where('academic_year_id', $currentYear->id)->count(),
                    'converted' => Enquiry::where('academic_year_id', $currentYear->id)
                        ->whereIn('status', ['confirmed', 'enrolled', 'converted'])->count(),
                ],
                'prev' => [
                    'year'      => $prevYear->name,
                    'enquiries' => Enquiry::where('academic_year_id', $prevYear->id)->count(),
                    'converted' => Enquiry::where('academic_year_id', $prevYear->id)
                        ->whereIn('status', ['confirmed', 'enrolled', 'converted'])->count(),
                ],
            ];
        }

        $school = SchoolSetting::first();

        return compact('currentYear', 'bySource', 'byStatus', 'byClass', 'classSeats',
            'monthlyTrend', 'byCounsellor', 'yoyData', 'school');
    }

    public function analyticsReportPdf()
    {
        $data = $this->buildAnalyticsData();
        $pdf  = Pdf::loadView('pdf.admission-analytics', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('admission-analytics-' . now()->format('Y-m-d') . '.pdf');
    }

    public function analyticsReportExcel()
    {
        $data = $this->buildAnalyticsData();

        // Sheet 1: Source-wise
        $sourceRows = $data['bySource']->map(fn($t, $s) => ['Source' => ucfirst($s), 'Count' => $t])->values()->toArray();
        // Sheet 2: Status-wise
        $statusRows = $data['byStatus']->map(fn($t, $s) => ['Status' => ucfirst(str_replace('_', ' ', $s)), 'Count' => $t])->values()->toArray();
        // Sheet 3: Class vs seats
        $seatRows = $data['classSeats']->map(fn($r) => [
            'Class'     => $r['class'],
            'Enquiries' => $r['enquiries'],
            'Seats'     => $r['seats'],
            'Fill %'    => $r['fill_pct'] !== null ? $r['fill_pct'] . '%' : 'N/A',
        ])->toArray();
        // Sheet 4: Monthly trend
        $months = ['', 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $trendRows = $data['monthlyTrend']->map(fn($t, $m) => ['Month' => $months[$m] ?? $m, 'Enquiries' => $t])->values()->toArray();
        // Sheet 5: Counsellor
        $counsellorRows = $data['byCounsellor']->map(fn($r) => [
            'Counsellor' => $r->assignedTo?->name ?? ('Staff #' . $r->assigned_to),
            'Total'      => $r->total,
            'Converted'  => $r->converted,
            'Rate %'     => $r->total > 0 ? round($r->converted / $r->total * 100, 1) . '%' : '0%',
        ])->toArray();
        // Sheet 6: YoY
        $yoyRows = [];
        if ($data['yoyData']) {
            $yoyRows = [
                ['Year' => $data['yoyData']['prev']['year'],    'Enquiries' => $data['yoyData']['prev']['enquiries'],    'Converted' => $data['yoyData']['prev']['converted']],
                ['Year' => $data['yoyData']['current']['year'], 'Enquiries' => $data['yoyData']['current']['enquiries'], 'Converted' => $data['yoyData']['current']['converted']],
            ];
        }

        // Combine into single sheet with sections
        $rows = [];
        $rows[] = ['=== SOURCE-WISE BREAKDOWN ===', '', '', ''];
        foreach ($sourceRows as $r) $rows[] = [$r['Source'], $r['Count'], '', ''];
        $rows[] = ['', '', '', ''];
        $rows[] = ['=== STATUS-WISE BREAKDOWN ===', '', '', ''];
        foreach ($statusRows as $r) $rows[] = [$r['Status'], $r['Count'], '', ''];
        $rows[] = ['', '', '', ''];
        $rows[] = ['=== CLASS vs SEAT CAPACITY ===', '', '', ''];
        $rows[] = ['Class', 'Enquiries', 'Seats', 'Fill %'];
        foreach ($seatRows as $r) $rows[] = array_values($r);
        $rows[] = ['', '', '', ''];
        $rows[] = ['=== MONTHLY TREND ===', '', '', ''];
        foreach ($trendRows as $r) $rows[] = [$r['Month'], $r['Enquiries'], '', ''];
        $rows[] = ['', '', '', ''];
        $rows[] = ['=== COUNSELLOR CONVERSION ===', '', '', ''];
        $rows[] = ['Counsellor', 'Total', 'Converted', 'Rate %'];
        foreach ($counsellorRows as $r) $rows[] = array_values($r);
        if ($yoyRows) {
            $rows[] = ['', '', '', ''];
            $rows[] = ['=== YEAR-OVER-YEAR ===', '', '', ''];
            $rows[] = ['Year', 'Enquiries', 'Converted', ''];
            foreach ($yoyRows as $r) $rows[] = [$r['Year'], $r['Enquiries'], $r['Converted'], ''];
        }

        $export = new ArrayExport($rows, ['Category / Item', 'Value', 'Value 2', 'Value 3']);
        return Excel::download($export, 'admission-analytics-' . now()->format('Y-m-d') . '.xlsx');
    }

    // ── Application Form Builder ──────────────────────────

    public function formBuilder(Request $request)
    {
        $years   = AcademicYear::orderByDesc('start_date')->get();
        $classes = Classes::active()->get();
        $configs = \App\Models\ApplicationFormConfig::with(['academicYear', 'class'])
            ->withCount('applications')
            ->orderByDesc('created_at')
            ->get();
        return view('admissions.form-builder', compact('years', 'classes', 'configs'));
    }

    public function storeFormConfig(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:200',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $fields = [];
        foreach (['student_name','dob','gender','nationality','religion','caste','category',
                  'parent_name','parent_mobile','parent_email','address','previous_school',
                  'siblings_in_school','annual_income'] as $f) {
            if ($request->boolean('field_' . $f)) {
                $fields[] = [
                    'name'     => $f,
                    'label'    => ucwords(str_replace('_', ' ', $f)),
                    'required' => $request->boolean('req_' . $f),
                    'type'     => in_array($f, ['dob']) ? 'date' : (in_array($f,['gender','category','religion','nationality']) ? 'select' : 'text'),
                ];
            }
        }

        $docFields = [];
        foreach (['birth_certificate','aadhaar_card','transfer_certificate','passport_photo',
                  'caste_certificate','address_proof','income_certificate'] as $d) {
            if ($request->boolean('doc_' . $d)) {
                $docFields[] = [
                    'name'     => $d,
                    'label'    => ucwords(str_replace('_', ' ', $d)),
                    'required' => $request->boolean('docreq_' . $d),
                    'types'    => ['pdf', 'jpg', 'jpeg', 'png'],
                    'max_mb'   => 5,
                ];
            }
        }

        \App\Models\ApplicationFormConfig::create([
            'title'            => $request->title,
            'description'      => $request->description,
            'academic_year_id' => $request->academic_year_id,
            'class_id'         => $request->class_id ?: null,
            'fields'           => $fields,
            'document_fields'  => $docFields,
            'application_fee'  => $request->application_fee ?? 0,
            'open_from'        => $request->open_from ?: null,
            'open_until'       => $request->open_until ?: null,
            'is_active'        => true,
        ]);

        return back()->with('success', 'Application form created successfully.');
    }

    public function toggleFormConfig(int $id)
    {
        $config = \App\Models\ApplicationFormConfig::findOrFail($id);
        $config->update(['is_active' => !$config->is_active]);
        return back()->with('success', 'Form ' . ($config->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function deleteFormConfig(int $id)
    {
        \App\Models\ApplicationFormConfig::findOrFail($id)->delete();
        return back()->with('success', 'Form configuration deleted.');
    }

    public function applications(Request $request)
    {
        $configs = \App\Models\ApplicationFormConfig::orderByDesc('created_at')->get();
        $apps    = \App\Models\StudentApplication::with(['formConfig.class', 'formConfig.academicYear'])
            ->when($request->form_config_id, fn($q, $v) => $q->where('form_config_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->when($request->search, fn($q, $v) => $q->where(function ($q2) use ($v) {
                $q2->where('student_name', 'like', "%$v%")
                   ->orWhere('application_number', 'like', "%$v%")
                   ->orWhere('parent_mobile', 'like', "%$v%");
            }))
            ->latest()
            ->paginate(20)->withQueryString();
        return view('admissions.applications', compact('apps', 'configs'));
    }

    public function updateApplicationStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:submitted,under_review,shortlisted,rejected,admitted']);
        \App\Models\StudentApplication::findOrFail($id)->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
        ]);
        return back()->with('success', 'Application status updated.');
    }

    public function applicationPdf(int $id)
    {
        $app    = \App\Models\StudentApplication::with('formConfig')->findOrFail($id);
        $school = SchoolSetting::first();
        $pdf    = Pdf::loadView('pdf.application-form', compact('app', 'school'));
        return $pdf->setPaper('A4', 'portrait')->download('application-' . $app->application_number . '.pdf');
    }

    // ── Public Application Form (unauthenticated) ─────────

    public function publicApplicationForm(string $token)
    {
        $config = \App\Models\ApplicationFormConfig::with(['academicYear', 'class'])
            ->where('link_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        if ($config->open_until && now()->gt($config->open_until)) {
            abort(410, 'This application form has closed.');
        }
        if ($config->open_from && now()->lt($config->open_from)) {
            abort(403, 'This application form is not yet open.');
        }

        return view('admissions.public-application', compact('config'));
    }

    public function submitPublicApplication(Request $request, string $token)
    {
        $config = \App\Models\ApplicationFormConfig::where('link_token', $token)->where('is_active', true)->firstOrFail();

        // Validate required fields from config
        $rules = ['parent_mobile' => 'required|string|max:20'];
        foreach (($config->fields ?? []) as $field) {
            if ($field['required'] ?? false) {
                $rules['fields.' . $field['name']] = 'required|string|max:500';
            }
        }
        $request->validate($rules);

        // Handle document uploads
        $documents = [];
        foreach (($config->document_fields ?? []) as $docField) {
            if ($request->hasFile('docs.' . $docField['name'])) {
                $path = $request->file('docs.' . $docField['name'])->store('applications/docs', 'public');
                $documents[$docField['name']] = $path;
            }
        }

        // Generate application number
        $yearPrefix = $config->academicYear?->start_date
            ? \Carbon\Carbon::parse($config->academicYear->start_date)->format('Y')
            : date('Y');
        $count  = \App\Models\StudentApplication::where('form_config_id', $config->id)->count();
        $appNum = 'APP-' . $yearPrefix . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        $formData = $request->input('fields', []);
        $appModel = \App\Models\StudentApplication::create([
            'application_number' => $appNum,
            'form_config_id'     => $config->id,
            'academic_year_id'   => $config->academic_year_id,
            'class_id'           => $config->class_id,
            'form_data'          => $formData,
            'documents'          => $documents,
            'student_name'       => $formData['student_name'] ?? null,
            'parent_name'        => $formData['parent_name'] ?? null,
            'parent_mobile'      => $request->parent_mobile,
            'parent_email'       => $request->input('fields.parent_email'),
            'status'             => 'submitted',
        ]);

        return redirect()->route('apply.success', $appModel->application_number);
    }

    public function applicationSuccess(string $number)
    {
        $app = \App\Models\StudentApplication::with('formConfig')->where('application_number', $number)->firstOrFail();
        return view('admissions.application-success', compact('app'));
    }
}
