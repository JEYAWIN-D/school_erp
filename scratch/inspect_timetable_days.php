<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$days = DB::table('timetables')->select('day_of_week', DB::raw('count(*) as c'))->groupBy('day_of_week')->orderBy('day_of_week')->get();
foreach ($days as $d) {
    echo "day_of_week: '{$d->day_of_week}', count: {$d->c}\n";
}
