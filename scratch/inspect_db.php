<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema='public' AND (table_name LIKE '%setting%' OR table_name LIKE '%time%')");
foreach ($tables as $t) {
    echo $t->table_name . PHP_EOL;
}
