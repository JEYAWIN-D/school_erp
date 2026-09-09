<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $controller = new App\Http\Controllers\Admin\AttendanceController();
    $request = Illuminate\Http\Request::create('/attendance/staff/register', 'GET');
    $response = $controller->staffRegister($request);
    echo "staffRegister returned view: " . $response->name() . PHP_EOL;
    echo "Rendered length: " . strlen($response->render()) . PHP_EOL;
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . PHP_EOL . " at " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
