<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Employee;
use App\Models\Subject;
use App\Models\TeacherSubjectAllocation;
use App\Models\Timetable;

$currentYear = AcademicYear::current() ?? AcademicYear::latest('id')->first();
$firstClassId = Classes::active()->first()?->id ?? 1;

// Distinct subject, teacher and class pairings from timetable
$pairings = Timetable::whereNotNull('subject_id')
    ->whereNotNull('teacher_id')
    ->select('subject_id', 'teacher_id', 'class_id')
    ->distinct()
    ->get();

echo "Found " . $pairings->count() . " distinct subject-teacher-class pairings.\n";

$rows = [];
$now = now();
$seen = [];
foreach ($pairings as $p) {
    $classId = $p->class_id ?: (Subject::find($p->subject_id)?->class_id ?: $firstClassId);
    $key = "{$p->subject_id}-{$p->teacher_id}-{$classId}";
    if (isset($seen[$key])) continue;
    $seen[$key] = true;

    $rows[] = [
        'subject_id'       => $p->subject_id,
        'employee_id'      => $p->teacher_id,
        'class_id'         => $classId,
        'section_id'       => null,
        'academic_year_id' => $currentYear?->id,
        'created_at'       => $now,
        'updated_at'       => $now,
    ];
}

echo "Preparing " . count($rows) . " unique records to insert...\n";

// Bulk insert in chunks of 100
foreach (array_chunk($rows, 100) as $chunk) {
    TeacherSubjectAllocation::insertOrIgnore($chunk);
}

// Ensure every single subject in the system has at least one assigned handling teacher
$unallocated = Subject::whereDoesntHave('allocations')->where('is_active', true)->get();
$teachers = Employee::where('is_active', true)->where('employee_type', 'teaching')->get();
if ($teachers->isEmpty()) {
    $teachers = Employee::where('is_active', true)->get();
}

if ($unallocated->isNotEmpty() && $teachers->isNotEmpty()) {
    $extraRows = [];
    $idx = 0;
    foreach ($unallocated as $s) {
        $t = $teachers[$idx % $teachers->count()];
        $classId = $s->class_id ?: $firstClassId;
        $extraRows[] = [
            'subject_id'       => $s->id,
            'employee_id'      => $t->id,
            'class_id'         => $classId,
            'section_id'       => null,
            'academic_year_id' => $currentYear?->id,
            'created_at'       => $now,
            'updated_at'       => $now,
        ];
        $idx++;
    }
    TeacherSubjectAllocation::insertOrIgnore($extraRows);
}

$total = TeacherSubjectAllocation::count();
echo "SUCCESS! Total TeacherSubjectAllocations in DB: {$total}\n";
