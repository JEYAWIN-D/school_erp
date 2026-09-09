<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$terms = App\Models\AcademicTerm::all();
echo "TOTAL ACADEMIC TERMS: " . $terms->count() . "\n";
foreach ($terms as $t) {
    echo "- ID: {$t->id} | Name: {$t->name} | Type: {$t->type} | Order: {$t->order_position} | Dates: {$t->start_date} to {$t->end_date}\n";
}

$syllabusTerms = App\Models\Syllabus::select('term', DB::raw('count(*) as c'))->groupBy('term')->get();
echo "\nSYLLABUS TERMS DISTRIBUTION:\n";
foreach ($syllabusTerms as $st) {
    echo "- Term '{$st->term}': {$st->c} chapters\n";
}
