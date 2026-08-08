@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('content')
@php
$moduleGroups = [
  'Main'         => ['view dashboard'],
  'Admissions'   => ['view admissions','create admissions','edit admissions','delete admissions','export admissions'],
  'Students'     => ['view students','create students','edit students','delete students','export students'],
  'Academics'    => ['view academics','create academics','edit academics','delete academics'],
  'Attendance'   => ['view attendance','mark attendance','edit attendance','export attendance'],
  'Examinations' => ['view examinations','create examinations','edit examinations','delete examinations','enter marks','publish results','export results'],
  'Fees'         => ['view fees','collect fees','edit fees','delete fees','export fees','manage fee structure','approve fees'],
  'HR & Payroll' => ['view employees','create employees','edit employees','delete employees','process payroll','view payroll','export payroll'],
  'Library'      => ['view library','manage library','issue books','return books'],
  'Transport'    => ['view transport','manage transport'],
  'Hostel'       => ['view hostel','manage hostel'],
  'Events'       => ['view events','create events','edit events','delete events'],
  'Gate'         => ['view gate','manage gate'],
  'Communication'=> ['send sms','send whatsapp','send email','manage circulars'],
  'LMS'          => ['view lms','create lms','edit lms','delete lms'],
  'Inventory'    => ['view inventory','manage inventory'],
  'Alumni'       => ['view alumni','manage alumni'],
  'Reports'      => ['view reports','export reports'],
  'System Admin' => ['manage users','manage roles','manage settings','view audit logs'],
  'Portal'       => ['access parent portal','access student portal'],
];
@endphp

<div class="space-y-6" x-data="{ tab: 'matrix', matrixRole: '{{ $roles->first()?->id }}' }">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Roles & Permissions</h1>
    <div class="flex gap-2">
      <a href="{{ route('system.audit-log') }}" class="btn-sm btn-secondary">Audit Log</a>
      <a href="{{ route('system.security') }}" class="btn-sm btn-secondary">Security</a>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  {{-- Tabs --}}
  <div class="flex gap-1 border-b border-slate-200">
    <button @click="tab='matrix'"  :class="tab==='matrix'  ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-700'"
            class="px-4 py-2 text-sm transition">Permission Matrix</button>
    <button @click="tab='roles'"   :class="tab==='roles'   ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-700'"
            class="px-4 py-2 text-sm transition">Manage Roles</button>
    <button @click="tab='users'"   :class="tab==='users'   ? 'border-b-2 border-indigo-600 text-indigo-700 font-semibold' : 'text-slate-500 hover:text-slate-700'"
            class="px-4 py-2 text-sm transition">User Assignments</button>
  </div>

  {{-- ── Tab 1: Visual Permission Matrix ─────────────────────────── --}}
  <div x-show="tab==='matrix'">
    <div class="card">
      <div class="flex items-center gap-4 mb-4 flex-wrap">
        <label class="label mb-0">Select Role:</label>
        <select x-model="matrixRole" class="select w-56">
          @foreach($roles->sortBy('name') as $role)
            <option value="{{ $role->id }}">{{ ucwords(str_replace('_',' ',$role->name)) }}</option>
          @endforeach
        </select>
        <p class="text-xs text-slate-400">Click a checkbox to toggle. Click <strong>Save</strong> to apply.</p>
      </div>

      @foreach($roles->sortBy('name') as $role)
      <div x-show="matrixRole==='{{ $role->id }}'">
        <form method="POST" action="{{ route('system.roles.update', $role->id) }}">
          @csrf @method('PUT')

          <div class="space-y-3">
            @foreach($moduleGroups as $moduleName => $modulePerms)
            @php
              $availPerms = $permissions->whereIn('name', $modulePerms)->values();
            @endphp
            @if($availPerms->count() > 0)
            <div class="border border-slate-100 rounded-xl overflow-hidden">
              <div class="bg-slate-50 px-4 py-2 flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">{{ $moduleName }}</span>
                <label class="flex items-center gap-1.5 text-xs text-slate-500 cursor-pointer">
                  <input type="checkbox" class="rounded"
                         onchange="toggleGroup(this, '{{ $role->id }}_{{ Str::slug($moduleName) }}')"
                         @if($availPerms->every(fn($p) => $role->hasPermissionTo($p->name))) checked @endif>
                  all
                </label>
              </div>
              <div class="px-4 py-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-y-2 gap-x-4">
                @foreach($availPerms as $perm)
                <label class="flex items-center gap-2 text-xs cursor-pointer group">
                  <input type="checkbox" name="permissions[]" value="{{ $perm->name }}"
                         class="perm-cb-{{ $role->id }}_{{ Str::slug($moduleName) }} rounded"
                         @checked($role->hasPermissionTo($perm->name))>
                  <span class="text-slate-600 group-hover:text-slate-800">{{ $perm->name }}</span>
                </label>
                @endforeach
              </div>
            </div>
            @endif
            @endforeach
          </div>

          <div class="mt-4 flex items-center gap-3">
            <button type="submit" class="btn btn-primary">Save Permissions for {{ ucwords(str_replace('_',' ',$role->name)) }}</button>
            <span class="text-xs text-slate-400">{{ $role->permissions_count }} permissions currently assigned</span>
          </div>
        </form>
      </div>
      @endforeach
    </div>
  </div>

  {{-- ── Tab 2: Manage Roles ──────────────────────────────────────── --}}
  <div x-show="tab==='roles'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Create Role --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Create New Role</h3>
      <form method="POST" action="{{ route('system.roles.store') }}" class="space-y-3">
        @csrf
        <div>
          <label class="label">Role Name (slug) <span class="text-red-500">*</span></label>
          <input type="text" name="name" class="input" required placeholder="e.g. lab_assistant">
        </div>
        <div>
          <label class="label">Copy permissions from</label>
          <select name="copy_from" class="select">
            <option value="">— Start blank —</option>
            @foreach($roles->sortBy('name') as $r)
              <option value="{{ $r->name }}">{{ ucwords(str_replace('_',' ',$r->name)) }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn-primary w-full">Create Role</button>
      </form>

      <div class="mt-6 space-y-2">
        <h4 class="text-sm font-semibold text-slate-600 mb-2">All Roles ({{ $roles->count() }})</h4>
        @foreach($roles->sortBy('name') as $role)
        <div class="flex items-center justify-between py-1 border-b border-slate-50 last:border-0 text-sm">
          <span class="text-slate-700 capitalize">{{ str_replace('_', ' ', $role->name) }}</span>
          <span class="text-xs text-slate-400">{{ $role->users_count }} users · {{ $role->permissions_count }} perms</span>
        </div>
        @endforeach
      </div>
    </div>

    {{-- Role detail cards --}}
    <div class="lg:col-span-2 space-y-3">
      @foreach($roles->sortBy('name') as $role)
      <div class="card py-3 px-4 flex items-center justify-between" x-data="{ open: false }">
        <div>
          <span class="font-medium text-slate-700 capitalize">{{ str_replace('_', ' ', $role->name) }}</span>
          <span class="ml-2 text-xs text-slate-400">{{ $role->users_count }} users · {{ $role->permissions_count }} permissions</span>
        </div>
        <button @click="open=!open; tab='matrix'; matrixRole='{{ $role->id }}'" class="btn-xs btn-secondary">
          Edit in Matrix
        </button>
      </div>
      @endforeach
    </div>
  </div>

  {{-- ── Tab 3: User Role Assignments ────────────────────────────── --}}
  <div x-show="tab==='users'" class="space-y-4">
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Assign Role to User</h3>
      <form method="POST" action="{{ route('system.roles.assign') }}" class="flex flex-wrap gap-3 items-end">
        @csrf
        <div>
          <label class="label">User</label>
          <select name="user_id" class="select" required>
            <option value="">Select user</option>
            @foreach($users->sortBy('name') as $u)
            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Role</label>
          <select name="role" class="select" required>
            <option value="">Select role</option>
            @foreach($roles->sortBy('name') as $r)
            <option value="{{ $r->name }}">{{ ucwords(str_replace('_',' ',$r->name)) }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn-primary btn-sm">Assign</button>
      </form>
    </div>

    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Current User Roles</h3>
      <div class="table-wrap">
        <table class="w-full text-sm">
          <thead><tr>
            <th class="th">User</th>
            <th class="th">Email</th>
            <th class="th">Roles</th>
            <th class="th">Actions</th>
          </tr></thead>
          <tbody>
            @foreach($users->sortBy('name') as $u)
            <tr class="tr">
              <td class="td font-medium text-slate-800">{{ $u->name }}</td>
              <td class="td text-slate-500 text-xs">{{ $u->email }}</td>
              <td class="td">
                <div class="flex flex-wrap gap-1">
                  @foreach($u->roles as $r)
                  <span class="badge-blue text-xs capitalize">{{ str_replace('_', ' ', $r->name) }}</span>
                  @endforeach
                  @if($u->roles->isEmpty()) <span class="text-slate-400 text-xs">No role</span> @endif
                </div>
              </td>
              <td class="td">
                @foreach($u->roles as $r)
                <form method="POST" action="{{ route('system.roles.remove') }}" class="inline">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $u->id }}">
                  <input type="hidden" name="role" value="{{ $r->name }}">
                  <button type="submit" class="btn-xs text-rose-600 hover:bg-rose-50 mr-1"
                          onclick="return confirm('Remove {{ $r->name }} from {{ $u->name }}?')">
                    ✕ {{ str_replace('_', ' ', $r->name) }}
                  </button>
                </form>
                @endforeach
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function toggleGroup(masterCb, groupClass) {
  document.querySelectorAll('.perm-cb-' + groupClass).forEach(cb => {
    cb.checked = masterCb.checked;
  });
}
</script>
@endpush
@endsection
