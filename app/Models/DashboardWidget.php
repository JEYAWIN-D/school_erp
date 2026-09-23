<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardWidget extends Model
{
    protected $fillable = [
        'name',
        'module',
        'source',
        'filters',
        'icon_color',
        'sort_order',
        'is_active',
        'is_default',
        'created_by',
    ];

    protected $casts = [
        'filters'    => 'array',
        'is_active'  => 'boolean',
        'is_default' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Only active widgets, ordered by sort_order then id */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /** Available icon gradient colours */
    public static function colorOptions(): array
    {
        return [
            'from-blue-500 to-indigo-600'    => 'Blue',
            'from-emerald-500 to-teal-600'   => 'Teal',
            'from-violet-500 to-purple-600'  => 'Purple',
            'from-sky-500 to-blue-600'       => 'Sky',
            'from-orange-400 to-amber-500'   => 'Amber',
            'from-rose-500 to-red-600'       => 'Red',
            'from-pink-500 to-rose-500'      => 'Pink',
            'from-green-500 to-emerald-600'  => 'Green',
            'from-indigo-500 to-blue-700'    => 'Indigo',
            'from-teal-500 to-cyan-600'      => 'Cyan',
            'from-slate-600 to-slate-800'    => 'Slate',
            'from-lime-500 to-green-600'     => 'Lime',
        ];
    }

    /**
     * Complete catalog of available data sources.
     * Each entry: [label, group, has_student_filters, value_format]
     * value_format: 'count' | 'currency' | 'percent'
     */
    public static function sourceCatalog(): array
    {
        return [
            // ── Students ─────────────────────────────────────────────────
            'students_total' => [
                'label'               => 'Total Students',
                'group'               => 'Students',
                'description'         => 'Total active enrolled students in current academic year',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'students.index',
                'link_label'          => 'View students →',
                'default_icon'        => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            ],
            'students_boys' => [
                'label'               => 'Boys',
                'group'               => 'Students',
                'description'         => 'Total active male students enrolled',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'students.index',
                'link_label'          => 'View boys →',
                'default_icon'        => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            ],
            'students_girls' => [
                'label'               => 'Girls',
                'group'               => 'Students',
                'description'         => 'Total active female students enrolled',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'students.index',
                'link_label'          => 'View girls →',
                'default_icon'        => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            ],
            'student_count' => [
                'label'               => 'Student Count (Filtered)',
                'group'               => 'Students',
                'description'         => 'Count of students matching selected custom filters',
                'has_student_filters' => true,
                'value_format'        => 'count',
                'link_route'          => 'students.index',
                'link_label'          => 'View students →',
                'default_icon'        => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            ],
            'student_birthdays_today' => [
                'label'               => "Today's Student Birthdays",
                'group'               => 'Students',
                'description'         => 'Students whose birthday is today',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'students.index',
                'link_label'          => 'View students →',
                'default_icon'        => 'M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z',
            ],
            'admissions_month' => [
                'label'               => 'New Admissions (This Month)',
                'group'               => 'Students',
                'description'         => 'Students admitted in the current calendar month',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'admissions.index',
                'link_label'          => 'View admissions →',
                'default_icon'        => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            ],

            // ── Staff & HR ────────────────────────────────────────────────
            'staff_teachers' => [
                'label'               => 'Total Teachers',
                'group'               => 'Staff & HR',
                'description'         => 'Total active teaching faculty members',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'hr.employees',
                'link_label'          => 'View teachers →',
                'default_icon'        => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055',
            ],
            'staff_non_teaching' => [
                'label'               => 'Total Non-Teaching Staff',
                'group'               => 'Staff & HR',
                'description'         => 'Total active administrative and support staff',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'hr.employees',
                'link_label'          => 'View staff →',
                'default_icon'        => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            ],
            'staff_total' => [
                'label'               => 'Total Staff',
                'group'               => 'Staff & HR',
                'description'         => 'Total number of active employees',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'hr.employees',
                'link_label'          => 'View staff →',
                'default_icon'        => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
            ],
            'staff_present_today' => [
                'label'               => 'Staff Present Today',
                'group'               => 'Staff & HR',
                'description'         => "Number of staff marked present in today's attendance",
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'hr.index',
                'link_label'          => 'Details →',
                'default_icon'        => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            'staff_absent_today' => [
                'label'               => 'Staff Absent Today',
                'group'               => 'Staff & HR',
                'description'         => 'Employees on leave or absent today',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'hr.index',
                'link_label'          => 'View HR →',
                'default_icon'        => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            ],
            'leave_pending' => [
                'label'               => 'Pending Leave Requests',
                'group'               => 'Staff & HR',
                'description'         => 'Leave requests awaiting approval',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'hr.index',
                'link_label'          => 'Manage →',
                'default_icon'        => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ],

            // ── Attendance ───────────────────────────────────────────────
            'attendance_present_today' => [
                'label'               => 'Present Students Today',
                'group'               => 'Attendance',
                'description'         => "Number of students present today",
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'attendance.index',
                'link_label'          => 'View attendance →',
                'default_icon'        => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            'attendance_absent_today' => [
                'label'               => 'Absent Students Today',
                'group'               => 'Attendance',
                'description'         => "Number of students absent today",
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'attendance.index',
                'link_label'          => 'View absentees →',
                'default_icon'        => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            'attendance_rate_today' => [
                'label'               => "Today's Attendance Rate",
                'group'               => 'Attendance',
                'description'         => "Student attendance percentage for today",
                'has_student_filters' => false,
                'value_format'        => 'percent',
                'link_route'          => 'attendance.index',
                'link_label'          => 'View attendance →',
                'default_icon'        => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            ],

            // ── Finance & Revenue ────────────────────────────────────────
            'revenue_total' => [
                'label'               => 'Total Revenue',
                'group'               => 'Finance',
                'description'         => 'Total fee collections for current academic year',
                'has_student_filters' => false,
                'value_format'        => 'currency',
                'link_route'          => 'reports.fee-collection-register',
                'link_label'          => 'Fee register →',
                'default_icon'        => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            'expenses_total' => [
                'label'               => 'Total Expenses',
                'group'               => 'Finance',
                'description'         => 'Total approved expenses for current academic year',
                'has_student_filters' => false,
                'value_format'        => 'currency',
                'link_route'          => 'expenses.index',
                'link_label'          => 'View expenses →',
                'default_icon'        => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
            ],
            'fee_outstanding' => [
                'label'               => 'Pending Fees',
                'group'               => 'Finance',
                'description'         => 'Total unpaid fee amount (due - paid, current year)',
                'has_student_filters' => false,
                'value_format'        => 'currency',
                'link_route'          => 'reports.fee-collection-register',
                'link_label'          => 'Report →',
                'default_icon'        => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            ],
            'fee_collected_today' => [
                'label'               => 'Fee Collected Today',
                'group'               => 'Finance',
                'description'         => 'Total fee payments received today',
                'has_student_filters' => false,
                'value_format'        => 'currency',
                'link_route'          => 'reports.fee-collection-register',
                'link_label'          => 'View history →',
                'default_icon'        => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
            ],
            'fee_collected_month' => [
                'label'               => 'Fee Collected This Month',
                'group'               => 'Finance',
                'description'         => 'Total fee payments received this month',
                'has_student_filters' => false,
                'value_format'        => 'currency',
                'link_route'          => 'reports.fee-collection-register',
                'link_label'          => 'View history →',
                'default_icon'        => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
            ],

            // ── Admissions ───────────────────────────────────────────────
            'admissions_total' => [
                'label'               => 'Total Admissions / Enquiries',
                'group'               => 'Admissions',
                'description'         => 'Total admission enquiries received',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'admissions.index',
                'link_label'          => 'View enquiries →',
                'default_icon'        => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            'admissions_pending' => [
                'label'               => 'Pending Follow-ups',
                'group'               => 'Admissions',
                'description'         => 'Enquiries awaiting follow-up',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'admissions.index',
                'link_label'          => 'View →',
                'default_icon'        => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            ],

            // ── Campus, Events & Notices ─────────────────────────────────
            'events_upcoming' => [
                'label'               => 'Upcoming Events',
                'group'               => 'Campus & Events',
                'description'         => 'Published events scheduled for today or later',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'events.index',
                'link_label'          => 'View events →',
                'default_icon'        => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            ],
            'notices_active' => [
                'label'               => 'Active Notices',
                'group'               => 'Campus & Events',
                'description'         => 'Latest active published school notices',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'academics.notices',
                'link_label'          => 'View notices →',
                'default_icon'        => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
            ],
            'circulars_active' => [
                'label'               => 'Active Circulars',
                'group'               => 'Campus & Events',
                'description'         => 'Latest official circulars published',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'academics.notices',
                'link_label'          => 'View circulars →',
                'default_icon'        => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            ],

            // ── Facilities ───────────────────────────────────────────────
            'library_issued' => [
                'label'               => 'Books Currently Issued',
                'group'               => 'Facilities',
                'description'         => 'Books currently issued and not returned',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'library.index',
                'link_label'          => 'View library →',
                'default_icon'        => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            ],
            'library_overdue' => [
                'label'               => 'Overdue Books',
                'group'               => 'Facilities',
                'description'         => 'Books past their due date not yet returned',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'library.index',
                'link_label'          => 'View library →',
                'default_icon'        => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            ],
            'hostel_residents' => [
                'label'               => 'Hostel Residents',
                'group'               => 'Facilities',
                'description'         => 'Active hostel residents',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'hostel.index',
                'link_label'          => 'View hostel →',
                'default_icon'        => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75',
            ],
            'transport_students' => [
                'label'               => 'Students on Transport',
                'group'               => 'Facilities',
                'description'         => 'Students with active transport allotment',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'transport.index',
                'link_label'          => 'View transport →',
                'default_icon'        => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z',
            ],

            // ── Academics & Classes ──────────────────────────────────────
            'classes_total' => [
                'label'               => 'Active Classes',
                'group'               => 'Academics',
                'description'         => 'Total active academic classes in school',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'classes.index',
                'link_label'          => 'View classes →',
                'default_icon'        => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            ],
            'sections_total' => [
                'label'               => 'Active Sections',
                'group'               => 'Academics',
                'description'         => 'Total sections across all classes',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'link_route'          => 'classes.index',
                'link_label'          => 'View sections →',
                'default_icon'        => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
            ],

            // ── Detailed Information Widgets ────────────────────────────
            'student_overview' => [
                'label'               => 'Student Overview',
                'group'               => 'Detailed Information',
                'description'         => 'Detailed student demographics: boys, girls, day scholars, hostellers, and class strength distribution',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'type'                => 'brief_info',
                'link_route'          => 'students.index',
                'link_label'          => 'View all students →',
                'default_icon'        => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
            ],
            'student_demographics' => [
                'label'               => 'Student Demographic Breakdown',
                'group'               => 'Detailed Information',
                'description'         => 'Boys vs Girls and Day Scholars vs Hostellers breakdown',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'type'                => 'brief_info',
                'link_route'          => 'students.index',
                'link_label'          => 'View students →',
                'default_icon'        => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            ],
            'class_distribution' => [
                'label'               => 'Class-wise Student Distribution',
                'group'               => 'Detailed Information',
                'description'         => 'Student strength breakdown across all classes',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'type'                => 'brief_info',
                'link_route'          => 'classes.index',
                'link_label'          => 'View classes →',
                'default_icon'        => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            ],
            'section_distribution' => [
                'label'               => 'Section Distribution',
                'group'               => 'Detailed Information',
                'description'         => 'Student strength breakdown across class sections',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'type'                => 'brief_info',
                'link_route'          => 'classes.index',
                'link_label'          => 'View sections →',
                'default_icon'        => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
            ],
            'attendance_overview' => [
                'label'               => 'Attendance Overview',
                'group'               => 'Detailed Information',
                'description'         => 'Today\'s student attendance breakdown (present, absent, half-day, late, %) and staff attendance',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'type'                => 'brief_info',
                'link_route'          => 'attendance.daily',
                'link_label'          => 'Attendance register →',
                'default_icon'        => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            ],
            'fee_overview' => [
                'label'               => 'Fee Collection Analytics',
                'group'               => 'Detailed Information',
                'description'         => 'Fee collection summary: total receipts realized, this month, outstanding receivables, and billing link',
                'has_student_filters' => false,
                'value_format'        => 'currency',
                'type'                => 'brief_info',
                'link_route'          => 'reports.fee-collection-register',
                'link_label'          => 'Fee register →',
                'default_icon'        => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
            ],
            'admissions_overview' => [
                'label'               => 'Admissions Overview',
                'group'               => 'Detailed Information',
                'description'         => 'Admission pipeline status: pending enquiries, leave approvals, TC requests, and recent applicants',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'type'                => 'brief_info',
                'link_route'          => 'admissions.index',
                'link_label'          => 'View admissions →',
                'default_icon'        => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
            ],
            'upcoming_events' => [
                'label'               => 'Upcoming Events',
                'group'               => 'Detailed Information',
                'description'         => 'Campus activities, meetings, and calendar events with dates and venues',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'type'                => 'brief_info',
                'link_route'          => 'events.index',
                'link_label'          => 'View all events →',
                'default_icon'        => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            ],
            'school_notices' => [
                'label'               => 'School Notices',
                'group'               => 'Detailed Information',
                'description'         => 'Latest academic and institutional announcements for students and staff',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'type'                => 'brief_info',
                'link_route'          => 'academics.notices',
                'link_label'          => 'View notices →',
                'default_icon'        => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
            ],
            'circulars_orders' => [
                'label'               => 'Circulars & Orders',
                'group'               => 'Detailed Information',
                'description'         => 'Official administrative orders and regulatory policies issued',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'type'                => 'brief_info',
                'link_route'          => 'academics.notices',
                'link_label'          => 'View circulars →',
                'default_icon'        => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            ],
            'birthday_wishes' => [
                'label'               => 'Birthday Information',
                'group'               => 'Detailed Information',
                'description'         => 'Dynamic celebration panel displaying students and staff celebrating birthdays today',
                'has_student_filters' => false,
                'value_format'        => 'count',
                'type'                => 'brief_info',
                'link_route'          => 'students.index',
                'link_label'          => 'View students →',
                'is_section'          => true,
                'default_icon'        => 'M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18zm-3-9v-2a2 2 0 00-2-2H8a2 2 0 00-2 2v2h12z',
            ],
            'revenue_analytics' => [
                'label'               => 'Total Revenue Analytics',
                'group'               => 'Detailed Information',
                'description'         => '6-month monthly income vs operating expenses comparative trend and net balance',
                'has_student_filters' => false,
                'value_format'        => 'currency',
                'type'                => 'brief_info',
                'link_route'          => 'reports.fee-collection-register',
                'link_label'          => 'Fee register →',
                'is_section'          => true,
                'default_icon'        => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            ],
        ];
    }

    /** Helper: Single Value Cards catalog items */
    public static function singleValueCatalog(): array
    {
        return array_filter(static::sourceCatalog(), fn($c) => ($c['type'] ?? 'single_value') === 'single_value');
    }

    /** Helper: Single Value Cards grouped by module */
    public static function singleValueCatalogGrouped(): array
    {
        $groupNameMap = [
            'Students' => 'Enrollment & Demographics',
        ];

        $groups = [];
        foreach (static::singleValueCatalog() as $key => $config) {
            $group = $groupNameMap[$config['group']] ?? $config['group'];
            if (!isset($groups[$group])) {
                $groups[$group] = [];
            }
            $groups[$group][$key] = $config;
        }
        return $groups;
    }

    /** Helper: Brief Information / Detailed Widgets catalog items */
    public static function briefInfoCatalog(): array
    {
        $briefs = array_filter(static::sourceCatalog(), fn($c) => ($c['type'] ?? 'single_value') === 'brief_info');
        unset($briefs['events_upcoming'], $briefs['notices_active'], $briefs['circulars_active']);
        return $briefs;
    }

    /** Helper: Brief Information / Detailed Widgets grouped by category */
    public static function briefInfoCatalogGrouped(): array
    {
        $categoryMap = [
            'student_overview'     => 'Students',
            'student_demographics' => 'Students',
            'class_distribution'   => 'Students',
            'section_distribution' => 'Students',
            'attendance_overview'  => 'Attendance',
            'fee_overview'         => 'Finance',
            'revenue_analytics'    => 'Finance',
            'admissions_overview'  => 'Admissions',
            'upcoming_events'      => 'Campus & Events',
            'school_notices'       => 'Campus & Events',
            'circulars_orders'     => 'Campus & Events',
            'birthday_wishes'      => 'Campus & Events',
        ];

        $groups = [];
        foreach (static::briefInfoCatalog() as $key => $config) {
            $cat = $categoryMap[$key] ?? 'General';
            if (!isset($groups[$cat])) {
                $groups[$cat] = [];
            }
            $groups[$cat][$key] = $config;
        }
        return $groups;
    }

    /** Returns grouped source catalog for the Add Card dropdown */
    public static function sourceCatalogGrouped(): array
    {
        $groups = [];
        foreach (static::sourceCatalog() as $key => $config) {
            $group = $config['group'];
            if (!isset($groups[$group])) {
                $groups[$group] = [];
            }
            $groups[$group][$key] = $config['label'];
        }
        return $groups;
    }
}
