<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$adminUser = App\Models\User::role('super_admin')->first() 
    ?? App\Models\User::role('admin')->first()
    ?? App\Models\User::all()->first(fn($u) => $u->hasAnyRole(['admin', 'super_admin', 'principal', 'head_master']));

echo "Admin User Found: " . ($adminUser ? $adminUser->email . " (ID: {$adminUser->id})" : "None") . "\n";
