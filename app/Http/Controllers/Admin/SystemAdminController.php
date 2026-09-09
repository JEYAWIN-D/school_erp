<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SystemAdminController extends Controller
{
    public function auditLog(Request $request)
    {
        $logs = AuditLog::with('user')
            ->when($request->user_id, fn($q, $v) => $q->where('user_id', $v))
            ->when($request->action, fn($q, $v) => $q->where('action', $v))
            ->when($request->from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest('created_at')
            ->paginate(50)->withQueryString();

        $users   = User::orderBy('name')->get(['id', 'name']);
        $actions = AuditLog::distinct()->pluck('action');

        return view('system.audit-log', compact('logs', 'users', 'actions'));
    }

    public function roles()
    {
        $roles = Role::withCount('users', 'permissions')->get();
        $permissions = Permission::orderBy('name')->get();
        $users = User::with('roles')->orderBy('name')->get();
        return view('system.roles', compact('roles', 'permissions', 'users'));
    }

    public function storeRole(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:50|unique:roles,name|regex:/^[a-z_]+$/',
            'copy_from' => 'nullable|string|exists:roles,name',
        ]);
        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        if ($request->filled('copy_from')) {
            $source = Role::findByName($request->copy_from);
            $role->syncPermissions($source->permissions);
        } elseif ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }
        return back()->with('success', 'Role "' . $data['name'] . '" created.');
    }

    public function updateRole(Request $request, int $id)
    {
        $role = Role::findOrFail($id);
        $role->syncPermissions($request->permissions ?? []);
        return back()->with('success', 'Permissions updated for role: ' . $role->name);
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|string|exists:roles,name',
        ]);
        User::findOrFail($request->user_id)->assignRole($request->role);
        return back()->with('success', 'Role assigned.');
    }

    public function removeRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|string',
        ]);
        User::findOrFail($request->user_id)->removeRole($request->role);
        return back()->with('success', 'Role removed.');
    }

    public function securitySettings()
    {
        $whitelist = DB::table('ip_whitelist')->where('is_active', true)->get();
        $recentAttempts = DB::table('login_attempts')
            ->orderBy('attempted_at', 'desc')
            ->limit(50)->get();

        $failedLogins = DB::table('login_attempts')
            ->where('success', false)
            ->where('attempted_at', '>', now()->subHours(24))
            ->count();

        return view('system.security', compact('whitelist', 'recentAttempts', 'failedLogins'));
    }

    public function addIpWhitelist(Request $request)
    {
        $data = $request->validate([
            'ip_address'  => 'required|ip',
            'description' => 'nullable|string|max:100',
        ]);
        DB::table('ip_whitelist')->insert(array_merge($data, [
            'is_active' => true,
            'added_by'  => Auth::id(),
            'created_at'=> now(), 'updated_at' => now(),
        ]));
        return back()->with('success', 'IP added to whitelist.');
    }

    public function removeIpWhitelist(int $id)
    {
        DB::table('ip_whitelist')->where('id', $id)->update(['is_active' => false]);
        return back()->with('success', 'IP removed from whitelist.');
    }

    public function backupInfo()
    {
        $backupDir = storage_path('app/backups');
        $files = [];
        if (is_dir($backupDir)) {
            $files = collect(scandir($backupDir))
                ->filter(fn($f) => str_ends_with($f, '.sql') || str_ends_with($f, '.zip'))
                ->map(fn($f) => [
                    'name' => $f,
                    'size' => number_format(filesize("$backupDir/$f") / 1024 / 1024, 2) . ' MB',
                    'date' => date('d M Y H:i', filemtime("$backupDir/$f")),
                ])->values();
        }
        return view('system.backup', compact('files'));
    }

    public function triggerBackup(Request $request)
    {
        // Triggers artisan backup command if spatie/laravel-backup is installed
        // Otherwise generate a basic SQL dump
        try {
            $filename = storage_path('app/backups/backup_' . now()->format('YmdHis') . '.sql');
            if (!is_dir(storage_path('app/backups'))) mkdir(storage_path('app/backups'), 0755, true);

            $db   = config('database.connections.mysql');
            $cmd  = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg($db['username']),
                escapeshellarg($db['password']),
                escapeshellarg($db['host']),
                escapeshellarg($db['database']),
                escapeshellarg($filename)
            );
            exec($cmd, $output, $code);
            if ($code === 0) {
                AuditLog::record('backup_triggered');
                return back()->with('success', 'Database backup created: ' . basename($filename));
            }
            return back()->with('error', 'Backup failed. Check mysqldump availability.');
        } catch (\Exception $e) {
            return back()->with('error', 'Backup error: ' . $e->getMessage());
        }
    }
}
