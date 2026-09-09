<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$entries = App\Models\Timetable::take(10)->get();
echo "TOTAL TIMETABLE ENTRIES: " . App\Models\Timetable::count() . "\n";
foreach ($entries as $e) {
    echo "- Day: {$e->day_of_week}, P{$e->period_number}, Type: {$e->period_type}, Sub: {$e->subject_id} (" . ($e->subject?->name ?? 'N/A') . "), Teacher ID: {$e->teacher_id}, Employee ID attr: {$e->employee_id}, Teacher: " . ($e->teacher?->full_name ?? 'NULL') . "\n";
}
