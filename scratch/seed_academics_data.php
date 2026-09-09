<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AcademicTerm;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Employee;
use App\Models\Subject;
use App\Models\Syllabus;
use App\Models\TeacherSubjectAllocation;
use App\Models\Timetable;
use Illuminate\Support\Facades\DB;

echo "=== 1. SYNC ACADEMIC TERMS TO STRICTLY TWO TERMS ===\n";
$currentYear = AcademicYear::current() ?? AcademicYear::latest('id')->first();
if ($currentYear) {
    // Check existing terms
    $t1 = AcademicTerm::updateOrCreate(
        ['academic_year_id' => $currentYear->id, 'name' => 'Term 1'],
        [
            'type' => 'term',
            'order_position' => 1,
            'start_date' => $currentYear->start_date ?? '2025-06-01',
            'end_date' => '2025-11-15',
        ]
    );
    echo "Saved Term 1: ID {$t1->id} (Order 1)\n";

    $t2 = AcademicTerm::updateOrCreate(
        ['academic_year_id' => $currentYear->id, 'name' => 'Term 2'],
        [
            'type' => 'term',
            'order_position' => 2,
            'start_date' => '2025-11-16',
            'end_date' => $currentYear->end_date ?? '2026-04-30',
        ]
    );
    echo "Saved Term 2: ID {$t2->id} (Order 2)\n";

    // Delete any Term 3 in academic_terms
    $deleted = AcademicTerm::where('academic_year_id', $currentYear->id)->where('name', 'Term 3')->delete();
    if ($deleted) echo "Deleted old Term 3 from academic terms.\n";
}

echo "\n=== 2. MIGRATE SYLLABUS TO STRICTLY TWO TERMS ===\n";
$term3Count = Syllabus::where('term', 'Term 3')->count();
echo "Found {$term3Count} chapters in Term 3.\n";
if ($term3Count > 0) {
    Syllabus::where('term', 'Term 3')->update(['term' => 'Term 2']);
    echo "Successfully merged Term 3 chapters into Term 2!\n";
}
$counts = Syllabus::select('term', DB::raw('count(*) as c'))->groupBy('term')->get();
foreach ($counts as $cnt) {
    echo "- {$cnt->term}: {$cnt->c} chapters\n";
}

echo "\n=== 3. POPULATE TEACHER SUBJECT ALLOCATIONS FROM TIMETABLE ===\n";
$timetableEntries = Timetable::whereNotNull('subject_id')
    ->whereNotNull('teacher_id')
    ->select('subject_id', 'teacher_id', 'class_id', 'section_id')
    ->distinct()
    ->get();

echo "Found " . $timetableEntries->count() . " distinct subject-teacher pairings in timetables.\n";
$inserted = 0;
foreach ($timetableEntries as $entry) {
    TeacherSubjectAllocation::firstOrCreate(
        [
            'subject_id'  => $entry->subject_id,
            'employee_id' => $entry->teacher_id,
            'class_id'    => $entry->class_id,
            'section_id'  => $entry->section_id,
        ],
        [
            'academic_year_id' => $currentYear?->id,
        ]
    );
    $inserted++;
}
echo "Synced {$inserted} teacher subject allocations!\n";

// Ensure subjects with no allocations get assigned a relevant teaching faculty
$unallocatedSubjects = Subject::whereDoesntHave('allocations')->where('is_active', true)->get();
$teachers = Employee::where('is_active', true)->where('employee_type', 'teaching')->get();
if ($teachers->isEmpty()) {
    $teachers = Employee::where('is_active', true)->get();
}

if ($unallocatedSubjects->isNotEmpty() && $teachers->isNotEmpty()) {
    echo "Assigning default teachers to " . $unallocatedSubjects->count() . " unallocated subjects...\n";
    $tIndex = 0;
    foreach ($unallocatedSubjects as $subj) {
        $assignedTeacher = $teachers[$tIndex % $teachers->count()];
        TeacherSubjectAllocation::firstOrCreate([
            'subject_id'  => $subj->id,
            'employee_id' => $assignedTeacher->id,
            'class_id'    => $subj->class_id,
        ], [
            'academic_year_id' => $currentYear?->id,
        ]);
        $tIndex++;
    }
}

$totalAlloc = TeacherSubjectAllocation::count();
echo "Total active allocations now: {$totalAlloc}\n";
echo "=== SEEDING COMPLETED SUCCESSFULLY ===\n";
