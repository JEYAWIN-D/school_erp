<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$initialReq = Illuminate\Http\Request::create('/academics', 'GET');
$app->instance('request', $initialReq);

$user = App\Models\User::first();
Auth::guard('web')->setUser($user);

$urls = [
    '/academics',
    '/academics/timetable?class_id=1',
    '/academics/subjects',
    '/academics/syllabus?class_id=1',
];

foreach ($urls as $url) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    $app->instance('request', $request);
    $response = $kernel->handle($request);
    echo "URL: {$url} => STATUS: " . $response->getStatusCode() . " (bytes: " . strlen($response->getContent()) . ")\n";
}
