<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Notice;
use App\Models\NoticeRead;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\Subject;
use App\Models\Substitution;
use App\Models\TeacherSubjectAllocation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademicController extends Controller
{
    public function index()
    {
        $currentYear  = AcademicYear::current();
        $classes      = Classes::active()->withCount(['sections as sections_count' => function ($q) use ($currentYear) {
            $q->where('is_active', true);
            if ($currentYear) {
                $q->where('academic_year_id', $currentYear->id);
            }
        }])->get();
        // Fallback: if no current year or all classes show 0, count without year filter
        if ($classes->sum('sections_count') === 0) {
            $classes = Classes::active()->withCount(['sections as sections_count' => function ($q) {
                $q->where('is_active', true);
            }])->get();
        }
        $subjects     = Subject::where('is_active', true)->count();
        $teachers     = Employee::where('is_active', true)
            ->where('employee_type', 'teaching')->count()
            ?: Employee::where('is_active', true)->count();
        $pendingHomework   = DB::table('homework')->where('due_date', '>=', today()->toDateString())->count();
        $pendingSubstitutions = DB::table('substitutions')->whereDate('date', today())->count();
        $activeNotices    = DB::table('notices')->where('is_published', true)
            ->where(fn($q) => $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', today()))
            ->count();
        return view('academics.index', compact(
            'currentYear', 'classes', 'subjects', 'teachers',
            'pendingHomework', 'pendingSubstitutions', 'activeNotices'
        ));
    }

    public function timetable(Request $request)
    {
        $classes  = Classes::active()->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $teachers = Employee::where('is_active', true)->orderBy('first_name')->get();
        $sections = collect();
        $timetable = [];

        if ($request->class_id) {
            $sections  = Section::where('class_id', $request->class_id)->get();
            $timetable = \App\Models\Timetable::with(['subject', 'teacher'])
                ->where('class_id', $request->class_id)
                ->when($request->section_id, fn($q, $v) => $q->where('section_id', $v))
                ->orderBy('day_of_week')->orderBy('start_time')
                ->get()->groupBy('day_of_week');
        }

        return view('academics.timetable', compact('classes', 'sections', 'timetable', 'subjects', 'teachers'));
    }

    public function subjects(Request $request)
    {
        $subjects = Subject::when($request->search, fn($q, $v) => $q->where('name', 'like', "%$v%"))
            ->when($request->type, fn($q, $v) => $q->where('type', $v))
            ->orderBy('name')->paginate(25)->withQueryString();

        return view('academics.subjects', compact('subjects'));
    }

    public function syllabus(Request $request)
    {
        $classes  = Classes::active()->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $syllabus = collect();

        if ($request->class_id && $request->subject_id) {
            $syllabus = \App\Models\Syllabus::where('class_id', $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->orderBy('chapter_number')->get();
        }

        return view('academics.syllabus', compact('classes', 'subjects', 'syllabus'));
    }

    public function homework(Request $request)
    {
        $classes  = Classes::active()->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $homework = \App\Models\Homework::with(['class', 'subject'])
            ->withCount('submissions')
            ->when($request->class_id,   fn($q, $v) => $q->where('class_id', $v))
            ->when($request->subject_id, fn($q, $v) => $q->where('subject_id', $v))
            ->when($request->date,       fn($q, $v) => $q->whereDate('due_date', $v))
            ->where('is_active', true)->latest()->paginate(20);

        return view('academics.homework', compact('classes', 'subjects', 'homework'));
    }

    public function saveTimetable(Request $request)
    {
        $request->validate(['class_id' => 'required|exists:classes,id', 'entries' => 'required|array']);
        foreach ($request->entries as $entry) {
            \App\Models\Timetable::updateOrCreate(
                ['class_id' => $entry['class_id'], 'section_id' => $entry['section_id'] ?? null, 'day_of_week' => $entry['day_of_week'], 'period_number' => $entry['period_number']],
                [
                    'subject_id'    => $entry['subject_id'] ?? null,
                    'teacher_id'    => $entry['teacher_id'] ?? $entry['employee_id'] ?? null,
                    'start_time'    => $entry['start_time'] ?? null,
                    'end_time'      => $entry['end_time'] ?? null,
                    'period_type'   => $entry['period_type'] ?? 'class',
                    'effective_from'=> $entry['effective_from'] ?? null,
                ]
            );
        }
        return back()->with('success', 'Timetable saved.');
    }

    public function saveSyllabus(Request $request)
    {
        $request->validate(['class_id' => 'required|exists:classes,id', 'subject_id' => 'required|exists:subjects,id', 'chapter_number' => 'required|integer', 'chapter_title' => 'required|string|max:200']);
        \App\Models\Syllabus::updateOrCreate(
            ['class_id' => $request->class_id, 'subject_id' => $request->subject_id, 'chapter_number' => $request->chapter_number],
            $request->only(['chapter_title', 'description', 'status', 'start_date', 'end_date'])
        );
        return back()->with('success', 'Syllabus entry saved.');
    }

    public function uploadSyllabusDocument(Request $request, int $id)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf|max:10240',
            'term'     => 'nullable|string|max:50',
        ]);
        $syllabus = \App\Models\Syllabus::findOrFail($id);
        $path = $request->file('document')->store('syllabus-docs', 'public');
        $syllabus->update([
            'document_path' => $path,
            'term'          => $request->term,
        ]);
        return back()->with('success', 'Syllabus document uploaded.');
    }

    public function saveHomework(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'title'      => 'nullable|string|max:200',
            'description'=> 'required|string|min:5',
            'due_date'   => 'required|date',
            'max_score'  => 'nullable|integer|min:0',
            'attachment' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('homework/attachments', 'public');
        }
        \App\Models\Homework::create(array_merge($request->only(['class_id', 'section_id', 'subject_id', 'title', 'description', 'due_date', 'max_score']), [
            'attachment'  => $attachmentPath,
            'assigned_by' => Auth::id(),
            'is_active'   => true,
        ]));
        return back()->with('success', 'Homework assigned.');
    }

    public function holidays()
    {
        $currentYear   = AcademicYear::current();
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $holidays = Holiday::when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->orderBy('date')->paginate(30);
        return view('academics.holidays', compact('holidays', 'currentYear', 'academicYears'));
    }

    public function addHoliday(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:100',
            'date'             => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);
        Holiday::firstOrCreate(
            ['date' => $request->date, 'academic_year_id' => $request->academic_year_id],
            $request->only(['name', 'description', 'type'])
        );
        return back()->with('success', 'Holiday added.');
    }

    public function deleteHoliday(int $id)
    {
        Holiday::findOrFail($id)->delete();
        return back()->with('success', 'Holiday removed.');
    }

    public function subjectsManage(Request $request)
    {
        $subjects = Subject::withCount(['allocations'])->orderBy('name')->paginate(20)->withQueryString();
        return view('academics.subjects-manage', compact('subjects'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100', 'credit_hours' => 'nullable|integer|min:0|max:40']);
        Subject::create(array_merge(
            $request->only(['name', 'code', 'type', 'medium', 'language_type', 'board_curriculum', 'credit_hours', 'stream']),
            [
                'is_active'       => true,
                'is_elective'     => $request->boolean('is_elective'),
                'is_coscholastic' => $request->boolean('is_coscholastic'),
            ]
        ));
        return back()->with('success', 'Subject added.');
    }

    public function updateSubject(Request $request, int $id)
    {
        Subject::findOrFail($id)->update(array_merge(
            $request->only(['name', 'code', 'type', 'medium', 'language_type', 'board_curriculum', 'credit_hours', 'stream']),
            [
                'is_elective'     => $request->boolean('is_elective'),
                'is_coscholastic' => $request->boolean('is_coscholastic'),
            ]
        ));
        return back()->with('success', 'Subject updated.');
    }

    public function toggleSubject(int $id)
    {
        $s = Subject::findOrFail($id);
        $s->update(['is_active' => !$s->is_active]);
        return back()->with('success', 'Subject ' . ($s->is_active ? 'activated' : 'deactivated') . '.');
    }

    public function teacherAllocation(Request $request)
    {
        $teachers       = Employee::where('employee_type', 'teaching')->where('is_active', true)->orderBy('first_name')->get();
        $subjects       = Subject::where('is_active', true)->orderBy('name')->get();
        $classes        = Classes::active()->get();
        $sections       = Section::orderBy('name')->get();
        $academicYears  = AcademicYear::orderByDesc('start_date')->get();
        $currentYear    = AcademicYear::current();
        $filterYearId   = $request->academic_year_id ?? $currentYear?->id;

        $allocations = TeacherSubjectAllocation::with(['employee', 'subject', 'class', 'section', 'academicYear'])
            ->when($filterYearId, fn($q) => $q->where('academic_year_id', $filterYearId))
            ->when($request->teacher_id, fn($q, $v) => $q->where('employee_id', $v))
            ->when($request->class_id,   fn($q, $v) => $q->where('class_id', $v))
            ->latest()->paginate(25)->withQueryString();

        return view('academics.teacher-allocation', compact(
            'teachers', 'subjects', 'classes', 'sections',
            'allocations', 'academicYears', 'filterYearId'
        ));
    }

    public function allocationHistory(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $teachers      = Employee::where('employee_type', 'teaching')->orderBy('first_name')->get();
        $subjects      = Subject::orderBy('name')->get();
        $classes       = Classes::orderBy('name')->get();

        $history = TeacherSubjectAllocation::with(['employee', 'subject', 'class', 'section', 'academicYear'])
            ->when($request->academic_year_id, fn($q, $v) => $q->where('academic_year_id', $v))
            ->when($request->teacher_id,       fn($q, $v) => $q->where('employee_id', $v))
            ->when($request->subject_id,       fn($q, $v) => $q->where('subject_id', $v))
            ->when($request->class_id,         fn($q, $v) => $q->where('class_id', $v))
            ->orderByDesc('academic_year_id')
            ->paginate(30)->withQueryString();

        return view('academics.allocation-history', compact('history', 'academicYears', 'teachers', 'subjects', 'classes'));
    }

    public function saveAllocation(Request $request)
    {
        $request->validate(['employee_id' => 'required|exists:employees,id', 'subject_id' => 'required|exists:subjects,id', 'class_id' => 'required|exists:classes,id']);
        $currentYear = AcademicYear::current();
        TeacherSubjectAllocation::firstOrCreate([
            'employee_id' => $request->employee_id,
            'subject_id'  => $request->subject_id,
            'class_id'    => $request->class_id,
            'section_id'  => $request->section_id ?? null,
            'academic_year_id' => $currentYear?->id,
        ]);
        return back()->with('success', 'Teacher assigned.');
    }

    public function deleteAllocation(int $id)
    {
        TeacherSubjectAllocation::findOrFail($id)->delete();
        return back()->with('success', 'Allocation removed.');
    }

    public function academicYear()
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        return view('academics.academic-year', compact('academicYears'));
    }

    public function saveAcademicYear(Request $request)
    {
        $request->validate(['name' => 'required|string|max:20', 'start_date' => 'required|date', 'end_date' => 'required|date|after:start_date']);
        if ($request->is_current) AcademicYear::query()->update(['is_current' => false]);
        AcademicYear::create(array_merge($request->only(['name', 'start_date', 'end_date']), ['is_current' => (bool)$request->is_current]));
        return back()->with('success', 'Academic year created.');
    }

    public function setCurrentYear(int $id)
    {
        AcademicYear::query()->update(['is_current' => false]);
        AcademicYear::findOrFail($id)->update(['is_current' => true]);
        return back()->with('success', 'Current academic year updated.');
    }

    public function yearArchive(Request $request)
    {
        $academicYears   = AcademicYear::orderByDesc('start_date')->get();
        $selectedYear    = $request->year_id
            ? AcademicYear::findOrFail($request->year_id)
            : $academicYears->where('is_current', false)->first();

        $enrollments = collect();
        $classSummary = collect();
        if ($selectedYear) {
            $classes = \App\Models\Classes::withCount([
                'enrollments as student_count' => fn($q) => $q->where('academic_year_id', $selectedYear->id),
            ])->get()->filter(fn($c) => $c->student_count > 0);

            foreach ($classes as $class) {
                $classSummary->push([
                    'class'         => $class,
                    'total'         => $class->student_count,
                    'promoted'      => \App\Models\StudentEnrollment::where('class_id', $class->id)
                                        ->where('academic_year_id', $selectedYear->id)
                                        ->where('promoted', true)->count(),
                    'detained'      => \App\Models\StudentEnrollment::where('class_id', $class->id)
                                        ->where('academic_year_id', $selectedYear->id)
                                        ->where('promoted', false)->count(),
                ]);
            }
        }

        return view('academics.year-archive', compact('academicYears', 'selectedYear', 'classSummary'));
    }

    public function yearArchiveStudents(Request $request, int $yearId)
    {
        $year        = AcademicYear::findOrFail($yearId);
        $classes     = \App\Models\Classes::active()->get();
        $enrollments = \App\Models\StudentEnrollment::with(['student', 'class', 'section'])
            ->where('academic_year_id', $yearId)
            ->when($request->class_id, fn($q) => $q->where('class_id', $request->class_id))
            ->orderBy('class_id')->orderBy('roll_number')
            ->paginate(50);

        return view('academics.year-archive-students', compact('year', 'classes', 'enrollments'));
    }

    // ── Notice Board ──────────────────────────────────────────

    public function notices(Request $request)
    {
        $notices = Notice::with(['createdBy', 'targetClass', 'reads'])
            ->when($request->type, fn($q, $v) => $q->where('notice_type', $v))
            ->when($request->audience, fn($q, $v) => $q->where('target_audience', $v))
            ->latest('publish_date')->paginate(20)->withQueryString();
        $classes = Classes::active()->get();
        return view('academics.notices', compact('notices', 'classes'));
    }

    public function createNotice()
    {
        $classes = Classes::active()->get();
        return view('academics.notice-form', compact('classes'));
    }

    public function storeNotice(Request $request)
    {
        $request->validate([
            'title'            => 'required|string|max:200',
            'content'          => 'required|string',
            'notice_type'      => 'required|in:general,circular,academic,exam,fee,event',
            'target_audience'  => 'required|in:all,students,staff,parents,class_specific',
            'publish_date'     => 'required|date',
            'expiry_date'      => 'nullable|date|after:publish_date',
            'target_class_id'  => 'nullable|exists:classes,id',
        ]);

        Notice::create(array_merge($request->validated(), [
            'created_by'   => Auth::id(),
            'is_published' => $request->boolean('is_published'),
        ]));

        return redirect()->route('academics.notices')->with('success', 'Notice created.');
    }

    public function editNotice(int $id)
    {
        $notice  = Notice::findOrFail($id);
        $classes = Classes::active()->get();
        return view('academics.notice-edit', compact('notice', 'classes'));
    }

    public function updateNotice(Request $request, int $id)
    {
        $notice = Notice::findOrFail($id);
        $notice->update(array_merge($request->only(['title', 'content', 'notice_type', 'target_audience', 'publish_date', 'expiry_date', 'target_class_id']), [
            'is_published' => $request->boolean('is_published'),
        ]));
        return back()->with('success', 'Notice updated.');
    }

    public function deleteNotice(int $id)
    {
        Notice::findOrFail($id)->delete();
        return back()->with('success', 'Notice deleted.');
    }

    public function markNoticeRead(int $id)
    {
        NoticeRead::firstOrCreate(['notice_id' => $id, 'user_id' => Auth::id()], ['read_at' => now()]);
        return back();
    }

    public function noticeReadReceipts(int $id)
    {
        $notice = Notice::with('createdBy')->findOrFail($id);
        $reads  = NoticeRead::with('user')
            ->where('notice_id', $id)
            ->orderBy('read_at')
            ->paginate(50);

        // Audience total (approximation for %age display)
        $totalUsers = \App\Models\User::where('is_active', true)->count();

        return view('academics.notice-read-receipts', compact('notice', 'reads', 'totalUsers'));
    }

    // ── Substitutions ─────────────────────────────────────────

    public function substitutions(Request $request)
    {
        $classes   = Classes::active()->get();
        $teachers  = Employee::where('employee_type', 'teaching')->where('is_active', true)->orderBy('first_name')->get();
        $subjects  = Subject::where('is_active', true)->orderBy('name')->get();
        $date      = $request->date ?? today()->toDateString();

        $substitutions = Substitution::with(['absentTeacher', 'substituteTeacher', 'class', 'section', 'subject'])
            ->when($request->date, fn($q, $v) => $q->where('date', $v))
            ->when(!$request->date, fn($q) => $q->where('date', today()))
            ->orderBy('period_number')->get();

        return view('academics.substitutions', compact('classes', 'teachers', 'subjects', 'substitutions', 'date'));
    }

    public function storeSubstitution(Request $request)
    {
        $request->validate([
            'date'                    => 'required|date',
            'absent_teacher_id'       => 'required|exists:employees,id',
            'substitute_teacher_id'   => 'required|exists:employees,id|different:absent_teacher_id',
            'class_id'                => 'required|exists:classes,id',
            'period_number'           => 'required|integer|min:1|max:12',
        ]);

        Substitution::create(array_merge($request->only(['date', 'absent_teacher_id', 'substitute_teacher_id', 'class_id', 'section_id', 'subject_id', 'period_number', 'start_time', 'end_time', 'remarks']), [
            'arranged_by' => Auth::id(),
        ]));

        return back()->with('success', 'Substitution recorded.');
    }

    public function deleteSubstitution(int $id)
    {
        Substitution::findOrFail($id)->delete();
        return back()->with('success', 'Substitution removed.');
    }

    // ── PDFs ──────────────────────────────────────────────────

    public function timetablePdf(Request $request)
    {
        $request->validate(['class_id' => 'required|exists:classes,id']);
        $class    = Classes::findOrFail($request->class_id);
        $sections = Section::where('class_id', $class->id)->get();
        $timetable = \App\Models\Timetable::with(['subject', 'teacher'])
            ->where('class_id', $class->id)
            ->when($request->section_id, fn($q, $v) => $q->where('section_id', $v))
            ->orderBy('day_of_week')->orderBy('start_time')
            ->get()->groupBy('day_of_week');
        $school = \App\Models\SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.timetable', compact('timetable', 'class', 'sections', 'school'))->setPaper('a4', 'landscape');
        return $pdf->download('timetable-' . $class->name . '.pdf');
    }

    public function calendarPdf()
    {
        $currentYear = AcademicYear::current();
        $holidays    = Holiday::orderBy('date')->get();
        $school      = SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.academic-calendar', compact('currentYear', 'holidays', 'school'));
        return $pdf->download('academic-calendar.pdf');
    }

    // ── Terms / Semesters ────────────────────────────────────

    public function terms(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $selectedYear  = $request->academic_year_id
            ? AcademicYear::find($request->academic_year_id)
            : AcademicYear::current();
        $terms = AcademicTerm::where('academic_year_id', $selectedYear?->id)
            ->orderBy('order_position')->get();
        return view('academics.terms', compact('academicYears', 'selectedYear', 'terms'));
    }

    public function storeTerm(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:50',
            'type'             => 'required|in:term,semester,quarter',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'order_position'   => 'nullable|integer|min:1',
        ]);
        AcademicTerm::create($request->only(['academic_year_id', 'name', 'type', 'start_date', 'end_date', 'order_position']));
        return back()->with('success', 'Term "' . $request->name . '" added.');
    }

    public function updateTerm(Request $request, int $id)
    {
        $term = AcademicTerm::findOrFail($id);
        $request->validate([
            'name'           => 'required|string|max:50',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after:start_date',
            'order_position' => 'nullable|integer|min:1',
        ]);
        $term->update($request->only(['name', 'type', 'start_date', 'end_date', 'order_position']));
        return back()->with('success', 'Term updated.');
    }

    public function deleteTerm(int $id)
    {
        AcademicTerm::findOrFail($id)->delete();
        return back()->with('success', 'Term deleted.');
    }

    // ── Copy holidays from a previous year ─────────────────

    public function copyHolidays(Request $request)
    {
        $request->validate([
            'from_year_id' => 'required|exists:academic_years,id',
            'to_year_id'   => 'required|exists:academic_years,id|different:from_year_id',
        ]);
        $fromYearStart = AcademicYear::find($request->from_year_id)->start_date;
        $toYear        = AcademicYear::find($request->to_year_id);
        $fromHolidays  = Holiday::where('academic_year_id', $request->from_year_id)->get();
        $copied = 0;
        foreach ($fromHolidays as $h) {
            $offset = $h->date->diffInDays($fromYearStart, false);
            $newDate = $toYear->start_date->addDays($offset);
            Holiday::firstOrCreate(
                ['date' => $newDate, 'academic_year_id' => $toYear->id],
                ['name' => $h->name, 'type' => $h->type, 'description' => $h->description]
            );
            $copied++;
        }
        return back()->with('success', "{$copied} holidays copied to {$toYear->name}.");
    }

    // ── Working days configuration ────────────────────────

    public function workingDays()
    {
        $setting = SchoolSetting::first();
        $config  = json_decode($setting?->working_days_config ?? '{}', true);
        return view('academics.working-days', compact('config'));
    }

    public function saveWorkingDays(Request $request)
    {
        $days   = $request->input('days', []);
        $config = json_encode($days);
        $setting = SchoolSetting::firstOrCreate([]);
        if (!\Illuminate\Support\Facades\Schema::hasColumn('school_settings', 'working_days_config')) {
            return back()->with('error', 'Working days config column not in DB yet.');
        }
        $setting->update(['working_days_config' => $config]);
        return back()->with('success', 'Working days configuration saved.');
    }

    public function teacherTimetable(Request $request)
    {
        $teachers  = Employee::where('is_active', true)
            ->where(fn($q) => $q->where('employee_type', 'teaching')->orWhereNull('employee_type'))
            ->orderBy('first_name')->get();
        $timetable = collect();
        $teacher   = null;

        if ($request->teacher_id) {
            $teacher   = Employee::findOrFail($request->teacher_id);
            $timetable = \App\Models\Timetable::with(['subject', 'class', 'section'])
                ->where('teacher_id', $request->teacher_id)
                ->where('period_type', 'class')
                ->orderBy('day_of_week')->orderBy('period_number')
                ->get()->groupBy('day_of_week');
        }

        return view('academics.teacher-timetable', compact('teachers', 'timetable', 'teacher'));
    }

    // ── Teacher Workload Report ────────────────────────────

    public function teacherWorkload(Request $request)
    {
        $maxPerWeek = (int) \App\Models\SchoolSetting::get('teacher_max_periods_per_week', 30);
        $teachers   = Employee::where('is_active', true)
            ->where(fn($q) => $q->where('employee_type', 'teaching')->orWhereNull('employee_type'))
            ->orderBy('first_name')->get();

        $workload = $teachers->map(function ($teacher) use ($maxPerWeek) {
            $periods = \App\Models\Timetable::where('teacher_id', $teacher->id)
                ->where('period_type', 'class')->count();
            return [
                'teacher'    => $teacher,
                'periods'    => $periods,
                'max'        => $maxPerWeek,
                'pct'        => $maxPerWeek > 0 ? round(($periods / $maxPerWeek) * 100) : 0,
                'over'       => $periods > $maxPerWeek,
            ];
        });

        return view('academics.teacher-workload', compact('workload', 'maxPerWeek'));
    }

    public function timetableConflicts(Request $request)
    {
        // Teacher double-booked: same employee, same day, same period
        $teacherConflicts = \App\Models\Timetable::with(['class', 'section', 'subject', 'teacher'])
            ->whereNotNull('teacher_id')
            ->get()
            ->groupBy(fn($t) => $t->teacher_id . '_' . $t->day_of_week . '_' . $t->period_number)
            ->filter(fn($group) => $group->count() > 1)
            ->map(fn($group) => [
                'type'    => 'Teacher Double-Booked',
                'teacher' => $group->first()->teacher?->full_name ?? 'Unknown',
                'day'     => $group->first()->day_of_week,
                'period'  => $group->first()->period_number,
                'classes' => $group->map(fn($t) => ($t->class?->name ?? '?') . ($t->section ? ' ' . $t->section->name : ''))->implode(', '),
                'entries' => $group,
            ]);

        // Subject taught in same class by multiple teachers same period (shouldn't happen normally)
        $classConflicts = \App\Models\Timetable::with(['class', 'section', 'subject', 'teacher'])
            ->get()
            ->groupBy(fn($t) => $t->class_id . '_' . ($t->section_id ?? 0) . '_' . $t->day_of_week . '_' . $t->period_number)
            ->filter(fn($group) => $group->count() > 1)
            ->map(fn($group) => [
                'type'    => 'Class Double-Booked',
                'teacher' => $group->map(fn($t) => $t->teacher?->full_name)->filter()->implode(', '),
                'day'     => $group->first()->day_of_week,
                'period'  => $group->first()->period_number,
                'classes' => ($group->first()->class?->name ?? '?') . ($group->first()->section ? ' ' . $group->first()->section->name : ''),
                'entries' => $group,
            ]);

        $conflicts = $teacherConflicts->merge($classConflicts)->values();
        $totalConflicts = $conflicts->count();

        return view('academics.timetable-conflicts', compact('conflicts', 'totalConflicts'));
    }

    public function homeworkSubmissions(Request $request, int $id)
    {
        $homework    = \App\Models\Homework::with(['class', 'section', 'subject'])->findOrFail($id);
        $currentYear = AcademicYear::current();

        $enrollments = \App\Models\StudentEnrollment::with('student')
            ->where('class_id', $homework->class_id)
            ->where('status', 'active')
            ->when($homework->section_id, fn($q) => $q->where('section_id', $homework->section_id))
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->get();

        $submissions = \App\Models\HomeworkSubmission::where('homework_id', $id)
            ->get()->keyBy('student_id');

        return view('academics.homework-submissions', compact('homework', 'enrollments', 'submissions'));
    }

    public function saveSubmissionStatus(Request $request, int $id)
    {
        $request->validate(['submissions' => 'required|array']);
        $homework = \App\Models\Homework::findOrFail($id);

        DB::transaction(function () use ($request, $homework) {
            foreach ($request->submissions as $studentId => $data) {
                \App\Models\HomeworkSubmission::updateOrCreate(
                    ['homework_id' => $homework->id, 'student_id' => $studentId],
                    array_merge(
                        array_intersect_key($data, array_flip(['status','submitted_at','score','teacher_feedback'])),
                        ['evaluated_by' => Auth::id(), 'evaluated_at' => now()]
                    )
                );
            }
        });
        return back()->with('success', 'Submission statuses saved.');
    }

    public function homeworkCompletionReport(Request $request)
    {
        $classes  = Classes::active()->get();
        $subjects = Subject::where('is_active', true)->get();
        $rows     = collect();

        if ($request->class_id) {
            $homeworks = \App\Models\Homework::with(['subject', 'submissions'])
                ->where('class_id', $request->class_id)
                ->when($request->subject_id, fn($q) => $q->where('subject_id', $request->subject_id))
                ->latest('due_date')->paginate(20)->withQueryString();

            $currentYear = AcademicYear::current();
            $totalStudents = \App\Models\StudentEnrollment::where('class_id', $request->class_id)
                ->where('status', 'active')
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->count();

            return view('academics.homework-completion-report', compact('homeworks', 'classes', 'subjects', 'totalStudents'));
        }

        return view('academics.homework-completion-report', compact('classes', 'subjects'));
    }

    // ── Lesson Plans ─────────────────────────────────────────

    public function lessonPlans(Request $request)
    {
        $classes  = Classes::active()->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $currentYear = AcademicYear::current();

        $query = \App\Models\LessonPlan::with(['class', 'subject', 'createdBy'])
            ->when($request->class_id,   fn($q, $v) => $q->where('class_id', $v))
            ->when($request->subject_id, fn($q, $v) => $q->where('subject_id', $v))
            ->when($request->status,     fn($q, $v) => $q->where('status', $v))
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->latest('plan_date');

        $plans = $query->paginate(25)->withQueryString();

        // Syllabus topics for linking
        $syllabusTopics = collect();
        if ($request->class_id && $request->subject_id) {
            $syllabusTopics = \App\Models\Syllabus::where('class_id', $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->orderBy('chapter_number')->get();
        }

        return view('academics.lesson-plans', compact('plans', 'classes', 'subjects', 'syllabusTopics'));
    }

    public function storeLessonPlan(Request $request)
    {
        $request->validate([
            'class_id'            => 'required|exists:classes,id',
            'subject_id'          => 'required|exists:subjects,id',
            'topic'               => 'required|string|max:255',
            'plan_date'           => 'required|date',
            'learning_objectives' => 'nullable|string',
            'teaching_method'     => 'nullable|string',
            'resources_required'  => 'nullable|string',
            'period_number'       => 'nullable|string',
            'duration_minutes'    => 'nullable|integer|min:1',
        ]);
        $currentYear = AcademicYear::current();
        \App\Models\LessonPlan::create(array_merge($request->only([
            'class_id', 'subject_id', 'syllabus_id', 'topic',
            'learning_objectives', 'teaching_method', 'resources_required',
            'period_number', 'plan_date', 'duration_minutes', 'teacher_notes',
        ]), [
            'academic_year_id' => $currentYear?->id,
            'status'           => 'submitted',
            'created_by'       => auth()->id(),
        ]));
        return back()->with('success', 'Lesson plan submitted for HOD review.');
    }

    public function reviewLessonPlan(Request $request, int $id)
    {
        $request->validate(['decision' => 'required|in:approved,rejected', 'hod_remarks' => 'nullable|string']);
        \App\Models\LessonPlan::findOrFail($id)->update([
            'status'           => $request->decision === 'approved' ? 'approved' : 'rejected',
            'hod_decision'     => $request->decision,
            'hod_remarks'      => $request->hod_remarks,
            'hod_reviewed_by'  => auth()->id(),
            'hod_reviewed_at'  => now(),
        ]);
        return back()->with('success', 'Lesson plan ' . $request->decision . '.');
    }

    public function markLessonComplete(int $id)
    {
        $plan = \App\Models\LessonPlan::findOrFail($id);
        $plan->update(['is_completed' => true, 'completed_at' => now()]);
        // Auto-mark syllabus topic as done if linked
        if ($plan->syllabus_id) {
            \App\Models\Syllabus::where('id', $plan->syllabus_id)->update(['status' => 'completed']);
        }
        return back()->with('success', 'Lesson marked complete.');
    }

    public function lessonPlanPdf(int $id)
    {
        $plan   = \App\Models\LessonPlan::with(['class', 'subject', 'createdBy', 'hodReviewedBy', 'syllabus'])->findOrFail($id);
        $school = \App\Models\SchoolSetting::first();
        $pdf    = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.lesson-plan', compact('plan', 'school'));
        return $pdf->download('lesson-plan-' . $plan->id . '.pdf');
    }

    // ── Syllabus Coverage Report ──────────────────────────────

    public function syllabusCoverage(Request $request)
    {
        $classes  = Classes::active()->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();
        $coverage = collect();
        $currentYear = AcademicYear::current();

        if ($request->class_id) {
            $subjectIds = $request->subject_id
                ? [$request->subject_id]
                : Subject::where('is_active', true)->pluck('id')->toArray();

            foreach ($subjectIds as $sid) {
                $total     = \App\Models\Syllabus::where('class_id', $request->class_id)->where('subject_id', $sid)->count();
                $completed = \App\Models\Syllabus::where('class_id', $request->class_id)->where('subject_id', $sid)->where('status', 'completed')->count();
                if ($total > 0) {
                    $subject = Subject::find($sid);
                    $coverage->push([
                        'subject'    => $subject,
                        'total'      => $total,
                        'completed'  => $completed,
                        'pending'    => $total - $completed,
                        'percentage' => round($completed / $total * 100, 1),
                    ]);
                }
            }
        }

        return view('academics.syllabus-coverage', compact('classes', 'subjects', 'coverage'));
    }

    /* ------------------------------------------------------------------ */
    /*  Section Management                                                  */
    /* ------------------------------------------------------------------ */

    public function sectionsManage(Request $request)
    {
        $currentYear = AcademicYear::current();
        $classes = Classes::active()->with(['sections' => function ($q) use ($currentYear) {
            if ($currentYear) $q->where('academic_year_id', $currentYear->id);
            $q->with(['classTeacher', 'coClassTeacher']);
        }])->get();
        $teachers = User::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $selectedClass = $request->class_id ? Classes::find($request->class_id) : null;
        $sections = collect();
        if ($selectedClass) {
            $sections = Section::where('class_id', $selectedClass->id)
                ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
                ->with(['classTeacher', 'coClassTeacher'])
                ->withCount(['enrollments as active_students_count' => fn($q) => $q->where('status', 'active')])
                ->get();
        }
        return view('academics.sections-manage', compact('classes', 'teachers', 'selectedClass', 'sections', 'currentYear'));
    }

    public function updateSection(Request $request, int $id)
    {
        $section = Section::findOrFail($id);
        $validated = $request->validate([
            'name'                => 'required|string|max:20',
            'capacity'            => 'required|integer|min:1|max:200',
            'class_teacher_id'    => 'nullable|exists:users,id',
            'co_class_teacher_id' => 'nullable|exists:users,id',
            'is_active'           => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $section->update($validated);
        return back()->with('success', 'Section "' . $section->name . '" updated.');
    }

    public function deactivateSectionIfEmpty(int $id)
    {
        $section = Section::withCount(['enrollments as active_count' => fn($q) => $q->where('status', 'active')])->findOrFail($id);
        if ($section->active_count > 0) {
            return back()->with('error', 'Cannot deactivate: section has ' . $section->active_count . ' active students.');
        }
        $section->update(['is_active' => false]);
        return back()->with('success', 'Section "' . $section->name . '" deactivated.');
    }

    public function createSection(Request $request)
    {
        $currentYear = AcademicYear::current();
        if (!$currentYear) {
            return back()->with('error', 'No active academic year found. Please set a current academic year before creating sections.');
        }
        $validated = $request->validate([
            'class_id'            => 'required|exists:classes,id',
            'name'                => 'required|string|max:20',
            'capacity'            => 'required|integer|min:1|max:200',
            'class_teacher_id'    => 'nullable|exists:users,id',
            'co_class_teacher_id' => 'nullable|exists:users,id',
        ]);
        $validated['academic_year_id'] = $currentYear->id;
        $validated['is_active'] = true;
        Section::create($validated);
        return back()->with('success', 'Section "' . $validated['name'] . '" created.');
    }

    public function renameClass(Request $request, int $id)
    {
        $class = Classes::findOrFail($id);
        $request->validate(['name' => 'required|string|max:100']);
        $old = $class->name;
        $class->update(['name' => $request->name]);
        return back()->with('success', "Class renamed from \"{$old}\" to \"{$request->name}\".");
    }

    public function mergeSection(Request $request, int $id)
    {
        $request->validate(['target_section_id' => 'required|exists:sections,id|different:id']);
        $source = Section::withCount(['enrollments as active_count' => fn($q) => $q->where('status', 'active')])->findOrFail($id);
        $target = Section::findOrFail($request->target_section_id);

        if ($source->id === $target->id) {
            return back()->with('error', 'Source and target sections must be different.');
        }

        \DB::transaction(function () use ($source, $target) {
            StudentEnrollment::where('section_id', $source->id)
                ->where('status', 'active')
                ->update(['section_id' => $target->id]);
            $source->update(['is_active' => false]);
        });

        return back()->with('success', "Merged section \"{$source->name}\" into \"{$target->name}\". {$source->active_count} students moved.");
    }

    public function splitSection(Request $request, int $id)
    {
        $request->validate([
            'new_section_name' => 'required|string|max:20',
            'move_count'       => 'required|integer|min:1',
        ]);
        $source = Section::findOrFail($id);
        $currentYear = AcademicYear::current();

        $studentsToMove = StudentEnrollment::where('section_id', $source->id)
            ->where('status', 'active')
            ->orderBy('roll_number')
            ->limit($request->move_count)
            ->get();

        if ($studentsToMove->isEmpty()) {
            return back()->with('error', 'No active students found in this section to split.');
        }

        \DB::transaction(function () use ($source, $request, $studentsToMove, $currentYear) {
            $newSection = Section::create([
                'class_id'         => $source->class_id,
                'academic_year_id' => $currentYear?->id,
                'name'             => $request->new_section_name,
                'capacity'         => $source->capacity,
                'is_active'        => true,
            ]);
            StudentEnrollment::whereIn('id', $studentsToMove->pluck('id'))
                ->update(['section_id' => $newSection->id]);
        });

        return back()->with('success', "Split: {$studentsToMove->count()} students moved from \"{$source->name}\" to new section \"{$request->new_section_name}\".");
    }

    // ── Homework Calendar ─────────────────────────────────

    public function homeworkCalendar(Request $request)
    {
        $classes  = Classes::active()->orderBy('name')->get();
        $subjects = Subject::where('is_active', true)->orderBy('name')->get();

        $month   = (int)($request->month ?? now()->month);
        $year    = (int)($request->year ?? now()->year);
        $classId = $request->class_id;

        $start = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        $homework = \App\Models\Homework::with(['class', 'subject', 'createdBy'])
            ->whereBetween('due_date', [$start, $end])
            ->when($classId, fn($q, $v) => $q->where('class_id', $v))
            ->where('is_active', true)
            ->get()
            ->groupBy(fn($h) => $h->due_date->format('Y-m-d'));

        $calendar = [];
        $current  = $start->copy();
        while ($current->lte($end)) {
            $calendar[$current->format('Y-m-d')] = [
                'date'     => $current->copy(),
                'homework' => $homework[$current->format('Y-m-d')] ?? collect(),
            ];
            $current->addDay();
        }

        return view('academics.homework-calendar', compact(
            'classes', 'subjects', 'calendar', 'month', 'year', 'classId', 'start'
        ));
    }
}
