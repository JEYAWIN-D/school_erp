<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PortalController extends Controller
{
    // ── Helpers ──────────────────────────────────────────────

    private function getStudentForUser(): ?Student
    {
        $user = Auth::user();
        return $user->student_id ? Student::find($user->student_id) : null;
    }

    private function getChildrenForParent(): \Illuminate\Support\Collection
    {
        $studentIds = DB::table('parent_students')
            ->where('parent_user_id', Auth::id())
            ->pluck('student_id');

        return Student::whereIn('id', $studentIds)->get();
    }

    private function resolveChild(Request $request, $children): ?Student
    {
        $id = $request->get('student_id', $children->first()?->id);
        return $children->firstWhere('id', $id);
    }

    private function getEnrollment(int $studentId): ?object
    {
        try {
            return DB::table('student_enrollments as se')
                ->join('classes as c', 'c.id', '=', 'se.class_id')
                ->join('sections as s', 's.id', '=', 'se.section_id')
                ->where('se.student_id', $studentId)
                ->where('se.status', 'active')
                ->select('se.*', 'c.name as class_name', 's.name as section_name')
                ->first();
        } catch (\Exception $e) {
            return null;
        }
    }

    private function attendanceSummary(int $studentId, Carbon $from, Carbon $to): array
    {
        try {
            $records = DB::table('attendance_records')
                ->where('student_id', $studentId)
                ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
                ->get()
                ->keyBy('date');
        } catch (\Exception $e) {
            $records = collect();
        }

        $total   = (int) $from->diffInDays($to) + 1;
        $present = $records->whereIn('status', ['present', 'late'])->count();
        $absent  = $records->where('status', 'absent')->count();
        $leave   = $records->where('status', 'on_leave')->count();

        return compact('records', 'total', 'present', 'absent', 'leave');
    }

    private function getFeeBalance(int $studentId): array
    {
        $totalDue  = 0;
        $totalPaid = 0;
        try { $totalDue  = DB::table('student_fee_charges')->where('student_id', $studentId)->sum('amount') ?? 0; } catch (\Exception $e) {}
        try { $totalPaid = DB::table('fee_payments')->where('student_id', $studentId)->where('is_cancelled', false)->sum('total_paid') ?? 0; } catch (\Exception $e) {}
        return ['due' => (float)$totalDue, 'paid' => (float)$totalPaid, 'balance' => max(0, $totalDue - $totalPaid)];
    }

    private function getRecentMarks(int $studentId, int $limit = 5): \Illuminate\Support\Collection
    {
        try {
            return DB::table('exam_marks as em')
                ->join('exam_schedules as es', 'es.id', '=', 'em.exam_schedule_id')
                ->join('exams as ex', 'ex.id', '=', 'es.exam_id')
                ->join('subjects as sub', 'sub.id', '=', 'es.subject_id')
                ->where('em.student_id', $studentId)
                ->orderByDesc('es.exam_date')
                ->select(
                    'ex.name as exam_name',
                    'sub.name as subject',
                    'em.marks_obtained',
                    'es.total_marks as max_marks',
                    'em.grade'
                )
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getNotices(int $limit = 5): \Illuminate\Support\Collection
    {
        try {
            return DB::table('notices')
                ->where('is_published', true)
                ->whereRaw('(expiry_date IS NULL OR expiry_date >= CURDATE())')
                ->orderByDesc('created_at')
                ->limit($limit)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getHomework(int $classId, int $days = 7): \Illuminate\Support\Collection
    {
        if (!$classId) return collect();
        try {
            return DB::table('homework as hw')
                ->leftJoin('subjects as sub', 'sub.id', '=', 'hw.subject_id')
                ->where('hw.class_id', $classId)
                ->where('hw.is_active', true)
                ->where('hw.due_date', '>=', today()->toDateString())
                ->where('hw.due_date', '<=', today()->addDays($days)->toDateString())
                ->orderBy('hw.due_date')
                ->select('hw.*', 'sub.name as subject_name')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    // ── Parent Portal ─────────────────────────────────────────

    public function parentDashboard(Request $request)
    {
        $children = $this->getChildrenForParent();
        if ($children->isEmpty()) {
            return view('portal.parent.no-children');
        }

        $child = $this->resolveChild($request, $children);
        if (!$child) abort(403);

        $enrollment = $this->getEnrollment($child->id);

        $todayAtt = null;
        try {
            $todayAtt = DB::table('attendance_records')
                ->where('student_id', $child->id)
                ->where('date', today()->toDateString())
                ->first();
        } catch (\Exception $e) {}

        $attSummary = $this->attendanceSummary($child->id, now()->startOfMonth(), now()->endOfMonth());
        $fees        = $this->getFeeBalance($child->id);
        $feeBalance  = $fees['balance'];
        $recentMarks = $this->getRecentMarks($child->id);
        $notices     = $this->getNotices();
        $homework    = $this->getHomework($enrollment?->class_id ?? 0);

        return view('portal.parent.dashboard', compact(
            'children', 'child', 'enrollment', 'todayAtt',
            'attSummary', 'feeBalance', 'recentMarks', 'notices', 'homework'
        ));
    }

    public function parentAttendance(Request $request)
    {
        $children = $this->getChildrenForParent();
        $child    = $this->resolveChild($request, $children);
        if (!$child) abort(403);

        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);
        $from  = Carbon::create($year, $month, 1)->startOfMonth();
        $to    = $from->copy()->endOfMonth();

        $attData    = $this->attendanceSummary($child->id, $from, $to);
        $percentage = $attData['total'] > 0
            ? round($attData['present'] / $attData['total'] * 100, 1)
            : null;

        $ytd = $this->ytdAttendance($child->id);

        return view('portal.parent.attendance', compact(
            'children', 'child', 'attData', 'from', 'to', 'month', 'year', 'percentage', 'ytd'
        ));
    }

    public function parentFees(Request $request)
    {
        $children = $this->getChildrenForParent();
        $child    = $this->resolveChild($request, $children);
        if (!$child) abort(403);

        $invoices = collect();
        try {
            $invoices = DB::table('student_fee_charges as sfc')
                ->leftJoin('fee_heads as fh', 'fh.id', '=', 'sfc.fee_head_id')
                ->where('sfc.student_id', $child->id)
                ->orderBy('sfc.due_date')
                ->select('sfc.*', 'fh.name as fee_head_name')
                ->get();
        } catch (\Exception $e) {}

        $payments = collect();
        try {
            $payments = DB::table('fee_payments')
                ->where('student_id', $child->id)
                ->where('is_cancelled', false)
                ->orderByDesc('payment_date')
                ->get();
        } catch (\Exception $e) {}

        // Compute per-fee-head paid amounts to determine invoice status
        $paidByHead = $payments->groupBy('fee_head_id')->map(fn($g) => $g->sum('total_paid'));
        $invoices = $invoices->map(function ($inv) use ($paidByHead) {
            $paid = $paidByHead->get($inv->fee_head_id, 0);
            $inv->paid_amount = $paid;
            $inv->status = $paid >= $inv->amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
            return $inv;
        });

        $totalDue  = $invoices->sum('amount');
        $totalPaid = $payments->sum('total_paid');
        $balance   = max(0, $totalDue - $totalPaid);

        return view('portal.parent.fees', compact(
            'children', 'child', 'invoices', 'payments', 'totalDue', 'totalPaid', 'balance'
        ));
    }

    public function parentExams(Request $request)
    {
        $children = $this->getChildrenForParent();
        $child    = $this->resolveChild($request, $children);
        if (!$child) abort(403);

        $enrollment = $this->getEnrollment($child->id);

        $results = collect();
        try {
            $results = DB::table('exam_marks as em')
                ->join('exam_schedules as es', 'es.id', '=', 'em.exam_schedule_id')
                ->join('exams as ex', 'ex.id', '=', 'es.exam_id')
                ->join('subjects as sub', 'sub.id', '=', 'es.subject_id')
                ->where('em.student_id', $child->id)
                ->orderByDesc('es.exam_date')
                ->select(
                    'ex.name as exam_name', 'es.exam_date',
                    'sub.name as subject_name',
                    'em.marks_obtained', 'es.total_marks as max_marks',
                    'em.grade',
                    DB::raw("CASE WHEN em.is_absent=1 THEN 'Absent' WHEN em.marks_obtained >= es.passing_marks THEN 'Pass' ELSE 'Fail' END as pass_status")
                )
                ->get()
                ->groupBy('exam_name');
        } catch (\Exception $e) {}

        $upcoming = collect();
        try {
            $upcoming = DB::table('exam_schedules as es')
                ->join('exams as ex', 'ex.id', '=', 'es.exam_id')
                ->join('subjects as sub', 'sub.id', '=', 'es.subject_id')
                ->where('es.class_id', $enrollment?->class_id ?? 0)
                ->where('es.exam_date', '>=', today()->toDateString())
                ->orderBy('es.exam_date')
                ->limit(15)
                ->select('ex.name as exam_name', 'sub.name as subject_name', 'es.exam_date', 'es.start_time', 'es.end_time', 'es.total_marks')
                ->get();
        } catch (\Exception $e) {}

        return view('portal.parent.exams', compact('children', 'child', 'results', 'upcoming'));
    }

    public function parentNotices(Request $request)
    {
        $children = $this->getChildrenForParent();
        $notices  = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        try {
            $userId  = Auth::id();
            $notices = DB::table('notices')
                ->leftJoin('notice_reads as nr', function($j) use ($userId) {
                    $j->on('nr.notice_id', '=', 'notices.id')->where('nr.user_id', '=', $userId);
                })
                ->where('notices.is_published', true)
                ->whereRaw('(notices.expiry_date IS NULL OR notices.expiry_date >= CURDATE())')
                ->orderByDesc('notices.created_at')
                ->select('notices.*', 'nr.read_at')
                ->paginate(15);
        } catch (\Exception $e) {}

        return view('portal.parent.notices', compact('children', 'notices'));
    }

    public function parentProfile(Request $request)
    {
        $user     = Auth::user();
        $children = $this->getChildrenForParent();
        return view('portal.parent.profile', compact('user', 'children'));
    }

    public function updateParentProfile(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'mobile' => 'required|string|max:15',
            'email'  => 'required|email|unique:users,email,' . Auth::id(),
        ]);
        Auth::user()->update($request->only('name', 'mobile', 'email'));
        return back()->with('success', 'Profile updated successfully.');
    }

    // ── Student Portal ────────────────────────────────────────

    public function studentDashboard(Request $request)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403, 'No student record linked to your account.');

        $enrollment = $this->getEnrollment($student->id);

        // Today's timetable (day_of_week is tinyint: 1=Mon..7=Sun ISO)
        $dayOfWeek = (int) now()->format('N');
        $timetable = collect();
        try {
            $timetable = DB::table('timetables as t')
                ->join('subjects as s', 's.id', '=', 't.subject_id')
                ->where('t.section_id', $enrollment?->section_id ?? 0)
                ->where('t.day_of_week', $dayOfWeek)
                ->where('t.is_active', true)
                ->orderBy('t.start_time')
                ->select('s.name as subject', 't.start_time', 't.end_time')
                ->get();
        } catch (\Exception $e) {}

        $todayAtt = null;
        try {
            $todayAtt = DB::table('attendance_records')
                ->where('student_id', $student->id)
                ->where('date', today()->toDateString())
                ->first();
        } catch (\Exception $e) {}

        $attSummary = $this->attendanceSummary($student->id, now()->startOfMonth(), now()->endOfMonth());
        $attPct = $attSummary['total'] > 0
            ? round($attSummary['present'] / $attSummary['total'] * 100, 1)
            : null;

        $homework    = $this->getHomework($enrollment?->class_id ?? 0);
        $notices     = $this->getNotices(4);
        $recentMarks = $this->getRecentMarks($student->id);
        $fees        = $this->getFeeBalance($student->id);
        $feeBalance  = $fees['balance'];

        return view('portal.student.dashboard', compact(
            'student', 'enrollment', 'timetable', 'todayAtt',
            'attSummary', 'attPct', 'homework', 'notices', 'recentMarks', 'feeBalance'
        ));
    }

    public function studentTimetable(Request $request)
    {
        $student    = $this->getStudentForUser();
        if (!$student) abort(403);
        $enrollment = $this->getEnrollment($student->id);

        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'];
        $timetable = [];
        try {
            $rows = DB::table('timetables as t')
                ->join('subjects as s', 's.id', '=', 't.subject_id')
                ->leftJoin('employees as e', 'e.id', '=', 't.teacher_id')
                ->where('t.section_id', $enrollment?->section_id ?? 0)
                ->where('t.is_active', true)
                ->orderBy('t.day_of_week')
                ->orderBy('t.start_time')
                ->select('t.day_of_week', 't.start_time', 't.end_time', 't.room', 't.period_type',
                         's.name as subject', 'e.first_name as teacher_first', 'e.last_name as teacher_last')
                ->get();

            foreach ($days as $d => $label) {
                $timetable[$d] = ['label' => $label, 'periods' => $rows->where('day_of_week', $d)->values()];
            }
        } catch (\Exception $e) {
            foreach ($days as $d => $label) {
                $timetable[$d] = ['label' => $label, 'periods' => collect()];
            }
        }

        $todayDow = (int) now()->format('N');
        return view('portal.student.timetable', compact('student', 'enrollment', 'timetable', 'todayDow'));
    }

    public function studentAttendance(Request $request)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);
        $from  = Carbon::create($year, $month, 1)->startOfMonth();
        $to    = $from->copy()->endOfMonth();

        $attData    = $this->attendanceSummary($student->id, $from, $to);
        $percentage = $attData['total'] > 0
            ? round($attData['present'] / $attData['total'] * 100, 1)
            : null;

        // YTD summary (current academic year start to today)
        $ytd = $this->ytdAttendance($student->id);

        $leaveRequests = collect();
        try {
            $leaveRequests = DB::table('student_leave_requests')
                ->where('student_id', $student->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {}

        return view('portal.student.attendance', compact(
            'student', 'attData', 'from', 'to', 'month', 'year', 'percentage', 'ytd', 'leaveRequests'
        ));
    }

    public function studentFees(Request $request)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $invoices = collect();
        try {
            $invoices = DB::table('student_fee_charges as sfc')
                ->leftJoin('fee_heads as fh', 'fh.id', '=', 'sfc.fee_head_id')
                ->where('sfc.student_id', $student->id)
                ->orderBy('sfc.due_date')
                ->select('sfc.*', 'fh.name as fee_head_name')
                ->get();
        } catch (\Exception $e) {}

        $payments = collect();
        try {
            $payments = DB::table('fee_payments')
                ->where('student_id', $student->id)
                ->where('is_cancelled', false)
                ->orderByDesc('payment_date')
                ->get();
        } catch (\Exception $e) {}

        // Compute per-fee-head paid amounts to determine invoice status
        $paidByHead = $payments->groupBy('fee_head_id')->map(fn($g) => $g->sum('total_paid'));
        $invoices = $invoices->map(function ($inv) use ($paidByHead) {
            $paid = $paidByHead->get($inv->fee_head_id, 0);
            $inv->paid_amount = $paid;
            $inv->status = $paid >= $inv->amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
            return $inv;
        });

        $totalDue  = $invoices->sum('amount');
        $totalPaid = $payments->sum('total_paid');
        $balance   = max(0, $totalDue - $totalPaid);

        return view('portal.student.fees', compact(
            'student', 'invoices', 'payments', 'totalDue', 'totalPaid', 'balance'
        ));
    }

    public function studentExams(Request $request)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $enrollment = $this->getEnrollment($student->id);

        $results = collect();
        try {
            $results = DB::table('exam_marks as em')
                ->join('exam_schedules as es', 'es.id', '=', 'em.exam_schedule_id')
                ->join('exams as ex', 'ex.id', '=', 'es.exam_id')
                ->join('subjects as sub', 'sub.id', '=', 'es.subject_id')
                ->where('em.student_id', $student->id)
                ->orderByDesc('es.exam_date')
                ->select(
                    'ex.name as exam_name', 'es.exam_date',
                    'sub.name as subject_name',
                    'em.marks_obtained', 'es.total_marks as max_marks',
                    'em.grade',
                    DB::raw("CASE WHEN em.is_absent=1 THEN 'Absent' WHEN em.marks_obtained >= es.passing_marks THEN 'Pass' ELSE 'Fail' END as pass_status")
                )
                ->get()
                ->groupBy('exam_name');
        } catch (\Exception $e) {}

        $upcoming = collect();
        try {
            $upcoming = DB::table('exam_schedules as es')
                ->join('exams as ex', 'ex.id', '=', 'es.exam_id')
                ->join('subjects as sub', 'sub.id', '=', 'es.subject_id')
                ->where('es.class_id', $enrollment?->class_id ?? 0)
                ->where('es.exam_date', '>=', today()->toDateString())
                ->orderBy('es.exam_date')
                ->limit(15)
                ->select('ex.name as exam_name', 'sub.name as subject_name', 'es.exam_date', 'es.start_time', 'es.end_time', 'es.total_marks')
                ->get();
        } catch (\Exception $e) {}

        return view('portal.student.exams', compact('student', 'results', 'upcoming'));
    }

    public function studentAcademics(Request $request)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $enrollment = null;
        try {
            $enrollment = DB::table('student_enrollments')
                ->where('student_id', $student->id)
                ->where('status', 'active')
                ->first();
        } catch (\Exception $e) {}

        $homework = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        try {
            $homework = DB::table('homework as hw')
                ->leftJoin('subjects as sub', 'sub.id', '=', 'hw.subject_id')
                ->where('hw.class_id', $enrollment?->class_id ?? 0)
                ->where('hw.is_active', true)
                ->orderByDesc('hw.due_date')
                ->select('hw.*', 'sub.name as subject_name')
                ->paginate(15);
        } catch (\Exception $e) {}

        $courses = collect();
        try {
            $courses = \App\Models\LmsCourse::where('class_id', $enrollment?->class_id ?? 0)
                ->where('status', 'published')
                ->with(['units.lessons'])
                ->get();
        } catch (\Exception $e) {}

        $assignments = collect();
        try {
            $assignments = DB::table('lms_assignments as la')
                ->join('lms_courses as lc', 'lc.id', '=', 'la.course_id')
                ->leftJoin('lms_assignment_submissions as las', function($j) use ($student) {
                    $j->on('las.assignment_id', '=', 'la.id')->where('las.student_id', '=', $student->id);
                })
                ->where('lc.class_id', $enrollment?->class_id ?? 0)
                ->where('lc.status', 'published')
                ->orderBy('la.due_at')
                ->select('la.*', 'lc.title as course_title', 'las.submitted_at', 'las.score', 'las.feedback')
                ->get();
        } catch (\Exception $e) {}

        return view('portal.student.academics', compact('student', 'enrollment', 'homework', 'courses', 'assignments'));
    }

    public function studentCourse(Request $request, int $id)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        try {
            $course = \App\Models\LmsCourse::with(['units.lessons'])->findOrFail($id);
        } catch (\Exception $e) {
            abort(404);
        }

        return view('portal.student.course', compact('student', 'course'));
    }

    public function studentNotices(Request $request)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $notices = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        try {
            $userId  = Auth::id();
            $notices = DB::table('notices')
                ->leftJoin('notice_reads as nr', function($j) use ($userId) {
                    $j->on('nr.notice_id', '=', 'notices.id')->where('nr.user_id', '=', $userId);
                })
                ->where('notices.is_published', true)
                ->whereRaw('(notices.expiry_date IS NULL OR notices.expiry_date >= CURDATE())')
                ->orderByDesc('notices.created_at')
                ->select('notices.*', 'nr.read_at')
                ->paginate(20);
        } catch (\Exception $e) {}

        return view('portal.student.notices', compact('student', 'notices'));
    }

    public function studentProfile(Request $request)
    {
        $student = $this->getStudentForUser();
        $user    = Auth::user();
        if (!$student) abort(403);
        return view('portal.student.profile', compact('student', 'user'));
    }

    public function updateStudentProfile(Request $request)
    {
        $request->validate([
            'email'  => 'required|email|unique:users,email,' . Auth::id(),
            'mobile' => 'nullable|string|max:15',
        ]);
        Auth::user()->update($request->only('email', 'mobile'));
        return back()->with('success', 'Contact details updated.');
    }

    // ── Leave Application ─────────────────────────────────────

    public function submitLeave(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string|max:50',
            'from_date'  => 'required|date|after_or_equal:today',
            'to_date'    => 'required|date|after_or_equal:from_date',
            'reason'     => 'required|string|max:500',
        ]);

        $student = $this->getStudentForUser();
        if (!$student) {
            // Parent submitting leave
            $children = $this->getChildrenForParent();
            $studentId = $request->integer('student_id', $children->first()?->id);
        } else {
            $studentId = $student->id;
        }

        $from = Carbon::parse($request->from_date);
        $to   = Carbon::parse($request->to_date);
        $days = (int) $from->diffInDays($to) + 1;

        try {
            DB::table('student_leave_requests')->insert([
                'student_id'  => $studentId,
                'leave_type'  => $request->leave_type,
                'from_date'   => $request->from_date,
                'to_date'     => $request->to_date,
                'days'        => $days,
                'reason'      => $request->reason,
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Could not submit leave request. Please try again.']);
        }

        return back()->with('success', "Leave request submitted for $days day(s). Pending approval.");
    }

    // ── Notice Read Receipt ───────────────────────────────────

    public function markNoticeRead(Request $request, int $noticeId)
    {
        $userId = Auth::id();
        try {
            DB::table('notice_reads')->updateOrInsert(
                ['notice_id' => $noticeId, 'user_id' => $userId],
                ['read_at' => now(), 'updated_at' => now(), 'created_at' => now()]
            );
        } catch (\Exception $e) {}

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back();
    }

    public function markAllNoticesRead(Request $request)
    {
        $userId = Auth::id();
        try {
            $noticeIds = DB::table('notices')
                ->where('is_published', true)
                ->whereRaw('(expiry_date IS NULL OR expiry_date >= CURDATE())')
                ->pluck('id');
            foreach ($noticeIds as $noticeId) {
                DB::table('notice_reads')->updateOrInsert(
                    ['notice_id' => $noticeId, 'user_id' => $userId],
                    ['read_at' => now(), 'updated_at' => now(), 'created_at' => now()]
                );
            }
        } catch (\Exception $e) {}

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }
        return back()->with('success', 'All notices marked as read.');
    }

    // ── Notifications bell ────────────────────────────────────

    public function notifications(Request $request)
    {
        $userId = Auth::id();
        $items  = collect();
        try {
            $items = DB::table('notices')
                ->leftJoin('notice_reads as nr', function($j) use ($userId) {
                    $j->on('nr.notice_id', '=', 'notices.id')->where('nr.user_id', '=', $userId);
                })
                ->where('notices.is_published', true)
                ->whereRaw('(notices.expiry_date IS NULL OR notices.expiry_date >= CURDATE())')
                ->orderByDesc('notices.created_at')
                ->limit(10)
                ->select('notices.id', 'notices.title', 'notices.created_at', DB::raw('nr.read_at IS NULL as unread'))
                ->get()
                ->map(fn($n) => [
                    'id'     => $n->id,
                    'title'  => $n->title,
                    'date'   => \Carbon\Carbon::parse($n->created_at)->diffForHumans(),
                    'unread' => (bool) $n->unread,
                ]);
        } catch (\Exception $e) {}

        return response()->json([
            'unread_count' => $items->where('unread', true)->count(),
            'items'        => $items->values(),
        ]);
    }

    // ── Assignment Submission ─────────────────────────────────

    public function submitAssignment(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|integer',
            'note'          => 'nullable|string|max:2000',
            'file'          => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $filePath = null;
        if ($request->hasFile('file')) {
            try {
                $filePath = $request->file('file')->store('assignment-submissions/' . $student->id, 'public');
            } catch (\Exception $e) {}
        }

        $isLate = false;
        try {
            $asgn = DB::table('lms_assignments')->where('id', $request->assignment_id)->first();
            $isLate = $asgn && Carbon::parse($asgn->due_at)->isPast();
        } catch (\Exception $e) {}

        try {
            DB::table('lms_assignment_submissions')->updateOrInsert(
                ['assignment_id' => $request->assignment_id, 'student_id' => $student->id],
                array_filter([
                    'note'         => $request->note,
                    'file_path'    => $filePath,
                    'submitted_at' => now(),
                    'is_late'      => $isLate,
                ], fn($v) => $v !== null)
            );
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Could not submit assignment. Please try again.']);
        }

        return back()->with('assignment_success', 'Assignment submitted successfully!');
    }

    // ── PDF Generation ────────────────────────────────────────

    public function studentFeePdf(Request $request)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $invoices = collect();
        try {
            $invoices = DB::table('student_fee_charges as sfc')
                ->leftJoin('fee_heads as fh', 'fh.id', '=', 'sfc.fee_head_id')
                ->where('sfc.student_id', $student->id)
                ->orderBy('sfc.due_date')
                ->select('sfc.*', 'fh.name as fee_head_name')
                ->get();
        } catch (\Exception $e) {}

        $payments = collect();
        try {
            $payments = DB::table('fee_payments')
                ->where('student_id', $student->id)
                ->where('is_cancelled', false)
                ->orderByDesc('payment_date')
                ->get();
        } catch (\Exception $e) {}

        $totalDue  = $invoices->sum('amount');
        $totalPaid = $payments->sum('total_paid');
        $balance   = max(0, $totalDue - $totalPaid);
        $enrollment = $this->getEnrollment($student->id);

        $pdf = Pdf::loadView('portal.pdf.fee-receipt', compact(
            'student', 'enrollment', 'invoices', 'payments', 'totalDue', 'totalPaid', 'balance'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('fee-statement-' . $student->admission_no . '.pdf');
    }

    public function parentFeePdf(Request $request)
    {
        $children = $this->getChildrenForParent();
        $child    = $this->resolveChild($request, $children);
        if (!$child) abort(403);

        $invoices = collect();
        try {
            $invoices = DB::table('student_fee_charges as sfc')
                ->leftJoin('fee_heads as fh', 'fh.id', '=', 'sfc.fee_head_id')
                ->where('sfc.student_id', $child->id)
                ->orderBy('sfc.due_date')
                ->select('sfc.*', 'fh.name as fee_head_name')
                ->get();
        } catch (\Exception $e) {}

        $payments = collect();
        try {
            $payments = DB::table('fee_payments')
                ->where('student_id', $child->id)
                ->where('is_cancelled', false)
                ->orderByDesc('payment_date')
                ->get();
        } catch (\Exception $e) {}

        $totalDue  = $invoices->sum('amount');
        $totalPaid = $payments->sum('total_paid');
        $balance   = max(0, $totalDue - $totalPaid);
        $enrollment = $this->getEnrollment($child->id);
        $student = $child;

        $pdf = Pdf::loadView('portal.pdf.fee-receipt', compact(
            'student', 'enrollment', 'invoices', 'payments', 'totalDue', 'totalPaid', 'balance'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('fee-statement-' . $child->admission_no . '.pdf');
    }

    public function studentReportPdf(Request $request)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $enrollment = $this->getEnrollment($student->id);

        $results = collect();
        try {
            $results = DB::table('exam_marks as em')
                ->join('exam_schedules as es', 'es.id', '=', 'em.exam_schedule_id')
                ->join('exams as ex', 'ex.id', '=', 'es.exam_id')
                ->join('subjects as sub', 'sub.id', '=', 'es.subject_id')
                ->where('em.student_id', $student->id)
                ->orderByDesc('es.exam_date')
                ->select(
                    'ex.name as exam_name', 'es.exam_date',
                    'sub.name as subject_name',
                    'em.marks_obtained', 'es.total_marks as max_marks', 'em.grade',
                    DB::raw("CASE WHEN em.is_absent=1 THEN 'Absent' WHEN em.marks_obtained >= es.passing_marks THEN 'Pass' ELSE 'Fail' END as pass_status")
                )
                ->get()
                ->groupBy('exam_name');
        } catch (\Exception $e) {}

        $pdf = Pdf::loadView('portal.pdf.report-card', compact(
            'student', 'enrollment', 'results'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('report-card-' . $student->admission_no . '.pdf');
    }

    // ── Hall Ticket PDF ──────────────────────────────────────

    public function studentHallTicket(Request $request)
    {
        $student    = $this->getStudentForUser();
        if (!$student) abort(403);

        $enrollment = $this->getEnrollment($student->id);
        $school     = DB::table('school_settings')->first();

        $upcoming = collect();
        try {
            $upcoming = DB::table('exam_schedules as es')
                ->join('exams as ex', 'ex.id', '=', 'es.exam_id')
                ->join('subjects as sub', 'sub.id', '=', 'es.subject_id')
                ->where('es.class_id', $enrollment?->class_id ?? 0)
                ->where('ex.is_published', true)
                ->where('es.exam_date', '>=', today()->toDateString())
                ->orderBy('es.exam_date')
                ->select('ex.name as exam_name', 'sub.name as subject_name',
                    'es.exam_date', 'es.start_time', 'es.end_time', 'es.total_marks', 'es.passing_marks')
                ->get()
                ->groupBy('exam_name');
        } catch (\Exception $e) {}

        $pdf = Pdf::loadView('portal.pdf.hall-ticket', compact(
            'student', 'enrollment', 'school', 'upcoming'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('hall-ticket-' . $student->admission_no . '.pdf');
    }

    // ── Individual Payment Receipt PDF ───────────────────────

    public function studentPaymentReceipt(Request $request, int $paymentId)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $payment = DB::table('fee_payments as fp')
            ->leftJoin('fee_heads as fh', 'fh.id', '=', 'fp.fee_head_id')
            ->where('fp.id', $paymentId)
            ->where('fp.student_id', $student->id)
            ->where('fp.is_cancelled', false)
            ->select('fp.*', 'fh.name as fee_head_name')
            ->first();

        if (!$payment) abort(404);

        $school = DB::table('school_settings')->first();
        $enrollment = $this->getEnrollment($student->id);

        $pdf = Pdf::loadView('portal.pdf.payment-receipt', compact(
            'student', 'payment', 'school', 'enrollment'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('receipt-' . $payment->receipt_number . '.pdf');
    }

    public function parentPaymentReceipt(Request $request, int $paymentId)
    {
        $children = $this->getChildrenForParent();
        $child    = $this->resolveChild($request, $children);
        if (!$child) abort(403);

        $payment = DB::table('fee_payments as fp')
            ->leftJoin('fee_heads as fh', 'fh.id', '=', 'fp.fee_head_id')
            ->where('fp.id', $paymentId)
            ->where('fp.student_id', $child->id)
            ->where('fp.is_cancelled', false)
            ->select('fp.*', 'fh.name as fee_head_name')
            ->first();

        if (!$payment) abort(404);

        $school      = DB::table('school_settings')->first();
        $enrollment  = $this->getEnrollment($child->id);

        $student = $child;
        $pdf = Pdf::loadView('portal.pdf.payment-receipt', compact(
            'student', 'payment', 'school', 'enrollment'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('receipt-' . $payment->receipt_number . '.pdf');
    }

    // ── YTD Attendance Helper ─────────────────────────────────

    private function ytdAttendance(int $studentId): array
    {
        try {
            $yearStart = DB::table('academic_years')->where('is_current', true)->value('start_date')
                ?? now()->startOfYear()->toDateString();
            $records = DB::table('attendance_records')
                ->where('student_id', $studentId)
                ->whereBetween('date', [$yearStart, today()->toDateString()])
                ->get();

            $present  = $records->whereIn('status', ['present', 'late'])->count();
            $absent   = $records->where('status', 'absent')->count();
            $leave    = $records->where('status', 'on_leave')->count();
            $total    = $present + $absent;
            $pct      = $total > 0 ? round($present / $total * 100, 1) : null;

            return compact('present', 'absent', 'leave', 'total', 'pct');
        } catch (\Exception $e) {
            return ['present' => 0, 'absent' => 0, 'leave' => 0, 'total' => 0, 'pct' => null];
        }
    }

    // ── Quiz ─────────────────────────────────────────────────

    public function takeQuiz(Request $request, int $quizId)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $quiz = \App\Models\LmsQuiz::with(['questions', 'course'])->findOrFail($quizId);
        if (!$quiz->isAvailable()) {
            return back()->with('error', 'This quiz is not currently available.');
        }

        $existing = \App\Models\LmsQuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $student->id)
            ->whereNotNull('submitted_at')
            ->latest()->first();

        $questions = $quiz->randomise ? $quiz->questions->shuffle() : $quiz->questions;

        return view('portal.student.quiz', compact('quiz', 'questions', 'student', 'existing'));
    }

    public function submitQuiz(Request $request, int $quizId)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $quiz      = \App\Models\LmsQuiz::with('questions')->findOrFail($quizId);
        $attempt   = \App\Models\LmsQuizAttempt::create([
            'quiz_id'    => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now()->subMinutes(1),
            'submitted_at' => now(),
            'status'     => 'completed',
            'score'      => 0,
            'total_marks'=> $quiz->questions->sum('marks') ?: $quiz->questions->count() * ($quiz->marks_per_question ?: 1),
        ]);

        $score = 0;
        foreach ($quiz->questions as $q) {
            $given = $request->input("answers.{$q->id}", '');
            $correct = $q->isCorrect($given);
            $mark = $correct ? ($q->marks ?: $quiz->marks_per_question ?: 1) : -abs($quiz->negative_marks ?? 0);
            $score += max(0, $mark);
            \App\Models\LmsQuizAnswer::create([
                'attempt_id'     => $attempt->id,
                'question_id'    => $q->id,
                'given_answer'   => $given,
                'is_correct'     => $correct,
                'marks_awarded'  => max(0, $mark),
            ]);
        }

        $attempt->update(['score' => $score]);

        return redirect()->route('portal.quiz.result', $attempt->id)
            ->with('success', "Quiz submitted! You scored {$score}/{$attempt->total_marks}.");
    }

    public function quizResult(Request $request, int $attemptId)
    {
        $student = $this->getStudentForUser();
        if (!$student) abort(403);

        $attempt = \App\Models\LmsQuizAttempt::with(['quiz.questions', 'answers'])->findOrFail($attemptId);
        if ($attempt->student_id !== $student->id) abort(403);

        return view('portal.student.quiz-result', compact('attempt', 'student'));
    }

    // ── Shared ────────────────────────────────────────────────

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password changed successfully.');
    }
}
