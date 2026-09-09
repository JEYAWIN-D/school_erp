<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$request = Illuminate\Http\Request::create('/transport/tracking', 'GET');
$app->instance('request', $request);

$user = App\Models\User::first();
Auth::login($user);

// Test view compilation directly
$response = view('transport.tracking', [
    'vehicles' => App\Models\Vehicle::where('is_active', true)->with(['route.stops'])->get(),
    'routes'   => App\Models\TransportRoute::where('is_active', true)->with(['vehicle', 'stops'])->get(),
    'stats'    => [
        'total_vehicles' => 3,
        'online_gprs' => 3,
        'in_transit' => 2,
        'idle_or_parked' => 1,
    ]
])->render();

echo "VIEW RENDER SUCCESS! Size: " . strlen($response) . " bytes\n";

// Also test liveTelemetry JSON endpoint
$controller = new App\Http\Controllers\Admin\TransportController();
$jsonResponse = $controller->liveTelemetry();
echo "TELEMETRY JSON STATUS: " . $jsonResponse->getStatusCode() . "\n";
echo "TELEMETRY DATA SAMPLE: " . substr($jsonResponse->getContent(), 0, 300) . "...\n";
