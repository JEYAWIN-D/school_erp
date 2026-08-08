@extends('layouts.app')
@section('title','Library Members')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Library Members</h1>
      <p class="page-subtitle">Manage student and staff library memberships</p>
    </div>
    <a href="{{ route('library.settings') }}" class="btn btn-secondary btn-sm">Borrowing Limits</a>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  {{-- Tabs --}}
  <div class="flex gap-1 border-b border-slate-200">
    <a href="{{ request()->fullUrlWithQuery(['tab'=>'students']) }}"
       class="px-4 py-2 text-sm font-medium border-b-2 {{ $tab==='students' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
      Students
    </a>
    <a href="{{ request()->fullUrlWithQuery(['tab'=>'staff']) }}"
       class="px-4 py-2 text-sm font-medium border-b-2 {{ $tab==='staff' ? 'border-indigo-600 text-indigo-700' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
      Staff (HR Sync)
    </a>
  </div>

  <form method="GET" class="card-flat py-3">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="flex gap-3 flex-wrap">
      <input type="text" name="search" value="{{ request('search') }}" class="input flex-1"
             placeholder="{{ $tab==='staff' ? 'Search by name or employee ID...' : 'Search by name or admission no...' }}">
      @if($tab === 'students')
      <select name="status" class="select w-36">
        <option value="">All Members</option>
        <option value="active" @selected(request('status')==='active')>Active</option>
        <option value="suspended" @selected(request('status')==='suspended')>Suspended</option>
      </select>
      @endif
      <button type="submit" class="btn btn-secondary btn-sm">Search</button>
    </div>
  </form>

  @if($tab === 'students' && $members)
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Student','Admission No','Class','Status','Books Issued','Overdue','Fine Due','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($members as $s)
        @php
          $activeIssues = $s->bookIssues; // eager-loaded (status='issued')
          $issued  = $activeIssues->count();
          $overdue = $activeIssues->filter(fn($i) => $i->due_date < today())->count();
          $fineDue = $activeIssues->filter(fn($i) => $i->due_date < today())->sum('fine_amount');
        @endphp
        <tr class="hover:bg-slate-50 {{ $s->library_suspended ? 'opacity-70 bg-red-50/30' : '' }}" x-data="{ suspendOpen: false }">
          <td class="px-4 py-3 font-medium text-slate-800">{{ $s->full_name }}</td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700">{{ $s->admission_number }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $s->currentEnrollment?->class?->name ?? '—' }}</td>
          <td class="px-4 py-3">
            @if($s->library_suspended)
              <span class="badge-red text-xs">Suspended</span>
              @if($s->library_suspension_reason)
                <p class="text-xs text-slate-400 mt-0.5 max-w-xs truncate" title="{{ $s->library_suspension_reason }}">{{ $s->library_suspension_reason }}</p>
              @endif
            @else
              <span class="badge-green text-xs">Active</span>
            @endif
          </td>
          <td class="px-4 py-3 text-center font-semibold text-slate-700">{{ $issued ?: '—' }}</td>
          <td class="px-4 py-3 text-center {{ $overdue > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">{{ $overdue ?: '—' }}</td>
          <td class="px-4 py-3 text-center {{ $fineDue > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">{{ $fineDue > 0 ? '₹'.number_format($fineDue,0) : '—' }}</td>
          <td class="px-4 py-3 space-y-1">
            <a href="{{ route('library.members.card', $s->id) }}" target="_blank" class="text-indigo-600 hover:underline text-xs block">Print Card</a>
            <a href="{{ route('library.members.history', $s->id) }}" class="text-slate-500 hover:underline text-xs block">History</a>
            @if($s->library_suspended)
              <form method="POST" action="{{ route('library.members.unsuspend', $s->id) }}">
                @csrf
                <button type="submit" class="text-green-600 hover:underline text-xs">Restore Access</button>
              </form>
            @else
              <button @click="suspendOpen=!suspendOpen" class="text-amber-600 hover:underline text-xs">Suspend</button>
              <div x-show="suspendOpen" x-transition class="mt-1" style="display:none">
                <form method="POST" action="{{ route('library.members.suspend', $s->id) }}" class="flex gap-1">
                  @csrf
                  <input type="text" name="reason" class="input text-xs py-0.5 px-2 flex-1" placeholder="Reason (optional)">
                  <button type="submit" class="btn btn-xs bg-amber-100 text-amber-700 border border-amber-300">OK</button>
                </form>
              </div>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No members found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($members->hasPages())<div class="px-4 pb-3">{{ $members->links() }}</div>@endif
  </div>

  @elseif($tab === 'staff' && $staff)
  <div class="card overflow-hidden">
    <div class="px-4 py-2 bg-blue-50 border-b border-blue-100 text-xs text-blue-700">
      All active employees from HR are automatically available as library members. No manual registration needed.
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Employee','ID','Department','Designation','Books Issued','Overdue','Fine Due'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($staff as $emp)
        @php
          $issued  = \App\Models\BookIssue::where('employee_id', $emp->id)->where('status','issued')->count();
          $overdue = \App\Models\BookIssue::where('employee_id', $emp->id)->where('status','issued')->where('due_date','<',today())->count();
          $fineDue = \App\Models\BookIssue::where('employee_id', $emp->id)->where('status','issued')->where('due_date','<',today())->sum('fine_amount');
        @endphp
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800">{{ $emp->first_name }} {{ $emp->last_name }}</td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700">{{ $emp->employee_number }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $emp->department ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $emp->designation ?? '—' }}</td>
          <td class="px-4 py-3 text-center font-semibold text-slate-700">{{ $issued ?: '—' }}</td>
          <td class="px-4 py-3 text-center {{ $overdue > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">{{ $overdue ?: '—' }}</td>
          <td class="px-4 py-3 text-center {{ $fineDue > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">{{ $fineDue > 0 ? '₹'.number_format($fineDue,0) : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No active staff found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($staff->hasPages())<div class="px-4 pb-3">{{ $staff->links() }}</div>@endif
  </div>
  @endif

</div>
@endsection
