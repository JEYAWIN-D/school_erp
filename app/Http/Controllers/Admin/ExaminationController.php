<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\GradingScheme;
use App\Models\GradingSchemeRange;
use App\Models\QuestionBank;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\ExamSchedule;
use App\Models\ActivityLearningRecord;
use App\Models\Competency;
use App\Models\CompetencyAssessment;
use App\Models\CoscholasticAssessment;
use App\Models\Student;
use App\Models\Subject;
use App\Exports\TabularMarksExport;
use App\Mail\ResultNotificationMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ExaminationController extends Controller
{
    public function index()
    {
        $currentYear = AcademicYear::current();
        $exams = \App\Models\Exam::with('academicYear')
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->orderBy('start_date')->get();

        $stats = [
            'total'     => $exams->count(),
            'upcoming'  => $exams->filter(fn($e) => \Carbon\Carbon::parse($e->start_date)->isFuture())->count(),
            'ongoing'   => $exams->filter(fn($e) => \Carbon\Carbon::parse($e->start_date)->isPast() && \Carbon\Carbon::parse($e->end_date)->isFuture())->count(),
            'results'   => $exams->where('result_published', true)->count(),
            'published' => $exams->where('is_published', true)->count(),
        ];

        // Upcoming exam schedules (next 7 days)
        $upcomingSchedules = DB::table('exam_schedules as es')
            ->join('exams as e', 'e.id', '=', 'es.exam_id')
            ->join('subjects as sub', 'sub.id', '=', 'es.subject_id')
            ->join('classes as c', 'c.id', '=', 'es.class_id')
            ->where('es.exam_date', '>=', today()->toDateString())
            ->where('es.exam_date', '<=', today()->addDays(7)->toDateString())
            ->when($currentYear, fn($q) => $q->where('e.academic_year_id', $currentYear->id))
            ->select('es.exam_date as date', 'es.start_time', 'es.end_time', 'sub.name as subject', 'c.name as class', 'e.name as exam_name')
            ->orderBy('es.exam_date')->orderBy('es.start_time')
            ->limit(8)->get();

        return view('examinations.index', compact('exams', 'currentYear', 'stats', 'upcomingSchedules'));
    }

    public function create()
    {
        $academicYear = AcademicYear::current();
        return view('examinations.create', compact('academicYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                     => 'required|string|max:100',
            'type'                     => 'required|in:unit_test,term,final,pre_board,practice',
            'start_date'               => 'required|date',
            'end_date'                 => 'required|date|after_or_equal:start_date',
            'passing_percentage'       => 'nullable|numeric|between:0,100',
            'best_of_n_subjects'       => 'nullable|integer|min:1|max:20',
            'term_label'               => 'nullable|string|max:30',
            'weightage_percent'        => 'nullable|numeric|between:0,100',
            'is_cumulative_component'  => 'nullable|boolean',
            'is_external'              => 'nullable|boolean',
            'conducting_body'          => 'nullable|string|max:100',
        ]);

        $currentYear = AcademicYear::current();
        $exam = \App\Models\Exam::create(array_merge($validated, [
            'academic_year_id'        => $currentYear?->id,
            'passing_percentage'      => $validated['passing_percentage'] ?? 33,
            'is_cumulative_component' => $request->boolean('is_cumulative_component'),
            'is_external'             => $request->boolean('is_external'),
        ]));

        return redirect()->route('examinations.index')
            ->with('success', 'Exam "' . $exam->name . '" created.');
    }

    public function edit(int $id)
    {
        $exam = \App\Models\Exam::findOrFail($id);
        $academicYear = AcademicYear::current();
        return view('examinations.edit', compact('exam', 'academicYear'));
    }

    public function update(Request $request, int $id)
    {
        $exam = \App\Models\Exam::findOrFail($id);
        $validated = $request->validate([
            'name'               => 'required|string|max:100',
            'type'               => 'required|in:unit_test,term,final,pre_board,practice',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'passing_percentage' => 'nullable|numeric|between:0,100',
            'term_label'         => 'nullable|string|max:30',
        ]);
        $exam->update(array_merge($validated, [
            'is_external' => $request->boolean('is_external'),
        ]));
        return redirect()->route('examinations.index')->with('success', 'Exam updated.');
    }

    public function destroy(int $id)
    {
        abort_unless(auth()->user()->can('delete examinations'), 403);
        $exam = \App\Models\Exam::findOrFail($id);
        if ($exam->schedules()->count() > 0) {
            return back()->with('error', 'Cannot delete exam with existing schedules. Remove schedules first.');
        }
        $exam->delete();
        return redirect()->route('examinations.index')->with('success', 'Exam deleted.');
    }

    public function marksImportTemplate()
    {
        $headers = ['student_name', 'admission_no', 'subject', 'obtained_marks', 'max_marks', 'is_absent'];
        $data    = [['Student Name', 'Admission No', 'Subject', 'Obtained Marks', 'Max Marks', 'Is Absent (0/1)']];
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ArrayExport($data),
            'marks-import-template.xlsx'
        );
    }

    public function marks(Request $request, int $id)
    {
        $exam    = \App\Models\Exam::with(['schedules.class', 'schedules.subject'])->findOrFail($id);
        $classes = Classes::active()->get();
        $enrollments = collect();
        $existingMarks = collect();
        $schedules = collect();

        if ($request->class_id) {
            $currentYear = AcademicYear::current();
            $enrollments = StudentEnrollment::with('student')
                ->where('class_id', $request->class_id)
                ->where('status', 'active')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->orderBy('roll_number')->get();

            $schedules = $exam->schedules->where('class_id', $request->class_id);

            if ($enrollments->isNotEmpty()) {
                $existingMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $exam->id))
                    ->whereIn('student_id', $enrollments->pluck('student_id'))
                    ->get()->keyBy(fn($m) => $m->exam_schedule_id . '_' . $m->student_id);
            }
        }

        return view('examinations.marks', compact('exam', 'classes', 'enrollments', 'schedules', 'existingMarks'));
    }

    public function lockMarks(Request $request, int $id)
    {
        $exam = \App\Models\Exam::findOrFail($id);
        $exam->update(['marks_locked' => true, 'marks_locked_at' => now(), 'marks_locked_by' => auth()->id()]);
        return back()->with('success', 'Marks locked. No further edits are allowed.');
    }

    public function unlockMarks(Request $request, int $id)
    {
        $exam = \App\Models\Exam::findOrFail($id);
        $exam->update(['marks_locked' => false, 'marks_locked_at' => null, 'marks_locked_by' => null]);
        return back()->with('success', 'Marks unlocked.');
    }

    public function saveMarks(Request $request, int $id)
    {
        $request->validate(['marks' => 'required|array']);
        $exam = \App\Models\Exam::findOrFail($id);
        if ($exam->marks_locked) {
            return back()->with('error', 'Marks are locked for this exam. Unlock first to make changes.');
        }

        DB::transaction(function () use ($request) {
            foreach ($request->marks as $scheduleId => $studentMarks) {
                foreach ($studentMarks as $studentId => $data) {
                    \App\Models\ExamMark::updateOrCreate(
                        ['exam_schedule_id' => $scheduleId, 'student_id' => $studentId],
                        [
                            'marks_obtained' => $data['marks'] ?? null,
                            'is_absent'      => isset($data['absent']) ? 1 : 0,
                            'grade'          => $data['grade'] ?? null,
                            'entered_by'     => auth()->id(),
                        ]
                    );
                }
            }
        });

        return back()->with('success', 'Marks saved.');
    }

    public function results(int $id)
    {
        $exam = \App\Models\Exam::with(['schedules.marks.student', 'schedules.subject', 'schedules.class'])->findOrFail($id);
        $classes  = Classes::active()->get();
        $subjects = Subject::orderBy('name')->get();
        return view('examinations.results', compact('exam', 'classes', 'subjects'));
    }

    public function storeSchedule(Request $request, int $id)
    {
        $exam = \App\Models\Exam::findOrFail($id);
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_date'  => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i|after:start_time',
            'max_marks'  => 'nullable|integer|min:1|max:1000',
            'min_marks'  => 'nullable|integer|min:0',
            'room_no'    => 'nullable|string|max:50',
        ]);

        ExamSchedule::create([
            'exam_id'    => $exam->id,
            'class_id'   => $request->class_id,
            'subject_id' => $request->subject_id,
            'exam_date'  => $request->exam_date,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'max_marks'  => $request->max_marks ?? 100,
            'pass_marks' => $request->min_marks ?? 35,
            'venue'      => $request->room_no,
        ]);

        return back()->with('success', 'Schedule added.');
    }

    public function destroySchedule(int $scheduleId)
    {
        abort_unless(auth()->user()->can('delete examinations'), 403);
        $schedule = ExamSchedule::findOrFail($scheduleId);
        if ($schedule->marks()->count() > 0) {
            return back()->with('error', 'Cannot delete schedule — marks have been entered for it.');
        }
        $schedule->delete();
        return back()->with('success', 'Schedule removed.');
    }

    public function reportCards(int $id, Request $request)
    {
        $exam = \App\Models\Exam::with(['schedules.marks.student', 'schedules.subject'])->findOrFail($id);
        $classes = Classes::active()->get();
        $enrollments = collect();
        if ($request->class_id) {
            $currentYear = AcademicYear::current();
            $q = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
            if ($request->section_id) $q->where('section_id', $request->section_id);
            if ($currentYear) $q->where('academic_year_id', $currentYear->id);
            $enrollments = $q->orderBy('roll_number')->get();
        }
        return view('examinations.report-cards', compact('exam', 'classes', 'enrollments'));
    }

    public function hallTickets(int $id)
    {
        $exam        = \App\Models\Exam::with('schedules.subject')->findOrFail($id);
        $classes     = Classes::active()->get();
        $sections    = collect();
        $enrollments = collect();
        if (request('class_id')) {
            $sections    = Section::where('class_id', request('class_id'))->get();
            $currentYear = AcademicYear::current();
            $q = StudentEnrollment::with('student')->where('class_id', request('class_id'))->where('status', 'active');
            if (request('section_id')) $q->where('section_id', request('section_id'));
            if ($currentYear) $q->where('academic_year_id', $currentYear->id);
            $enrollments = $q->get();
        }
        return view('examinations.hall-tickets', compact('exam', 'classes', 'sections', 'enrollments'));
    }

    public function hallTicketPdf(int $enrollmentId)
    {
        $enrollment  = StudentEnrollment::with(['student', 'class', 'section', 'academicYear'])->findOrFail($enrollmentId);
        $exam        = \App\Models\Exam::with('schedules.subject')->findOrFail(request('exam_id'));
        $school      = \App\Models\SchoolSetting::first();
        $student     = $enrollment->student;
        $academicYear = $enrollment->academicYear;
        $schedules   = $exam->schedules->where('class_id', $enrollment->class_id);

        $qrData = implode('|', array_filter([
            $student?->admission_number,
            $student?->full_name,
            $exam->name,
            $enrollment->class?->name,
            $enrollment->roll_number,
        ]));
        $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(100)->generate($qrData));

        $pdf = Pdf::loadView('pdf.hall-ticket', compact('school', 'exam', 'academicYear', 'student', 'enrollment', 'schedules', 'qrCode'));
        return $pdf->download('hall-ticket-' . $student?->admission_number . '.pdf');
    }

    public function hallTicketsBulk(int $id)
    {
        $exam = \App\Models\Exam::with('schedules.subject')->findOrFail($id);
        $classId   = request('class_id');
        $sectionId = request('section_id');
        if (!$classId) return back()->with('error', 'Select a class first.');

        $currentYear = AcademicYear::current();
        $q = StudentEnrollment::with(['student', 'class', 'section', 'academicYear'])
            ->where('class_id', $classId)->where('status', 'active');
        if ($sectionId) $q->where('section_id', $sectionId);
        if ($currentYear) $q->where('academic_year_id', $currentYear->id);
        $enrollments = $q->get();

        if ($enrollments->isEmpty()) return back()->with('error', 'No students found.');

        $school = \App\Models\SchoolSetting::first();
        $zip = new \ZipArchive();
        $zipPath = storage_path('app/tmp/hall-tickets-' . $id . '-' . time() . '.zip');
        @mkdir(dirname($zipPath), 0755, true);
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($enrollments as $enrollment) {
            try {
                $student = $enrollment->student;
                $schedules = $exam->schedules->where('class_id', $enrollment->class_id);
                $qrData = implode('|', array_filter([
                    $student?->admission_number,
                    $student?->full_name,
                    $exam->name,
                    $enrollment->class?->name,
                    $enrollment->roll_number,
                ]));
                $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(100)->generate($qrData));
                $pdfContent = Pdf::loadView('pdf.hall-ticket', compact(
                    'school', 'exam', 'student', 'enrollment', 'schedules', 'qrCode'
                ) + ['academicYear' => $enrollment->academicYear])->output();
                $zip->addFromString('hall-ticket-' . ($student?->admission_number ?? $enrollment->id) . '.pdf', $pdfContent);
            } catch (\Exception $e) {}
        }

        $zip->close();

        return response()->download($zipPath, 'hall-tickets-' . $exam->name . '.zip')->deleteFileAfterSend(true);
    }

    public function reportCardPdf(int $enrollmentId)
    {
        $enrollment   = StudentEnrollment::with(['student', 'class', 'section', 'academicYear'])->findOrFail($enrollmentId);
        $exam         = \App\Models\Exam::with('schedules.subject')->findOrFail(request('exam_id'));
        $school       = \App\Models\SchoolSetting::first() ?? (object)['school_name' => 'DASA EduERP', 'address' => ''];
        $gradingScheme = GradingScheme::where('is_default', true)->with('ranges')->first();
        $student      = $enrollment->student;
        $academicYear = $enrollment->academicYear;

        $allMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $exam->id))
            ->where('student_id', $enrollment->student_id)->with('examSchedule.subject')->get();
        $marks             = $allMarks->filter(fn($m) => !($m->examSchedule?->subject?->is_coscholastic));
        $coscholasticMarks = $allMarks->filter(fn($m) => $m->examSchedule?->subject?->is_coscholastic);

        $schedules  = $exam->schedules->where('class_id', $enrollment->class_id);
        $totalMarks = $schedules->filter(fn($s) => !($s->subject?->is_coscholastic))->sum('max_marks');
        $obtained   = $marks->sum('marks_obtained');
        $percentage = $totalMarks > 0 ? round($obtained / $totalMarks * 100, 2) : 0;
        $overallGrade = $gradingScheme?->gradeFor($percentage) ?? '—';
        $hasFail    = $marks->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35));
        $result     = $hasFail ? 'FAIL' : 'PASS';

        // Class rank
        $classStudentIds = StudentEnrollment::where('class_id', $enrollment->class_id)
            ->where('status', 'active')
            ->when($academicYear, fn($q) => $q->where('academic_year_id', $academicYear->id))
            ->pluck('student_id');
        $ranked = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $exam->id)
                ->whereHas('subject', fn($sq) => $sq->where('is_coscholastic', false)))
            ->whereIn('student_id', $classStudentIds)
            ->selectRaw('student_id, SUM(marks_obtained) as total')
            ->groupBy('student_id')->orderByDesc('total')->pluck('student_id')->values();
        $rank = $ranked->search($student->id) !== false ? $ranked->search($student->id) + 1 : '—';

        // Attendance for academic year
        $yearStart   = $academicYear?->start_date ?? now()->startOfYear();
        $yearEnd     = $academicYear?->end_date ?? now();
        $totalDays   = \App\Models\AttendanceRecord::where('student_id', $student->id)->whereBetween('date', [$yearStart, $yearEnd])->count();
        $presentDays = \App\Models\AttendanceRecord::where('student_id', $student->id)
            ->whereIn('status', ['present', 'half_day'])->whereBetween('date', [$yearStart, $yearEnd])->count();
        $attendancePct = $totalDays > 0 ? round($presentDays / $totalDays * 100, 1) : 0;

        // Cumulative marks across term components
        $cumulativeData  = collect();
        $cumulativeTotal = 0;
        if ($exam->is_cumulative_component) {
            $termExams = \App\Models\Exam::where('is_cumulative_component', true)->whereNotNull('weightage_percent')->get();
            foreach ($termExams as $te) {
                $teTotal = $te->schedules->where('class_id', $enrollment->class_id)
                    ->filter(fn($s) => !($s->subject?->is_coscholastic))->sum('max_marks');
                $teObtained = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $te->id))
                    ->where('student_id', $student->id)->sum('marks_obtained');
                $tePct = $teTotal > 0 ? round($teObtained / $teTotal * 100, 1) : 0;
                $cumulativeData->push([
                    'label'      => $te->term_label ?? $te->name,
                    'weightage'  => $te->weightage_percent,
                    'percentage' => $tePct,
                    'weighted'   => round($tePct * $te->weightage_percent / 100, 2),
                ]);
            }
            $cumulativeTotal = $cumulativeData->sum('weighted');
        }

        $teacherRemarks    = \App\Models\SchoolSetting::get('ct_remarks_class_' . $enrollment->class_id, null);
        $principalRemarks  = \App\Models\SchoolSetting::get('principal_remarks_exam_' . $exam->id, null)
                          ?? \App\Models\SchoolSetting::get('principal_remarks', null);

        // Embed signature & stamp as base64 for PDF (DomPDF requires inline data URIs)
        $signatureBase64 = null;
        $stampBase64     = null;
        $schoolRow = \App\Models\SchoolSetting::first();
        if ($schoolRow?->principal_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($schoolRow->principal_signature)) {
            $signatureBase64 = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($schoolRow->principal_signature));
        }
        if ($schoolRow?->school_stamp && \Illuminate\Support\Facades\Storage::disk('public')->exists($schoolRow->school_stamp)) {
            $stampBase64 = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($schoolRow->school_stamp));
        }

        // Per-subject data for SVG bar chart
        $subjectChart = $marks->map(function ($m) {
            $max = $m->examSchedule?->max_marks ?? 0;
            return [
                'name'   => \Illuminate\Support\Str::limit($m->examSchedule?->subject?->name ?? '—', 12),
                'pct'    => $max > 0 ? round($m->marks_obtained / $max * 100) : 0,
                'absent' => (bool) $m->is_absent,
                'pass'   => !$m->is_absent && $m->marks_obtained >= ($m->examSchedule?->pass_marks ?? 35),
            ];
        })->values();

        $pdf = Pdf::loadView('pdf.report-card', compact(
            'school', 'exam', 'academicYear', 'student', 'enrollment',
            'marks', 'coscholasticMarks', 'totalMarks', 'percentage',
            'overallGrade', 'result', 'rank', 'attendancePct',
            'teacherRemarks', 'principalRemarks', 'cumulativeData', 'cumulativeTotal',
            'totalDays', 'presentDays', 'signatureBase64', 'stampBase64', 'subjectChart'
        ));
        return $pdf->download('report-card-' . $student?->admission_number . '.pdf');
    }

    public function savePrincipalRemarks(Request $request, int $id)
    {
        $request->validate(['principal_remarks' => 'nullable|string|max:500']);
        \App\Models\SchoolSetting::set('principal_remarks_exam_' . $id, $request->principal_remarks ?? '');
        return back()->with('success', 'Principal remarks saved.');
    }

    public function grading()
    {
        $schemes = GradingScheme::with('ranges')->orderBy('name')->get();
        return view('examinations.grading', compact('schemes'));
    }

    public function storeGrading(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'type'              => 'required|in:percentage,gpa',
            'pass_percentage'   => 'nullable|numeric|min:0|max:100',
            'is_default'        => 'nullable',
        ]);
        if (!empty($validated['is_default'])) {
            GradingScheme::query()->update(['is_default' => false]);
        }
        GradingScheme::create(array_merge($validated, ['is_default' => !empty($request->is_default)]));
        return back()->with('success', 'Grading scheme created.');
    }

    public function editGrading(int $id)
    {
        $scheme = GradingScheme::with('ranges')->findOrFail($id);
        return view('examinations.grading-edit', compact('scheme'));
    }

    public function updateGrading(Request $request, int $id)
    {
        $scheme = GradingScheme::findOrFail($id);
        $scheme->update($request->only(['name', 'type', 'pass_percentage']));
        if ($request->filled('ranges')) {
            $scheme->ranges()->delete();
            foreach ($request->ranges as $r) {
                $scheme->ranges()->create($r);
            }
        }
        return back()->with('success', 'Grading scheme updated.');
    }

    public function setDefaultGrading(int $id)
    {
        GradingScheme::query()->update(['is_default' => false]);
        GradingScheme::findOrFail($id)->update(['is_default' => true]);
        return back()->with('success', 'Default grading scheme updated.');
    }

    public function tabulation(Request $request)
    {
        $exams    = \App\Models\Exam::orderByDesc('id')->get();
        $classes  = Classes::active()->get();
        $sections = Section::all();
        $enrollments = collect();
        $subjects    = collect();
        $marksMap    = [];
        $ranks       = [];
        $gradingScheme = GradingScheme::where('is_default', true)->with('ranges')->first();

        if ($request->exam_id && $request->class_id) {
            $exam = \App\Models\Exam::with('schedules.subject')->findOrFail($request->exam_id);
            $subjects = $exam->schedules->where('class_id', $request->class_id)->map(fn($s) => tap($s->subject, fn($sub) => $sub->pivot = $s));
            $currentYear = AcademicYear::current();
            $q = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
            if ($request->section_id) $q->where('section_id', $request->section_id);
            if ($currentYear) $q->where('academic_year_id', $currentYear->id);
            $enrollments = $q->get();

            $allMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $request->exam_id))
                ->whereIn('student_id', $enrollments->pluck('student_id'))->get();
            foreach ($allMarks as $m) {
                $marksMap[$m->student_id][$m->examSchedule?->subject_id] = $m;
            }

            $bestN = $exam->best_of_n_subjects;
            $totals = $enrollments->mapWithKeys(function ($e) use ($marksMap, $bestN) {
                $studentMarks = collect($marksMap[$e->student_id] ?? [])
                    ->map(fn($m) => (float) $m->marks_obtained)
                    ->filter(fn($v) => $v > 0);
                if ($bestN && $studentMarks->count() > $bestN) {
                    $studentMarks = $studentMarks->sortDesc()->take($bestN);
                }
                return [$e->id => $studentMarks->sum()];
            });
            $sorted = $totals->sortDesc()->values();
            $enrollments->each(function ($e) use ($sorted, $totals, &$ranks) {
                $ranks[$e->id] = $sorted->search($totals[$e->id]) + 1;
            });
            $bestN = $exam->best_of_n_subjects;
        }

        $examObj = isset($exam) ? $exam : null;
        return view('examinations.tabulation', compact('exams', 'classes', 'sections', 'enrollments', 'subjects', 'marksMap', 'ranks', 'gradingScheme', 'examObj'));
    }

    public function tabulationExport(Request $request)
    {
        return Excel::download(new TabularMarksExport($request->exam_id, $request->class_id), 'tabulation.xlsx');
    }

    public function questionBank(Request $request)
    {
        $subjects  = Subject::orderBy('name')->get();
        $classes   = Classes::active()->get();
        $questions = QuestionBank::with(['subject', 'class'])
            ->when($request->subject_id, fn($q, $v) => $q->where('subject_id', $v))
            ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
            ->when($request->question_type, fn($q, $v) => $q->where('question_type', $v))
            ->when($request->difficulty, fn($q, $v) => $q->where('difficulty', $v))
            ->latest()->paginate(20)->withQueryString();
        return view('examinations.question-bank', compact('questions', 'subjects', 'classes'));
    }

    public function storeQuestion(Request $request)
    {
        $validated = $request->validate([
            'subject_id'    => 'required|exists:subjects,id',
            'question_text' => 'required|string',
            'question_type' => 'required|in:mcq,short_answer,long_answer,true_false,fill_blank',
            'difficulty'    => 'required|in:easy,medium,hard',
            'marks'         => 'required|numeric|min:0.5',
        ]);
        $options = null;
        if ($request->question_type === 'mcq' && $request->options_text) {
            $lines = array_filter(array_map('trim', explode("\n", $request->options_text)));
            $options = collect($lines)->map(fn($l) => ['text' => ltrim($l, '*'), 'correct' => str_starts_with($l, '*')])->all();
            $options = json_encode($options);
        }
        QuestionBank::create(array_merge($validated, [
            'class_id' => $request->class_id,
            'chapter'  => $request->chapter,
            'options'  => $options,
            'answer'   => $request->answer,
            'created_by' => auth()->id(),
        ]));
        return back()->with('success', 'Question added to bank.');
    }

    public function editQuestion(int $id)
    {
        $question = QuestionBank::findOrFail($id);
        $subjects = Subject::orderBy('name')->get();
        $classes  = Classes::active()->get();
        return view('examinations.question-edit', compact('question', 'subjects', 'classes'));
    }

    public function updateQuestion(Request $request, int $id)
    {
        QuestionBank::findOrFail($id)->update($request->only(['subject_id', 'class_id', 'question_text', 'question_type', 'difficulty', 'marks', 'chapter', 'answer']));
        return back()->with('success', 'Question updated.');
    }

    public function deleteQuestion(int $id)
    {
        QuestionBank::findOrFail($id)->delete();
        return back()->with('success', 'Question deleted.');
    }

    public function bulkReportCards(Request $request)
    {
        $request->validate(['exam_id' => 'required|exists:exams,id', 'class_id' => 'required|exists:classes,id']);

        $exam        = \App\Models\Exam::with('schedules.subject')->findOrFail($request->exam_id);
        $currentYear = AcademicYear::current();
        $q = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
        if ($request->section_id) $q->where('section_id', $request->section_id);
        if ($currentYear) $q->where('academic_year_id', $currentYear->id);
        $enrollments = $q->get();

        $gradingScheme = GradingScheme::where('is_default', true)->with('ranges')->first();
        $school = \App\Models\SchoolSetting::first();

        $reportData = $enrollments->map(function ($enrollment) use ($exam, $gradingScheme) {
            $marks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $exam->id))
                ->where('student_id', $enrollment->student_id)
                ->with('examSchedule.subject')->get();
            $totalMarks = $exam->schedules->where('class_id', $enrollment->class_id)->sum('max_marks');
            $obtained   = $marks->sum('marks_obtained');
            $percentage = $totalMarks > 0 ? round($obtained / $totalMarks * 100, 2) : 0;
            $hasFail    = $marks->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35));
            return compact('enrollment', 'marks', 'totalMarks', 'obtained', 'percentage', 'hasFail');
        });

        $pdf = Pdf::loadView('pdf.bulk-report-cards', compact('exam', 'reportData', 'gradingScheme', 'school'))->setPaper('a4');
        return $pdf->download('report-cards-' . \Illuminate\Support\Str::slug($exam->name) . '.pdf');
    }

    public function cgpaReport(Request $request)
    {
        $exams   = \App\Models\Exam::orderByDesc('id')->get();
        $classes = Classes::active()->get();
        $report  = collect();
        $exam    = null;

        if ($request->exam_id && $request->class_id) {
            $exam    = \App\Models\Exam::with('schedules.subject')->findOrFail($request->exam_id);
            $scheme  = GradingScheme::where('is_default', true)->with('ranges')->first();

            $currentYear = AcademicYear::current();
            $q = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
            if ($currentYear) $q->where('academic_year_id', $currentYear->id);
            $enrollments = $q->get();

            foreach ($enrollments as $enrollment) {
                $marks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $exam->id))
                    ->where('student_id', $enrollment->student_id)
                    ->with('examSchedule')
                    ->get()
                    ->filter(fn($m) => !$m->is_absent && !($m->examSchedule?->subject?->is_coscholastic));

                $totalPoints   = 0;
                $subjectCount  = 0;
                $subjectDetail = [];
                foreach ($marks as $m) {
                    $maxMark = $m->examSchedule?->max_marks ?? 100;
                    $pct     = $maxMark > 0 ? ($m->marks_obtained / $maxMark * 100) : 0;
                    $range   = $scheme?->ranges->filter(fn($r) => $pct >= $r->min_marks && $pct <= $r->max_marks)->first();
                    $gp      = $range?->gpa_points ?? 0;
                    $totalPoints += $gp;
                    $subjectCount++;
                    $subjectDetail[] = [
                        'subject' => $m->examSchedule?->subject?->name,
                        'marks'   => $m->marks_obtained,
                        'max'     => $maxMark,
                        'grade'   => $range?->grade ?? '—',
                        'gp'      => $gp,
                    ];
                }
                $cgpa = $subjectCount > 0 ? round($totalPoints / $subjectCount, 2) : 0;
                $report->push([
                    'student'  => $enrollment->student,
                    'subjects' => $subjectDetail,
                    'cgpa'     => $cgpa,
                    'count'    => $subjectCount,
                ]);
            }
            $report = $report->sortByDesc('cgpa');
        }

        return view('examinations.cgpa-report', compact('exams', 'classes', 'report', 'exam'));
    }

    public function classResultSummary(Request $request)
    {
        $exams   = \App\Models\Exam::orderByDesc('id')->get();
        $classes = Classes::active()->get();
        $summary = collect();

        if ($request->exam_id && $request->class_id) {
            $exam = \App\Models\Exam::with('schedules.subject')->findOrFail($request->exam_id);
            $currentYear = AcademicYear::current();
            $q = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
            if ($currentYear) $q->where('academic_year_id', $currentYear->id);
            $enrollments = $q->get();

            $allMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $request->exam_id))
                ->whereIn('student_id', $enrollments->pluck('student_id'))->get();

            $summary = $enrollments->map(function ($enrollment) use ($allMarks, $exam) {
                $marks      = $allMarks->where('student_id', $enrollment->student_id);
                $totalMarks = $exam->schedules->where('class_id', $enrollment->class_id)->sum('max_marks');
                $obtained   = $marks->sum('marks_obtained');
                $percentage = $totalMarks > 0 ? round($obtained / $totalMarks * 100, 1) : 0;
                $hasFail    = $marks->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35));
                return ['student' => $enrollment->student, 'obtained' => $obtained, 'totalMarks' => $totalMarks, 'percentage' => $percentage, 'hasFail' => $hasFail];
            })->sortByDesc('percentage');
        }

        return view('examinations.class-result-summary', compact('exams', 'classes', 'summary'));
    }

    public function failedStudents(Request $request)
    {
        $exams   = \App\Models\Exam::orderByDesc('id')->get();
        $classes = Classes::active()->get();
        $failed  = collect();

        $exam      = null;
        $threshold = 2;
        if ($request->exam_id) {
            $exam = \App\Models\Exam::findOrFail($request->exam_id);
            $threshold = $exam->supplementary_threshold ?? 2;
            $currentYear = AcademicYear::current();
            $failedMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $request->exam_id))
                ->where(fn($q) => $q->whereRaw('marks_obtained < COALESCE((SELECT pass_marks FROM exam_schedules WHERE id = exam_schedule_id), 35)'))
                ->whereHas('examSchedule.class', fn($q) => $request->class_id ? $q->where('class_id', $request->class_id) : $q)
                ->with(['student', 'examSchedule.subject'])
                ->get()
                ->groupBy('student_id');

            $failed = $failedMarks->map(fn($marks) => [
                'student'          => $marks->first()->student,
                'subjects'         => $marks->map(fn($m) => $m->examSchedule?->subject?->name)->filter(),
                'supp_eligible'    => $marks->count() <= $threshold,
            ]);
        }

        return view('examinations.failed-students', compact('exams', 'classes', 'failed', 'threshold'));
    }

    public function tabulationPdf(Request $request)
    {
        $request->validate(['exam_id' => 'required', 'class_id' => 'required']);
        // Reuse tabulation data
        $exam = \App\Models\Exam::with('schedules.subject')->findOrFail($request->exam_id);
        $class = Classes::findOrFail($request->class_id);
        $subjects = $exam->schedules->where('class_id', $request->class_id)->map(fn($s) => tap($s->subject, fn($sub) => $sub->pivot = $s));
        $currentYear = AcademicYear::current();
        $q = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
        if ($currentYear) $q->where('academic_year_id', $currentYear->id);
        $enrollments = $q->get();
        $allMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $request->exam_id))
            ->whereIn('student_id', $enrollments->pluck('student_id'))->get();
        $marksMap = [];
        foreach ($allMarks as $m) { $marksMap[$m->student_id][$m->examSchedule?->subject_id] = $m; }
        $totals = $enrollments->mapWithKeys(fn($e) => [$e->id => collect($marksMap[$e->student_id] ?? [])->sum('marks_obtained')]);
        $sorted = $totals->sortDesc()->values();
        $ranks = [];
        $enrollments->each(fn($e) => $ranks[$e->id] = $sorted->search($totals[$e->id]) + 1);
        $school = \App\Models\SchoolSetting::first();
        $gradingScheme = GradingScheme::where('is_default', true)->with('ranges')->first();
        $pdf = Pdf::loadView('pdf.tabulation', compact('exam', 'class', 'subjects', 'enrollments', 'marksMap', 'totals', 'ranks', 'gradingScheme', 'school'))->setPaper('a3', 'landscape');
        return $pdf->download('tabulation-' . $class->name . '.pdf');
    }

    public function subjectPerformance(Request $request)
    {
        $exams   = \App\Models\Exam::with('schedules.subject')->latest()->get();
        $classes = Classes::orderBy('sort_order')->get();
        $data    = [];
        $exam    = null;
        $class   = null;
        if ($request->exam_id && $request->class_id) {
            $exam    = \App\Models\Exam::with('schedules.subject')->findOrFail($request->exam_id);
            $class   = Classes::findOrFail($request->class_id);
            $subjects = $exam->schedules->where('class_id', $request->class_id);
            $currentYear = AcademicYear::current();
            $enrollments = StudentEnrollment::where('class_id', $request->class_id)
                ->where('status', 'active')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->pluck('student_id');
            foreach ($subjects as $schedule) {
                $marks = ExamMark::where('exam_schedule_id', $schedule->id)
                    ->whereIn('student_id', $enrollments)
                    ->whereNotNull('marks_obtained')
                    ->pluck('marks_obtained');
                $passed = ExamMark::where('exam_schedule_id', $schedule->id)
                    ->whereIn('student_id', $enrollments)
                    ->where('marks_obtained', '>=', $schedule->pass_marks)
                    ->count();
                $total  = $marks->count();
                $data[] = [
                    'subject'    => $schedule->subject->name ?? 'N/A',
                    'max_marks'  => $schedule->max_marks,
                    'pass_marks' => $schedule->pass_marks,
                    'total'      => $total,
                    'passed'     => $passed,
                    'failed'     => $total - $passed,
                    'pass_pct'   => $total > 0 ? round(($passed / $total) * 100, 1) : 0,
                    'avg'        => $total > 0 ? round($marks->avg(), 1) : 0,
                    'highest'    => $total > 0 ? $marks->max() : 0,
                    'lowest'     => $total > 0 ? $marks->min() : 0,
                ];
            }
        }
        return view('examinations.subject-performance', compact('exams', 'classes', 'data', 'exam', 'class'));
    }

    public function studentResultHistory(Request $request)
    {
        $students = collect();
        $history  = collect();
        $student  = null;
        if ($request->student_id) {
            $student  = \App\Models\Student::findOrFail($request->student_id);
            $studentId = $student->id;
            $exams = \App\Models\Exam::with(['schedules' => fn($q) => $q->where('class_id', function($sub) use ($studentId) {
                $sub->select('class_id')->from('student_enrollments')->where('student_id', $studentId)->where('status', 'active')->limit(1);
            })])->latest()->get();
            foreach ($exams as $exam) {
                $marks = ExamMark::where('student_id', $studentId)
                    ->whereHas('examSchedule', fn($q) => $q->where('exam_id', $exam->id))
                    ->with('examSchedule.subject')
                    ->get();
                if ($marks->isEmpty()) continue;
                $total   = $marks->sum('marks_obtained');
                $maxTotal = $marks->sum(fn($m) => $m->examSchedule?->max_marks ?? 0);
                $history[] = [
                    'exam'     => $exam,
                    'marks'    => $marks,
                    'total'    => $total,
                    'max'      => $maxTotal,
                    'pct'      => $maxTotal > 0 ? round(($total / $maxTotal) * 100, 1) : 0,
                    'passed'   => $marks->every(fn($m) => $m->marks_obtained >= ($m->examSchedule?->pass_marks ?? 0)),
                ];
            }
        }
        if ($request->ajax() || $request->search) {
            $students = \App\Models\Student::where('first_name', 'like', "%{$request->search}%")
                ->orWhere('last_name', 'like', "%{$request->search}%")
                ->orWhere('admission_no', 'like', "%{$request->search}%")
                ->limit(20)->get();
            if ($request->ajax()) return response()->json($students);
        }
        return view('examinations.student-result-history', compact('student', 'history', 'students'));
    }

    // ── Marks Entry Progress Indicator ────────────────────

    public function marksProgress(Request $request)
    {
        $exams   = \App\Models\Exam::with('schedules')->latest()->get();
        $classes = Classes::active()->get();
        $data    = collect();

        if ($request->exam_id) {
            $exam = \App\Models\Exam::with('schedules.class')->findOrFail($request->exam_id);
            foreach ($classes as $cls) {
                $schedules = $exam->schedules->where('class_id', $cls->id);
                if ($schedules->isEmpty()) continue;

                $year = AcademicYear::current();
                $totalStudents = StudentEnrollment::where('class_id', $cls->id)
                    ->where('status', 'active')
                    ->when($year, fn($q) => $q->where('academic_year_id', $year->id))
                    ->count();

                foreach ($schedules as $schedule) {
                    $entered = ExamMark::where('exam_schedule_id', $schedule->id)->count();
                    $pct     = $totalStudents > 0 ? round(($entered / $totalStudents) * 100) : 0;
                    $data->push([
                        'class'    => $cls->name,
                        'subject'  => $schedule->subject?->name ?? '—',
                        'date'     => $schedule->exam_date,
                        'total'    => $totalStudents,
                        'entered'  => $entered,
                        'pct'      => $pct,
                    ]);
                }
            }
        }

        return view('examinations.marks-progress', compact('exams', 'classes', 'data'));
    }

    // ── Merit Scholarship Auto-Apply ──────────────────────

    public function applyMeritScholarships(Request $request)
    {
        $request->validate(['exam_id' => 'required|exists:exams,id', 'class_id' => 'required|exists:classes,id']);

        $exam    = \App\Models\Exam::with('schedules')->findOrFail($request->exam_id);
        $classId = $request->class_id;
        $year    = AcademicYear::current();

        // Get merit-type scholarship schemes
        $schemes = \App\Models\ScholarshipScheme::where('is_active', true)
            ->where('criteria_type', 'merit')
            ->whereNotNull('marks_threshold')
            ->get();

        if ($schemes->isEmpty()) {
            return back()->with('error', 'No active merit-based scholarship schemes configured.');
        }

        $enrollments = StudentEnrollment::with('student')
            ->where('class_id', $classId)->where('status', 'active')
            ->when($year, fn($q) => $q->where('academic_year_id', $year->id))->get();

        $totalMarks = $exam->schedules->where('class_id', $classId)->sum('max_marks');
        if ($totalMarks <= 0) {
            return back()->with('error', 'No exam schedules with marks found for this class.');
        }

        $applied = 0;
        foreach ($enrollments as $enrollment) {
            $obtained   = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $exam->id))
                ->where('student_id', $enrollment->student_id)->sum('marks_obtained');
            $percentage = round(($obtained / $totalMarks) * 100, 2);

            foreach ($schemes as $scheme) {
                if ($percentage >= $scheme->marks_threshold) {
                    $exists = \App\Models\StudentConcession::where('student_id', $enrollment->student_id)
                        ->where('scholarship_id', $scheme->id)
                        ->where('academic_year_id', $year?->id)->exists();
                    if (!$exists) {
                        \App\Models\StudentConcession::create([
                            'student_id'           => $enrollment->student_id,
                            'scholarship_id'       => $scheme->id,
                            'academic_year_id'     => $year?->id,
                            'concession_type'      => 'scholarship',
                            'value_type'           => 'percentage',
                            'value'                => $scheme->value,
                            'applicable_fee_heads' => $scheme->applicable_fee_heads,
                            'valid_from'           => $year?->start_date ?? now(),
                            'valid_to'             => $year?->end_date ?? now()->addYear(),
                            'granted_by'           => auth()->id(),
                            'remarks'              => "Merit: {$percentage}% in {$exam->name}",
                        ]);
                        $applied++;
                    }
                }
            }
        }

        return back()->with('success', "{$applied} merit scholarship(s) applied.");
    }

    // ── Exam-based Promotion Eligibility ──────────────────

    public function promotionEligibility(Request $request)
    {
        $exams    = \App\Models\Exam::orderByDesc('id')->get();
        $classes  = Classes::active()->get();
        $eligible = collect();
        $ineligible = collect();

        if ($request->exam_id && $request->class_id) {
            $exam = \App\Models\Exam::with('schedules')->findOrFail($request->exam_id);
            $year = AcademicYear::current();
            $enrollments = StudentEnrollment::with('student')
                ->where('class_id', $request->class_id)->where('status', 'active')
                ->when($year, fn($q) => $q->where('academic_year_id', $year->id))->get();

            foreach ($enrollments as $enrollment) {
                $marks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $request->exam_id))
                    ->where('student_id', $enrollment->student_id)->with('examSchedule')->get();
                $hasFail = $marks->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35));
                if ($hasFail) {
                    $ineligible->push($enrollment->student);
                } else {
                    $eligible->push($enrollment->student);
                }
            }
        }

        return view('examinations.promotion-eligibility', compact('exams', 'classes', 'eligible', 'ineligible'));
    }

    // ── Grace marks save for an exam schedule ─────────────

    public function saveGraceMarks(Request $request, int $id)
    {
        $exam = \App\Models\Exam::findOrFail($id);
        $request->validate(['grace' => 'required|array', 'grace.*' => 'integer|min:0|max:20']);
        foreach ($request->grace as $scheduleId => $graceMarks) {
            \App\Models\ExamSchedule::where('id', $scheduleId)->where('exam_id', $id)
                ->update(['grace_marks' => $graceMarks]);
        }
        return back()->with('success', 'Grace marks updated.');
    }

    // ── Marks Recheck Request Workflow ────────────────────

    public function recheckRequests(Request $request)
    {
        $exams   = \App\Models\Exam::latest()->get();
        $requests = \App\Models\MarksRecheckRequest::with(['exam', 'student', 'examSchedule.subject'])
            ->when($request->exam_id,  fn($q, $v) => $q->where('exam_id', $v))
            ->when($request->status,   fn($q, $v) => $q->where('status', $v))
            ->latest()->paginate(25);
        return view('examinations.recheck-requests', compact('requests', 'exams'));
    }

    public function storeRecheckRequest(Request $request)
    {
        $request->validate([
            'exam_id'          => 'required|exists:exams,id',
            'student_id'       => 'required|exists:students,id',
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'reason'           => 'nullable|string',
        ]);
        $mark = ExamMark::where('student_id', $request->student_id)
            ->where('exam_schedule_id', $request->exam_schedule_id)->first();
        \App\Models\MarksRecheckRequest::create([
            'exam_id'          => $request->exam_id,
            'student_id'       => $request->student_id,
            'exam_schedule_id' => $request->exam_schedule_id,
            'marks_before'     => $mark?->marks_obtained,
            'reason'           => $request->reason,
            'status'           => 'pending',
            'requested_by'     => auth()->id(),
        ]);
        return back()->with('success', 'Recheck request submitted.');
    }

    public function processRecheckRequest(Request $request, int $id)
    {
        $request->validate([
            'action'        => 'required|in:under_review,revised,rejected',
            'admin_remarks' => 'nullable|string',
            'marks_after'   => 'nullable|numeric|min:0',
        ]);
        $recheck = \App\Models\MarksRecheckRequest::with('examSchedule')->findOrFail($id);
        $recheck->update([
            'status'        => $request->action,
            'admin_remarks' => $request->admin_remarks,
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
            'marks_after'   => $request->marks_after,
        ]);
        // If revised, update the actual mark
        if ($request->action === 'revised' && $request->marks_after !== null) {
            ExamMark::where('student_id', $recheck->student_id)
                ->where('exam_schedule_id', $recheck->exam_schedule_id)
                ->update(['marks_obtained' => $request->marks_after, 'entered_by' => auth()->id()]);
        }
        return back()->with('success', 'Recheck request ' . $request->action . '.');
    }

    // ── Marks Import via Excel ────────────────────────────

    public function marksImport(Request $request)
    {
        $exams   = \App\Models\Exam::with('schedules.subject')->latest()->get();
        $classes = Classes::active()->get();
        return view('examinations.marks-import', compact('exams', 'classes'));
    }

    public function cumulativeMarks(Request $request)
    {
        $currentYear = AcademicYear::current();
        $classes     = Classes::active()->get();

        $termExams = Exam::where('academic_year_id', $currentYear?->id)
            ->where('is_cumulative_component', true)
            ->orderBy('start_date')
            ->get();

        $result = collect();

        if ($request->class_id && $termExams->count()) {
            $enrollments = \App\Models\StudentEnrollment::with('student')
                ->where('class_id', $request->class_id)
                ->where('status', 'active')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->get();

            $totalWeight = $termExams->sum('weightage_percent') ?: 100;

            $result = $enrollments->map(function ($enrollment) use ($termExams, $totalWeight) {
                $termScores = [];
                $weightedTotal = 0;

                foreach ($termExams as $exam) {
                    $schedules = \App\Models\ExamSchedule::where('exam_id', $exam->id)
                        ->where('class_id', $enrollment->class_id)->get();
                    $scheduleIds = $schedules->pluck('id');
                    $marks = \App\Models\ExamMark::whereIn('exam_schedule_id', $scheduleIds)
                        ->where('student_id', $enrollment->student_id)
                        ->get();
                    $maxTotal = $schedules->sum('max_marks');
                    $obtained = $marks->sum('marks_obtained');
                    $pct      = $maxTotal > 0 ? ($obtained / $maxTotal * 100) : 0;
                    $weighted = $maxTotal > 0 ? ($pct * (($exam->weightage_percent ?? 0) / $totalWeight)) : 0;

                    $termScores[$exam->id] = ['pct' => round($pct, 1), 'weighted' => round($weighted, 2)];
                    $weightedTotal += $weighted;
                }

                return [
                    'student'       => $enrollment->student,
                    'term_scores'   => $termScores,
                    'cumulative'    => round($weightedTotal, 2),
                ];
            })->sortByDesc('cumulative')->values();
        }

        return view('examinations.cumulative-marks', compact('termExams', 'classes', 'result', 'currentYear'));
    }

    public function hallTicketBlocks(Request $request)
    {
        $exams = Exam::with('academicYear')->orderBy('start_date', 'desc')->get();
        $blocked = collect();

        if ($request->exam_id) {
            $exam = Exam::findOrFail($request->exam_id);
            $currentYear = AcademicYear::current();

            $enrollments = \App\Models\StudentEnrollment::with(['student', 'class', 'section'])
                ->where('status', 'active')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->get();

            $blockedReasons = [];

            if ($request->boolean('block_fee_defaulters', true)) {
                try {
                    $defaulterIds = \Illuminate\Support\Facades\DB::table('student_fee_charges')
                        ->whereRaw('amount > 0')
                        ->whereNotIn('student_id', function ($sub) {
                            $sub->select('student_id')->from('fee_payments')
                                ->where('is_cancelled', false);
                        })
                        ->pluck('student_id')->unique();
                } catch (\Exception $e) {
                    $defaulterIds = collect();
                }
                foreach ($enrollments as $e) {
                    if ($defaulterIds->contains($e->student_id)) {
                        $blockedReasons[$e->student_id][] = 'Fee Defaulter';
                    }
                }
            }

            if ($request->boolean('block_attendance_shortage', true)) {
                $minPct = (float) \App\Models\SchoolSetting::get('min_attendance_percent', 75);
                $attendanceSummary = \App\Models\AttendanceRecord::where('academic_year_id', $currentYear?->id)
                    ->selectRaw("student_id, COUNT(*) as total, SUM(CASE WHEN status='present' THEN 1 WHEN status='late' THEN 1 WHEN status='half_day' THEN 0.5 ELSE 0 END) as present_count")
                    ->groupBy('student_id')
                    ->get()->keyBy('student_id');

                foreach ($enrollments as $e) {
                    $att = $attendanceSummary[$e->student_id] ?? null;
                    if ($att && $att->total > 0) {
                        $pct = ($att->present_count / $att->total) * 100;
                        if ($pct < $minPct) {
                            $blockedReasons[$e->student_id][] = sprintf('Attendance %.1f%% (min %s%%)', $pct, $minPct);
                        }
                    }
                }
            }

            $blocked = $enrollments->filter(fn($e) => isset($blockedReasons[$e->student_id]))
                ->map(function ($e) use ($blockedReasons) {
                    $e->block_reasons = $blockedReasons[$e->student_id];
                    return $e;
                });
        }

        return view('examinations.hall-ticket-blocks', compact('exams', 'blocked'));
    }

    public function coscholasticEntry(Request $request, int $examId)
    {
        $exam     = Exam::findOrFail($examId);
        $classes  = Classes::active()->get();
        $entries  = collect();
        $students = collect();
        $subjects = collect();

        if ($request->class_id) {
            $currentYear = AcademicYear::current();
            $enrollments = \App\Models\StudentEnrollment::with('student')
                ->where('class_id', $request->class_id)
                ->where('status', 'active')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->get();
            $students = $enrollments->pluck('student')->filter();

            $subjects = \App\Models\ExamSchedule::where('exam_id', $examId)
                ->where('class_id', $request->class_id)
                ->whereHas('subject', fn($q) => $q->where('is_coscholastic', true))
                ->with('subject')->get();

            if ($students->count() && $subjects->count()) {
                $entries = \App\Models\ExamMark::whereIn('exam_schedule_id', $subjects->pluck('id'))
                    ->whereIn('student_id', $students->pluck('id'))
                    ->get()
                    ->groupBy('student_id');
            }
        }

        return view('examinations.coscholastic-entry', compact('exam', 'classes', 'students', 'subjects', 'entries'));
    }

    public function saveCoscholasticGrades(Request $request, int $examId)
    {
        $request->validate(['class_id' => 'required', 'grades' => 'required|array']);
        $exam = Exam::findOrFail($examId);
        if ($exam->marks_locked) {
            return back()->with('error', 'Marks are locked. Unlock first.');
        }
        DB::transaction(function () use ($request) {
            foreach ($request->grades as $studentId => $subjectGrades) {
                foreach ($subjectGrades as $scheduleId => $grade) {
                    \App\Models\ExamMark::updateOrCreate(
                        ['exam_schedule_id' => $scheduleId, 'student_id' => $studentId],
                        ['grade' => $grade, 'entered_by' => Auth::id()]
                    );
                }
            }
        });
        return back()->with('success', 'Co-scholastic grades saved.');
    }

    public function processMarksImport(Request $request)
    {
        $request->validate([
            'exam_id'   => 'required|exists:exams,id',
            'class_id'  => 'required|exists:classes,id',
            'file'      => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        $exam    = \App\Models\Exam::findOrFail($request->exam_id);
        $classId = $request->class_id;

        $rows    = \Maatwebsite\Excel\Facades\Excel::toArray(
            new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array): array { return $array; }
            },
            $request->file('file')
        );

        $sheet   = $rows[0] ?? [];
        if (count($sheet) < 2) {
            return back()->with('error', 'File is empty or invalid format.');
        }

        $headers = array_map('trim', $sheet[0]);
        $rows    = array_slice($sheet, 1);

        $schedules = \App\Models\ExamSchedule::where('exam_id', $exam->id)
            ->where('class_id', $classId)->with('subject')->get()->keyBy('subject_id');

        // Map subject name → schedule
        $subjectMap = $schedules->mapWithKeys(fn($s) => [strtolower($s->subject?->name ?? '') => $s]);

        $currentYear = AcademicYear::current();
        $enrollments = StudentEnrollment::with('student')->where('class_id', $classId)
            ->where('status', 'active')
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->get();
        $studentMap = $enrollments->mapWithKeys(fn($e) => [strtolower(trim($e->student?->admission_number ?? '')) => $e->student_id]);

        $imported = 0;
        $errors   = [];
        foreach ($rows as $i => $row) {
            if (empty($row[0])) continue;
            $admNo    = strtolower(trim($row[0]));
            $studentId = $studentMap[$admNo] ?? null;
            if (!$studentId) { $errors[] = "Row " . ($i + 2) . ": Admission No '{$row[0]}' not found."; continue; }

            foreach ($headers as $colIdx => $header) {
                if ($colIdx < 1) continue;
                $subjectName = strtolower($header);
                $schedule = $subjectMap[$subjectName] ?? null;
                if (!$schedule) continue;
                $marks = isset($row[$colIdx]) && $row[$colIdx] !== '' ? (float)$row[$colIdx] : null;
                ExamMark::updateOrCreate(
                    ['exam_schedule_id' => $schedule->id, 'student_id' => $studentId],
                    ['marks_obtained' => $marks, 'is_absent' => is_null($marks), 'entered_by' => Auth::id()]
                );
                $imported++;
            }
        }

        $msg = "Imported {$imported} marks entries.";
        if ($errors) $msg .= ' Errors: ' . implode('; ', array_slice($errors, 0, 5));
        return back()->with($errors ? 'error' : 'success', $msg);
    }

    // ── Comparative Analysis: Class Average vs School Average ──

    public function comparativeAnalysis(Request $request)
    {
        $currentYear = AcademicYear::current();
        $exams       = Exam::when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->orderByDesc('id')->get();

        if (!$request->exam_id) {
            return view('examinations.comparative-analysis', [
                'exams'            => $exams,
                'exam'             => null,
                'classData'        => collect(),
                'schoolAvg'        => null,
                'subjectSchoolAvg' => [],
                'subjectNames'     => [],
            ]);
        }

        $exam = Exam::findOrFail($request->exam_id);

        $allSchedules = ExamSchedule::with('subject')
            ->where('exam_id', $exam->id)
            ->get();

        $subjectNames = $allSchedules->pluck('subject.name', 'subject_id')->unique()->toArray();

        $classIds = $allSchedules->pluck('class_id')->unique();
        $classes  = Classes::whereIn('id', $classIds)->orderBy('name')->get();

        $classData = $classes->map(function ($class) use ($allSchedules) {
            $classSchedules  = $allSchedules->where('class_id', $class->id);
            $subjectAverages = [];
            $classTotal      = 0;
            $classDivider    = 0;

            foreach ($classSchedules as $schedule) {
                $subjectName = $schedule->subject->name ?? ('Subject '.$schedule->subject_id);
                $marks = ExamMark::where('exam_schedule_id', $schedule->id)
                    ->where('is_absent', false)
                    ->pluck('marks_obtained');

                if ($marks->isEmpty()) continue;

                $avg = round($marks->avg(), 1);
                $subjectAverages[$subjectName] = $avg;
                $classTotal   += $marks->sum();
                $classDivider += $marks->count();
            }

            if ($classDivider === 0) return null;

            return [
                'class'        => $class,
                'overall_avg'  => round($classTotal / $classDivider, 1),
                'subject_avgs' => $subjectAverages,
            ];
        })->filter()->values();

        $schoolAvg = $classData->isEmpty() ? null : round($classData->avg('overall_avg'), 1);

        $subjectSchoolAvg = [];
        foreach ($allSchedules->groupBy(fn($s) => $s->subject->name ?? ('Subject '.$s->subject_id)) as $subjName => $schedules) {
            $scheduleIds = $schedules->pluck('id');
            $allMarks = ExamMark::whereIn('exam_schedule_id', $scheduleIds)
                ->where('is_absent', false)
                ->pluck('marks_obtained');
            if ($allMarks->isNotEmpty()) {
                $subjectSchoolAvg[$subjName] = round($allMarks->avg(), 1);
            }
        }

        return view('examinations.comparative-analysis', compact(
            'exams', 'exam', 'classData', 'schoolAvg', 'subjectSchoolAvg', 'subjectNames'
        ));
    }

    // ── Export: Class Result Summary PDF & Excel ──────────

    public function classResultSummaryPdf(Request $request)
    {
        $request->validate(['exam_id' => 'required', 'class_id' => 'required']);
        $exam  = Exam::with('schedules.subject')->findOrFail($request->exam_id);
        $class = Classes::findOrFail($request->class_id);
        $currentYear = AcademicYear::current();
        $q = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
        if ($currentYear) $q->where('academic_year_id', $currentYear->id);
        $enrollments = $q->get();
        $allMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $request->exam_id))
            ->whereIn('student_id', $enrollments->pluck('student_id'))->get();
        $summary = $enrollments->map(function ($enrollment) use ($allMarks, $exam) {
            $marks      = $allMarks->where('student_id', $enrollment->student_id);
            $totalMarks = $exam->schedules->where('class_id', $enrollment->class_id)->sum('max_marks');
            $obtained   = $marks->sum('marks_obtained');
            $percentage = $totalMarks > 0 ? round($obtained / $totalMarks * 100, 1) : 0;
            $hasFail    = $marks->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35));
            return ['student' => $enrollment->student, 'obtained' => $obtained, 'totalMarks' => $totalMarks, 'percentage' => $percentage, 'hasFail' => $hasFail];
        })->sortByDesc('percentage');
        $school = \App\Models\SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.class-result-summary', compact('exam', 'class', 'summary', 'school'))
            ->setPaper('a4', 'portrait');
        return $pdf->download('class-result-'.$class->name.'-'.$exam->name.'.pdf');
    }

    public function classResultSummaryExcel(Request $request)
    {
        $request->validate(['exam_id' => 'required', 'class_id' => 'required']);
        $exam  = Exam::with('schedules.subject')->findOrFail($request->exam_id);
        $class = Classes::findOrFail($request->class_id);
        $currentYear = AcademicYear::current();
        $q = StudentEnrollment::with('student')->where('class_id', $request->class_id)->where('status', 'active');
        if ($currentYear) $q->where('academic_year_id', $currentYear->id);
        $enrollments = $q->get();
        $allMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $request->exam_id))
            ->whereIn('student_id', $enrollments->pluck('student_id'))->get();
        $summary = $enrollments->map(function ($enrollment) use ($allMarks, $exam) {
            $marks    = $allMarks->where('student_id', $enrollment->student_id);
            $total    = $exam->schedules->where('class_id', $enrollment->class_id)->sum('max_marks');
            $obtained = $marks->sum('marks_obtained');
            $pct      = $total > 0 ? round($obtained / $total * 100, 1) : 0;
            $hasFail  = $marks->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35));
            return [
                'Adm No'      => $enrollment->student?->admission_number,
                'Student Name'=> $enrollment->student?->full_name,
                'Marks Obtained' => $obtained,
                'Total Marks' => $total,
                'Percentage'  => $pct.'%',
                'Result'      => $hasFail ? 'FAIL' : 'PASS',
            ];
        })->sortByDesc('Percentage')->values();

        return Excel::download(new \App\Exports\ArrayExport($summary->toArray(), ['Adm No', 'Student Name', 'Marks Obtained', 'Total Marks', 'Percentage', 'Result']),
            'class-result-'.$class->name.'-'.$exam->name.'.xlsx');
    }

    public function failedStudentsPdf(Request $request)
    {
        $request->validate(['exam_id' => 'required']);
        $exam = Exam::findOrFail($request->exam_id);
        $threshold = $exam->supplementary_threshold ?? 2;
        $failedMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $exam->id))
            ->where(fn($q) => $q->whereRaw('marks_obtained < COALESCE((SELECT pass_marks FROM exam_schedules WHERE id = exam_schedule_id), 35)'))
            ->whereHas('examSchedule.class', fn($q) => $request->class_id ? $q->where('class_id', $request->class_id) : $q)
            ->with(['student', 'examSchedule.subject'])
            ->get()->groupBy('student_id');
        $failed = $failedMarks->map(fn($marks) => [
            'student'       => $marks->first()->student,
            'subjects'      => $marks->map(fn($m) => $m->examSchedule?->subject?->name)->filter(),
            'supp_eligible' => $marks->count() <= $threshold,
        ]);
        $school = \App\Models\SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.failed-students', compact('exam', 'failed', 'school', 'threshold'))
            ->setPaper('a4', 'portrait');
        return $pdf->download('failed-students-'.$exam->name.'.pdf');
    }

    public function failedStudentsExcel(Request $request)
    {
        $request->validate(['exam_id' => 'required']);
        $exam = Exam::findOrFail($request->exam_id);
        $threshold = $exam->supplementary_threshold ?? 2;
        $failedMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $exam->id))
            ->where(fn($q) => $q->whereRaw('marks_obtained < COALESCE((SELECT pass_marks FROM exam_schedules WHERE id = exam_schedule_id), 35)'))
            ->with(['student', 'examSchedule.subject'])
            ->get()->groupBy('student_id');
        $rows = $failedMarks->map(fn($marks) => [
            'Adm No'       => $marks->first()->student?->admission_number,
            'Student Name' => $marks->first()->student?->full_name,
            'Failed Subjects' => $marks->map(fn($m) => $m->examSchedule?->subject?->name)->filter()->implode(', '),
            'Fail Count'   => $marks->count(),
            'Supp Eligible'=> $marks->count() <= $threshold ? 'Yes' : 'No',
        ])->values();

        return Excel::download(new \App\Exports\ArrayExport($rows->toArray(), ['Adm No', 'Student Name', 'Failed Subjects', 'Fail Count', 'Supp Eligible']),
            'failed-students-'.$exam->name.'.xlsx');
    }

    public function comparativeAnalysisPdf(Request $request)
    {
        $request->validate(['exam_id' => 'required']);
        $exam = Exam::findOrFail($request->exam_id);
        $allSchedules = ExamSchedule::with('subject')->where('exam_id', $exam->id)->get();
        $classIds = $allSchedules->pluck('class_id')->unique();
        $classes  = Classes::whereIn('id', $classIds)->orderBy('name')->get();
        $classData = $classes->map(function ($class) use ($allSchedules) {
            $classSchedules  = $allSchedules->where('class_id', $class->id);
            $classTotal = 0; $classDivider = 0; $subjectAverages = [];
            foreach ($classSchedules as $schedule) {
                $subjectName = $schedule->subject->name ?? ('Subject '.$schedule->subject_id);
                $marks = ExamMark::where('exam_schedule_id', $schedule->id)->where('is_absent', false)->pluck('marks_obtained');
                if ($marks->isEmpty()) continue;
                $subjectAverages[$subjectName] = round($marks->avg(), 1);
                $classTotal += $marks->sum(); $classDivider += $marks->count();
            }
            if ($classDivider === 0) return null;
            return ['class' => $class, 'overall_avg' => round($classTotal / $classDivider, 1), 'subject_avgs' => $subjectAverages];
        })->filter()->values();
        $schoolAvg = $classData->isEmpty() ? null : round($classData->avg('overall_avg'), 1);
        $subjectSchoolAvg = [];
        foreach ($allSchedules->groupBy(fn($s) => $s->subject->name ?? ('Subject '.$s->subject_id)) as $subjName => $schedules) {
            $scheduleIds = $schedules->pluck('id');
            $allMarks = ExamMark::whereIn('exam_schedule_id', $scheduleIds)->where('is_absent', false)->pluck('marks_obtained');
            if ($allMarks->isNotEmpty()) $subjectSchoolAvg[$subjName] = round($allMarks->avg(), 1);
        }
        $school = \App\Models\SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.comparative-analysis', compact('exam', 'classData', 'schoolAvg', 'subjectSchoolAvg', 'school'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('comparative-analysis-'.$exam->name.'.pdf');
    }

    // ── Question Paper Upload & Management ────────────────

    public function questionPapers(Request $request)
    {
        $exams    = Exam::orderByDesc('id')->get();
        $subjects = Subject::orderBy('name')->get();
        $classes  = Classes::active()->get();

        $papers = \App\Models\QuestionPaper::with(['exam', 'subject', 'class'])
            ->when($request->exam_id, fn($q, $v) => $q->where('exam_id', $v))
            ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
            ->latest()->get();

        return view('examinations.question-papers', compact('papers', 'exams', 'subjects', 'classes'));
    }

    public function uploadQuestionPaper(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:200',
            'file'             => 'required|file|mimes:pdf|max:20480',
            'exam_id'          => 'nullable|exists:exams,id',
            'subject_id'       => 'nullable|exists:subjects,id',
            'class_id'         => 'nullable|exists:classes,id',
            'accessible_from'  => 'nullable|date',
            'is_restricted'    => 'boolean',
        ]);

        $path = $request->file('file')->store('question-papers', 'public');

        \App\Models\QuestionPaper::create([
            'title'           => $request->title,
            'file_path'       => $path,
            'original_name'   => $request->file('file')->getClientOriginalName(),
            'exam_id'         => $request->exam_id,
            'subject_id'      => $request->subject_id,
            'class_id'        => $request->class_id,
            'accessible_from' => $request->accessible_from,
            'is_restricted'   => $request->boolean('is_restricted', true),
            'uploaded_by'     => auth()->id(),
        ]);

        return back()->with('success', 'Question paper uploaded successfully.');
    }

    public function downloadQuestionPaper(int $id)
    {
        $paper = \App\Models\QuestionPaper::findOrFail($id);
        abort_if(
            $paper->is_restricted && $paper->accessible_from && $paper->accessible_from->isFuture(),
            403,
            'This question paper is restricted until ' . $paper->accessible_from->format('d M Y')
        );
        return response()->download(storage_path('app/public/' . $paper->file_path), $paper->original_name ?? basename($paper->file_path));
    }

    public function deleteQuestionPaper(int $id)
    {
        $paper = \App\Models\QuestionPaper::findOrFail($id);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($paper->file_path);
        $paper->delete();
        return back()->with('success', 'Question paper deleted.');
    }

    /* ------------------------------------------------------------------ */
    /*  Manual Question Paper Assembly from Question Bank                   */
    /* ------------------------------------------------------------------ */

    public function assembleQuestionPaper(Request $request)
    {
        $subjects  = Subject::orderBy('name')->get();
        $classes   = Classes::active()->get();
        $exams     = Exam::orderByDesc('id')->get();

        $questions = collect();
        if ($request->subject_id || $request->class_id) {
            $questions = QuestionBank::with(['subject', 'class'])
                ->when($request->subject_id, fn($q, $v) => $q->where('subject_id', $v))
                ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
                ->when($request->question_type, fn($q, $v) => $q->where('question_type', $v))
                ->when($request->chapter, fn($q, $v) => $q->where('chapter', 'like', "%$v%"))
                ->when($request->difficulty, fn($q, $v) => $q->where('difficulty_level', $v))
                ->orderBy('question_type')->orderBy('difficulty_level')
                ->get();
        }

        return view('examinations.assemble-paper', compact('subjects', 'classes', 'exams', 'questions'));
    }

    public function generateAssembledPaper(Request $request)
    {
        $request->validate([
            'question_ids'  => 'required|array|min:1',
            'question_ids.*'=> 'exists:question_bank,id',
            'paper_title'   => 'required|string|max:200',
        ]);

        $questions   = QuestionBank::with(['subject', 'class'])->findMany($request->question_ids);
        $school      = \App\Models\SchoolSetting::first();
        $exam        = $request->exam_id ? Exam::find($request->exam_id) : null;
        $totalMarks  = $questions->sum('marks');
        $duration    = $request->duration ?? 180;
        $templateId  = $request->template_id;
        $template    = $templateId ? \App\Models\QuestionPaperTemplate::find($templateId) : \App\Models\QuestionPaperTemplate::getDefault();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.assembled-question-paper', compact('questions', 'school', 'exam', 'totalMarks', 'duration', 'request', 'template'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('question-paper-' . \Illuminate\Support\Str::slug($request->paper_title) . '.pdf');
    }

    // ── Question Paper Template Builder ───────────────────

    public function questionPaperTemplates()
    {
        $templates = \App\Models\QuestionPaperTemplate::orderByDesc('is_default')->orderBy('name')->get();
        return view('examinations.question-paper-templates', compact('templates'));
    }

    public function storeQuestionPaperTemplate(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        if ($request->boolean('is_default')) {
            \App\Models\QuestionPaperTemplate::where('is_default', true)->update(['is_default' => false]);
        }
        \App\Models\QuestionPaperTemplate::create([
            'name'                    => $request->name,
            'exam_type'               => $request->exam_type,
            'show_school_logo'        => $request->boolean('show_school_logo', true),
            'show_school_name'        => $request->boolean('show_school_name', true),
            'show_school_address'     => $request->boolean('show_school_address', true),
            'show_affiliation'        => $request->boolean('show_affiliation', true),
            'header_instructions'     => $request->header_instructions,
            'general_instructions'    => $request->general_instructions,
            'watermark_text'          => $request->watermark_text,
            'question_numbering'      => $request->question_numbering ?? 'numeric',
            'show_marks_per_question' => $request->boolean('show_marks_per_question', true),
            'show_section_totals'     => $request->boolean('show_section_totals', true),
            'show_answer_lines'       => $request->boolean('show_answer_lines'),
            'answer_lines_count'      => $request->answer_lines_count ?? 5,
            'paper_size'              => $request->paper_size ?? 'A4',
            'font_size'               => $request->font_size ?? '12',
            'is_default'              => $request->boolean('is_default'),
        ]);
        return back()->with('success', 'Question paper template saved.');
    }

    public function deleteQuestionPaperTemplate(int $id)
    {
        \App\Models\QuestionPaperTemplate::findOrFail($id)->delete();
        return back()->with('success', 'Template deleted.');
    }

    public function printQuestionPaper(int $id)
    {
        $paper    = \App\Models\QuestionPaper::findOrFail($id);
        abort_if(
            $paper->is_restricted && $paper->accessible_from && $paper->accessible_from->isFuture(),
            403, 'This question paper is restricted until ' . $paper->accessible_from->format('d M Y')
        );
        $paper->increment('access_count');
        if ($paper->file_path) {
            return response()->file(storage_path('app/public/' . $paper->file_path), [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . ($paper->original_name ?? basename($paper->file_path)) . '"',
            ]);
        }
        abort(404, 'File not found.');
    }

    // ── NEP 2020 Competency Framework ─────────────────────────────────────────

    public function competencies(Request $request)
    {
        $classes  = Classes::active()->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $competencies = Competency::with(['subject', 'class'])
            ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
            ->when($request->subject_id, fn($q, $v) => $q->where('subject_id', $v))
            ->orderBy('class_id')->orderBy('subject_id')->orderBy('sort_order')
            ->paginate(40);
        return view('examinations.competencies', compact('classes', 'subjects', 'competencies'));
    }

    public function storeCompetency(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'name'       => 'required|string|max:200',
            'code'       => 'nullable|string|max:30',
            'domain'     => 'required|in:cognitive,affective,psychomotor,co_scholastic',
        ]);
        Competency::create($request->only(['class_id', 'subject_id', 'name', 'code', 'description', 'domain', 'sort_order']));
        return back()->with('success', 'Competency added.');
    }

    public function deleteCompetency(int $id)
    {
        Competency::findOrFail($id)->delete();
        return back()->with('success', 'Competency deleted.');
    }

    public function competencyAssessment(Request $request)
    {
        $academicYear = AcademicYear::current();
        $classes  = Classes::active()->get();
        $students = collect();
        $competencies = collect();
        $assessments  = collect();
        $student = null;

        if ($request->student_id) {
            $student = Student::with('currentEnrollment.class')->findOrFail($request->student_id);
            $classId = $student->currentEnrollment?->class_id;
            if ($classId) {
                $competencies = Competency::with('subject')
                    ->where('class_id', $classId)
                    ->where('is_active', true)
                    ->orderBy('subject_id')->orderBy('sort_order')
                    ->get();
                $assessments = CompetencyAssessment::where('student_id', $student->id)
                    ->where('academic_year_id', $academicYear?->id)
                    ->when($request->term, fn($q, $v) => $q->where('term', $v))
                    ->get()->keyBy(fn($a) => $a->competency_id . '_' . $a->term);
            }
        }

        return view('examinations.competency-assessment', compact(
            'academicYear', 'classes', 'student', 'competencies', 'assessments'
        ));
    }

    public function saveCompetencyAssessment(Request $request)
    {
        $request->validate([
            'student_id'      => 'required|exists:students,id',
            'academic_year_id'=> 'required|exists:academic_years,id',
            'term'            => 'required|string|max:30',
            'assessments'     => 'array',
        ]);

        foreach ($request->input('assessments', []) as $competencyId => $data) {
            CompetencyAssessment::updateOrCreate(
                [
                    'student_id'       => $request->student_id,
                    'competency_id'    => $competencyId,
                    'academic_year_id' => $request->academic_year_id,
                    'term'             => $request->term,
                ],
                [
                    'level'           => $data['level'] ?? 'not_assessed',
                    'remarks'         => $data['remarks'] ?? null,
                    'assessed_by'     => auth()->id(),
                    'assessment_date' => today(),
                ]
            );
        }
        return back()->with('success', 'Competency assessments saved.');
    }

    public function competencyReport(Request $request)
    {
        $academicYear = AcademicYear::current();
        $classes  = Classes::active()->get();
        $students = collect();
        $report   = collect();
        $student  = null;

        if ($request->student_id) {
            $student = Student::with('currentEnrollment.class')->findOrFail($request->student_id);
            $classId = $student->currentEnrollment?->class_id;
            $competencies = Competency::with('subject')->where('class_id', $classId)->where('is_active', true)->get();
            $assessments  = CompetencyAssessment::with('competency.subject')
                ->where('student_id', $student->id)
                ->where('academic_year_id', $academicYear?->id)
                ->get();

            $report = $competencies->map(function ($comp) use ($assessments) {
                $termAssessments = $assessments->where('competency_id', $comp->id)->keyBy('term');
                return [
                    'competency'   => $comp,
                    'subject'      => $comp->subject?->name,
                    'term1'        => $termAssessments->get('Term 1'),
                    'term2'        => $termAssessments->get('Term 2'),
                    'annual'       => $termAssessments->get('Annual'),
                ];
            });
        }

        return view('examinations.competency-report', compact('academicYear', 'classes', 'student', 'report'));
    }

    public function coscholasticAssessment(Request $request)
    {
        $academicYear = AcademicYear::current();
        $student = null;
        $assessments = collect();

        if ($request->student_id) {
            $student = Student::findOrFail($request->student_id);
            $assessments = CoscholasticAssessment::where('student_id', $student->id)
                ->where('academic_year_id', $academicYear?->id)
                ->when($request->term, fn($q, $v) => $q->where('term', $v))
                ->get()->groupBy('area');
        }

        $areas = [
            'arts'           => ['Painting', 'Music', 'Dance', 'Drama', 'Craft'],
            'sports'         => ['Athletics', 'Cricket', 'Football', 'Basketball', 'Yoga', 'Swimming'],
            'values'         => ['Honesty', 'Discipline', 'Teamwork', 'Respect', 'Empathy'],
            'health_hygiene' => ['Personal Hygiene', 'Physical Fitness', 'Health Awareness'],
            'work_education' => ['Gardening', 'Cooking', 'Tailoring', 'Computer Skills'],
        ];

        return view('examinations.coscholastic-assessment', compact(
            'academicYear', 'student', 'assessments', 'areas'
        ));
    }

    public function saveCoscholasticAssessment(Request $request)
    {
        $request->validate([
            'student_id'       => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term'             => 'required|string|max:30',
        ]);

        foreach ($request->input('areas', []) as $area => $subAreas) {
            foreach ($subAreas as $subArea => $data) {
                if (empty($data['grade'])) continue;
                CoscholasticAssessment::updateOrCreate(
                    [
                        'student_id'       => $request->student_id,
                        'academic_year_id' => $request->academic_year_id,
                        'term'             => $request->term,
                        'area'             => $area,
                        'sub_area'         => $subArea,
                    ],
                    [
                        'grade'       => $data['grade'],
                        'remarks'     => $data['remarks'] ?? null,
                        'assessed_by' => auth()->id(),
                    ]
                );
            }
        }
        return back()->with('success', 'Co-scholastic assessment saved.');
    }

    public function activityRecords(Request $request)
    {
        $academicYear = AcademicYear::current();
        $student = null;
        $records = collect();

        if ($request->student_id) {
            $student = Student::findOrFail($request->student_id);
            $records = ActivityLearningRecord::where('student_id', $student->id)
                ->where('academic_year_id', $academicYear?->id)
                ->orderByDesc('activity_date')
                ->get();
        }

        return view('examinations.activity-records', compact('academicYear', 'student', 'records'));
    }

    public function storeActivityRecord(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'activity_name' => 'required|string|max:200',
            'activity_type' => 'required|string|max:50',
            'activity_date' => 'required|date|before_or_equal:today',
        ]);

        $academicYear = AcademicYear::current();
        $attachment = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('activity-records', 'public');
        }

        ActivityLearningRecord::create([
            'student_id'       => $request->student_id,
            'academic_year_id' => $academicYear?->id,
            'activity_name'    => $request->activity_name,
            'activity_type'    => $request->activity_type,
            'description'      => $request->description,
            'activity_date'    => $request->activity_date,
            'outcome'          => $request->outcome,
            'attachment'       => $attachment,
            'recorded_by'      => auth()->id(),
        ]);

        return back()->with('success', 'Activity record added.');
    }

    // ── Email result notifications ────────────────────────

    public function emailResultNotifications(int $examId)
    {
        $exam    = Exam::with('schedules.subject')->findOrFail($examId);
        $classes = Classes::active()->orderBy('name')->get();
        return view('examinations.email-results', compact('exam', 'classes'));
    }

    public function sendResultEmails(Request $request, int $examId)
    {
        $request->validate(['class_id' => 'nullable|exists:classes,id']);

        $exam         = Exam::with('schedules.subject')->findOrFail($examId);
        $gradingScheme = GradingScheme::where('is_default', true)->with('ranges')->first();
        $school       = \App\Models\SchoolSetting::first();

        $enrollQuery = StudentEnrollment::with(['student.user', 'student.parent', 'class', 'academicYear'])
            ->where('exam_id', $examId)
            ->orWhereHas('student', fn($q) => $q);

        // Get enrollments for the exam's academic year
        $enrollments = StudentEnrollment::with(['student', 'class', 'section', 'academicYear'])
            ->where('status', 'active')
            ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
            ->when($exam->academic_year_id, fn($q) => $q->where('academic_year_id', $exam->academic_year_id))
            ->get();

        $sent = 0; $skipped = 0;

        foreach ($enrollments as $enrollment) {
            $student = $enrollment->student;
            $email   = $student?->user?->email ?? $student?->parent?->email ?? null;
            if (!$email) { $skipped++; continue; }

            $allMarks = ExamMark::whereHas('examSchedule', fn($q) => $q->where('exam_id', $examId))
                ->where('student_id', $student->id)->with('examSchedule.subject')->get();

            if ($allMarks->isEmpty()) { $skipped++; continue; }

            $marks      = $allMarks->filter(fn($m) => !($m->examSchedule?->subject?->is_coscholastic));
            $totalMarks = $exam->schedules->where('class_id', $enrollment->class_id)
                ->filter(fn($s) => !($s->subject?->is_coscholastic))->sum('max_marks');
            $obtained   = $marks->sum('marks_obtained');
            $percentage = $totalMarks > 0 ? round($obtained / $totalMarks * 100, 2) : 0;
            $grade      = $gradingScheme?->gradeFor($percentage) ?? '—';
            $hasFail    = $marks->contains(fn($m) => !$m->is_absent && $m->marks_obtained < ($m->examSchedule?->pass_marks ?? 35));
            $result     = $hasFail ? 'FAIL' : 'PASS';

            // Generate PDF
            try {
                $signatureBase64 = null; $stampBase64 = null;
                if ($school?->principal_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->principal_signature)) {
                    $signatureBase64 = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($school->principal_signature));
                }
                if ($school?->school_stamp && \Illuminate\Support\Facades\Storage::disk('public')->exists($school->school_stamp)) {
                    $stampBase64 = 'data:image/png;base64,' . base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($school->school_stamp));
                }
                $coscholasticMarks = $allMarks->filter(fn($m) => $m->examSchedule?->subject?->is_coscholastic);
                $subjectChart = $marks->map(fn($m) => [
                    'name'   => \Illuminate\Support\Str::limit($m->examSchedule?->subject?->name ?? '—', 12),
                    'pct'    => ($m->examSchedule?->max_marks ?? 0) > 0 ? round($m->marks_obtained / $m->examSchedule->max_marks * 100) : 0,
                    'absent' => (bool)$m->is_absent,
                    'pass'   => !$m->is_absent && $m->marks_obtained >= ($m->examSchedule?->pass_marks ?? 35),
                ])->values();
                $academicYear     = $enrollment->academicYear;
                $overallGrade     = $grade;
                $rank = '—'; $attendancePct = 0; $totalDays = 0; $presentDays = 0;
                $teacherRemarks = null; $principalRemarks = null;
                $cumulativeData = collect(); $cumulativeTotal = 0;
                $pdf = Pdf::loadView('pdf.report-card', compact(
                    'school', 'exam', 'academicYear', 'student', 'enrollment',
                    'marks', 'coscholasticMarks', 'totalMarks', 'percentage',
                    'overallGrade', 'result', 'rank', 'attendancePct',
                    'teacherRemarks', 'principalRemarks', 'cumulativeData', 'cumulativeTotal',
                    'totalDays', 'presentDays', 'signatureBase64', 'stampBase64', 'subjectChart'
                ))->setPaper('a4', 'portrait');

                $pdfContent = $pdf->output();
                $overallGrade = $grade;

                Mail::to($email)->send(new ResultNotificationMail(
                    studentName: $student->full_name,
                    examName:    $exam->name,
                    percentage:  $percentage,
                    grade:       $grade,
                    result:      $result,
                    schoolName:  $school?->school_name ?? config('app.name'),
                    pdfContent:  $pdfContent,
                    pdfName:     'report-card-' . ($student->admission_number ?? $student->id) . '.pdf',
                ));
                $sent++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        return back()->with('success', "Result notifications sent: {$sent}. Skipped: {$skipped}.");
    }
}
