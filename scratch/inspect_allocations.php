<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$allocCount = App\Models\TeacherSubjectAllocation::count();
echo "TOTAL TEACHER ALLOCATIONS: " . $allocCount . "\n";
if ($allocCount > 0) {
    $samples = App\Models\TeacherSubjectAllocation::with(['employee', 'subject', 'class', 'section'])->take(5)->get();
    foreach ($samples as $s) {
        echo "- Teacher: " . ($s->employee?->full_name ?? 'N/A') . " | Subject: " . ($s->subject?->name ?? 'N/A') . " | Class: " . ($s->class?->name ?? 'N/A') . " " . ($s->section?->name ?? '') . "\n";
    }
}
