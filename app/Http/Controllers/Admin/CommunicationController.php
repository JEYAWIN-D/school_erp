<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommunicationLog;
use App\Models\StaffNotice;
use App\Models\StaffNoticeRead;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CommunicationController extends Controller
{
    public function index()
    {
        $recentLogs   = CommunicationLog::latest()->limit(5)->get();
        $staffNotices = StaffNotice::latest()->limit(5)->get();

        $totalEmailSent = CommunicationLog::where('type', 'email')->where('status', 'sent')->count();
        $totalSmsSent   = CommunicationLog::where('type', 'sms')->where('status', 'sent')->count();
        $todaySent      = CommunicationLog::whereDate('sent_at', today())->where('status', 'sent')->count();
        $totalStaffNotices = StaffNotice::count();
        $unreadCount    = StaffNotice::whereDoesntHave('reads', fn($q) => $q->where('user_id', Auth::id()))->count();

        $portalAccounts = 0;
        try {
            $portalAccounts = User::whereNotNull('student_id')->where('is_active', true)->count();
            // Add parent portal users (those linked in parent_students table)
            $parentAccounts = DB::table('parent_students')->distinct()->count('parent_user_id');
            $portalAccounts = $portalAccounts + (int)$parentAccounts;
        } catch (\Exception $e) {
            try { $portalAccounts = User::where('is_active', true)->count(); } catch (\Exception $e2) {}
        }

        return view('communication.index', compact(
            'recentLogs', 'staffNotices',
            'totalEmailSent', 'totalSmsSent', 'todaySent',
            'totalStaffNotices', 'unreadCount', 'portalAccounts'
        ));
    }

    // ── Bulk Email ─────────────────────────────────────────────

    public function bulkEmail()
    {
        $classes = DB::table('classes')->orderBy('sort_order')->get();

        $parentCount = DB::table('students')
            ->where('status', 'active')
            ->where(fn($q) => $q->whereNotNull('father_email')->orWhereNotNull('mother_email'))
            ->count();

        $staffCount = DB::table('users')
            ->whereNotNull('employee_id')
            ->where('is_active', true)
            ->whereNotNull('email')
            ->count();

        $classStudentCounts = DB::table('student_enrollments')
            ->where('status', 'active')
            ->select('class_id', DB::raw('count(distinct student_id) as cnt'))
            ->groupBy('class_id')
            ->pluck('cnt', 'class_id');

        return view('communication.bulk-email', compact('classes', 'parentCount', 'staffCount', 'classStudentCounts'));
    }

    public function sendBulkEmail(Request $request)
    {
        $request->validate([
            'subject'        => 'required|string|max:200',
            'body'           => 'required|string',
            'audience_type'  => 'required|in:all_parents,all_staff,specific_class,specific_role',
            'class_id'       => 'required_if:audience_type,specific_class|nullable|integer',
        ]);

        $audienceMeta = ['type' => $request->audience_type];
        if ($request->audience_type === 'specific_class') {
            $audienceMeta['class_id'] = $request->class_id;
        }

        // Resolve recipient email list
        $recipients = $this->resolveRecipients($request->audience_type, $request->class_id);

        // Create log entry
        $log = CommunicationLog::create([
            'type'          => 'email',
            'subject'       => $request->subject,
            'body'          => $request->body,
            'audience_meta' => $audienceMeta,
            'sent_count'    => 0,
            'failed_count'  => 0,
            'status'        => 'sending',
            'sent_by'       => Auth::id(),
            'sent_at'       => now(),
        ]);

        $sent   = 0;
        $failed = 0;

        foreach ($recipients as $email) {
            try {
                Mail::html($request->body, function ($msg) use ($request, $email) {
                    $msg->to($email)
                        ->subject($request->subject)
                        ->from(config('mail.from.address'), config('app.name'));
                });
                $sent++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        $log->update([
            'sent_count'   => $sent,
            'failed_count' => $failed,
            'status'       => $failed === 0 ? 'sent' : ($sent === 0 ? 'failed' : 'sent'),
        ]);

        return redirect()->route('communication.logs')
            ->with('success', "Email sent to {$sent} recipient(s)" . ($failed > 0 ? ", {$failed} failed." : '.'));
    }

    private function resolveRecipients(string $type, ?int $classId): array
    {
        return match ($type) {
            'all_parents' => DB::table('students')
                ->whereIn('status', ['active'])
                ->whereNotNull('father_email')
                ->pluck('father_email')
                ->merge(DB::table('students')->whereNotNull('mother_email')->pluck('mother_email'))
                ->filter()
                ->unique()
                ->values()
                ->all(),

            'all_staff' => DB::table('users')
                ->whereNotNull('employee_id')
                ->where('is_active', true)
                ->whereNotNull('email')
                ->pluck('email')
                ->all(),

            'specific_class' => DB::table('students as s')
                ->join('student_enrollments as se', 'se.student_id', '=', 's.id')
                ->where('se.class_id', $classId)
                ->where('se.status', 'active')
                ->select(DB::raw("CASE WHEN s.father_email IS NOT NULL THEN s.father_email ELSE s.mother_email END as email"))
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all(),

            default => [],
        };
    }

    // ── Communication Logs ─────────────────────────────────────

    public function logs(Request $request)
    {
        $query = CommunicationLog::with('sender')->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(20)->withQueryString();
        return view('communication.logs', compact('logs'));
    }

    // ── Staff Notice Board ─────────────────────────────────────

    public function staffNotices(Request $request)
    {
        $notices = StaffNotice::with('author')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->orderByRaw("priority = 'urgent' DESC")
            ->orderByDesc('created_at')
            ->paginate(15);

        $readIds = StaffNoticeRead::where('user_id', Auth::id())->pluck('staff_notice_id')->toArray();

        return view('communication.staff-notices', compact('notices', 'readIds'));
    }

    public function storeStaffNotice(Request $request)
    {
        $request->validate([
            'title'               => 'required|string|max:200',
            'body'                => 'required|string',
            'priority'            => 'required|in:normal,urgent',
            'target_department'   => 'nullable|string|max:100',
            'expires_at'          => 'nullable|date|after:today',
        ]);

        StaffNotice::create([
            'title'             => $request->title,
            'body'              => $request->body,
            'priority'          => $request->priority,
            'target_department' => $request->target_department,
            'expires_at'        => $request->expires_at,
            'created_by'        => Auth::id(),
        ]);

        return redirect()->route('communication.staff-notices')
            ->with('success', 'Staff notice posted successfully.');
    }

    public function deleteStaffNotice(StaffNotice $notice)
    {
        $notice->delete();
        return back()->with('success', 'Notice deleted.');
    }

    public function markNoticeRead(StaffNotice $notice)
    {
        StaffNoticeRead::firstOrCreate(
            ['staff_notice_id' => $notice->id, 'user_id' => Auth::id()],
            ['read_at' => now()]
        );
        return response()->json(['ok' => true]);
    }

    public function noticeReadReceipts(StaffNotice $notice)
    {
        $reads = $notice->reads()->with('user')->latest('read_at')->get();
        return view('communication.notice-receipts', compact('notice', 'reads'));
    }

    // ── Portal Account Management ──────────────────────────────

    public function portalAccounts(Request $request)
    {
        $query = Student::where('status', 'active');

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->q . '%')
                  ->orWhere('last_name', 'like', '%' . $request->q . '%')
                  ->orWhere('admission_no', 'like', '%' . $request->q . '%');
            });
        }

        $students = $query->paginate(20)->withQueryString();

        // Fetch users linked to these students: student_id => user_id
        $studentUsers = DB::table('users')
            ->whereIn('student_id', $students->pluck('id'))
            ->pluck('id', 'student_id');

        $linkedStudentUserIds = $studentUsers->keys()->toArray();

        // Fetch parent links
        $parentLinks = DB::table('parent_students')
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->groupBy('student_id');

        return view('communication.portal-accounts', compact('students', 'linkedStudentUserIds', 'studentUsers', 'parentLinks'));
    }

    public function createPortalAccount(Request $request)
    {
        $request->validate([
            'student_id'   => 'required|exists:students,id',
            'account_type' => 'required|in:student,parent',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:8',
        ]);

        $student = Student::findOrFail($request->student_id);

        if ($request->account_type === 'student') {
            $user = User::create([
                'name'       => $student->first_name . ' ' . $student->last_name,
                'email'      => $request->email,
                'mobile'     => $student->mobile,
                'password'   => bcrypt($request->password),
                'student_id' => $student->id,
                'is_active'  => true,
            ]);
            $user->assignRole('student');
        } else {
            // parent account
            $user = User::create([
                'name'     => $request->parent_name ?? ($student->father_name ?? 'Parent'),
                'email'    => $request->email,
                'mobile'   => $request->mobile ?? $student->father_mobile,
                'password' => bcrypt($request->password),
                'is_active'=> true,
            ]);
            $user->assignRole('parent');

            DB::table('parent_students')->insertOrIgnore([
                'parent_user_id' => $user->id,
                'student_id'     => $student->id,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        return redirect()->route('communication.portal-accounts')
            ->with('success', ucfirst($request->account_type) . ' portal account created for ' . $student->first_name . '.');
    }

    public function resetPortalPassword(Request $request, int $id)
    {
        $request->validate(['password' => 'required|min:8|confirmed']);
        $user = User::findOrFail($id);
        $user->update(['password' => bcrypt($request->password)]);
        return back()->with('success', 'Password reset for ' . $user->name . '.');
    }
}
