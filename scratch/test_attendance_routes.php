<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$admin = User::where('email', 'admin@schoolerp.in')->first() ?? User::first();
Auth::login($admin);

$routes = [
    '/attendance',
    '/attendance/staff',
    '/attendance/staff/register',
    '/attendance/staff/register?month=' . now()->format('Y-m'),
];

$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

foreach ($routes as $uri) {
    try {
        $req = Request::create($uri, 'GET');
        $req->setUserResolver(fn() => $admin);
        $res = $httpKernel->handle($req);
        echo "Route {$uri} => Status: " . $res->getStatusCode() . "\n";
        if ($res->getStatusCode() !== 200) {
            echo "Response snippet: " . substr($res->getContent(), 0, 300) . "\n";
        }
    } catch (\Throwable $e) {
        echo "Error on {$uri}: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}
