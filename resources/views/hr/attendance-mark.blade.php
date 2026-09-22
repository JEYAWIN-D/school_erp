@extends('layouts.app')
@section('title', 'Mark Staff Attendance')

@section('content')
<div class="space-y-6" x-data="{ searchQuery: '' }">

  {{-- Page Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
    <div class="flex items-center gap-3.5">
      <a href="{{ route('hr.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition" title="Back to HR & Payroll">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-md shadow-emerald-100 flex-shrink-0" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #FFFFFF;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div>
        <h1 class="page-title text-xl font-black text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">Mark Attendance</h1>
        <p class="text-xs font-semibold text-slate-500 mt-0.5">Record daily school staff attendance &bull; {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</p>
      </div>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('hr.attendance.view', ['date' => $date]) }}" class="btn btn-secondary btn-sm text-xs font-bold flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
        View Attendance
      </a>
    </div>
  </div>

  {{-- Filters Bar: Date & Staff Category Only --}}
  <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs">
    <form method="GET" action="{{ route('hr.attendance.mark') }}" id="filterForm" class="flex flex-wrap items-end justify-between gap-4">
      <div class="flex flex-wrap items-center gap-4">
        {{-- Date Picker --}}
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Date</label>
          <input type="date" name="date" value="{{ $date }}" onchange="document.getElementById('filterForm').submit()"
                 class="input input-sm border-slate-200 rounded-xl font-bold text-xs bg-slate-50 text-slate-800 focus:bg-white">
        </div>

        {{-- Staff Category --}}
        <div>
          <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Staff Category</label>
          <select name="category" onchange="document.getElementById('filterForm').submit()"
                  class="select select-sm border-slate-200 rounded-xl font-bold text-xs bg-slate-50 text-slate-800 focus:bg-white min-w-[180px]">
            @foreach($categories as $cat)
              <option value="{{ $cat['key'] }}" @selected($category === $cat['key'])>
                {{ $cat['label'] }} ({{ $cat['count'] }})
              </option>
            @endforeach
          </select>
        </div>
      </div>

      {{-- Search Filter in table --}}
      <div class="w-full sm:w-64">
        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Quick Search</label>
        <div class="relative">
          <input type="text" x-model="searchQuery" placeholder="Filter staff name or ID..."
                 class="input input-sm w-full bg-slate-50 border-slate-200 text-xs rounded-xl focus:bg-white pr-8">
          <button type="button"
                  x-show="searchQuery && searchQuery.length > 0"
                  @click="searchQuery = ''"
                  class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 font-bold text-base cursor-pointer leading-none"
                  title="Clear search"
                  style="display: none;">
            &times;
          </button>
        </div>
      </div>
    </form>
  </div>

  {{-- Attendance Marking Form --}}
  <form method="POST" action="{{ route('hr.attendance.mark.save') }}" id="attendanceMarkForm">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="category" value="{{ $category }}">

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">

      {{-- Quick Action Header --}}
      <div class="p-3.5 bg-slate-50/80 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2">
          <button type="button" onclick="bulkSetStatus('present')"
                  class="btn btn-secondary btn-xs font-bold text-emerald-700 bg-emerald-50 border-emerald-200 hover:bg-emerald-100 flex items-center gap-1.5 shadow-2xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            Mark All Present
          </button>
        </div>

        <div class="text-xs text-slate-500 font-medium">
          Showing <strong class="text-slate-900">{{ $employees->count() }}</strong> staff members
        </div>
      </div>

      {{-- Table --}}
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-black uppercase tracking-wider text-slate-400">
              <th class="py-3 px-4 w-[35%]">Staff Name</th>
              <th class="py-3 px-4 w-[20%]">Category</th>
              <th class="py-3 px-4 w-[45%] text-left">Attendance</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-xs">
            @forelse($employees as $emp)
              @php
                $existing = $attendances->get($emp->id);
                $approvedLeave = $approvedLeaves->get($emp->id);

                $validStatuses = ['present', 'absent', 'half_day', 'on_duty', 'paid_off', 'permission'];
                if ($existing && in_array($existing->status, $validStatuses)) {
                    $status = $existing->status;
                } else {
                    $status = 'present';
                }

                $outTime = ($existing && $existing->check_out) ? \Carbon\Carbon::parse($existing->check_out)->format('H:i') : '';
                $inTime  = ($existing && $existing->check_in)  ? \Carbon\Carbon::parse($existing->check_in)->format('H:i') : '';
              @endphp
              <tr class="hover:bg-slate-50/60 transition-colors"
                  x-show="!searchQuery || '{{ strtolower($emp->full_name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($emp->employee_code ?? '') }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($emp->category_label ?? '') }}'.includes(searchQuery.toLowerCase())">
                
                {{-- Staff Name --}}
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                      @if($emp->photo)
                        <img src="{{ asset('storage/' . $emp->photo) }}" class="w-full h-full object-cover rounded-lg" alt="">
                      @else
                        {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name ?? '', 0, 1)) }}
                      @endif
                    </div>
                    <div>
                      <span class="font-bold text-slate-900 block leading-tight">{{ $emp->full_name }}</span>
                      <span class="text-[10px] font-mono text-slate-400 leading-tight">{{ $emp->employee_code ?? 'EMP-' . $emp->id }}</span>
                    </div>
                  </div>
                </td>

                {{-- Category --}}
                <td class="py-3.5 px-4">
                  <span class="{{ $emp->category_badge_class }} text-[10px] px-2.5 py-0.5 font-bold uppercase rounded-md">
                    {{ $emp->category_label }}
                  </span>
                  @if($approvedLeave)
                    <span class="block mt-1 text-[10px] text-amber-700 font-medium">
                      On Leave: {{ $approvedLeave->leaveType?->name ?? 'Leave' }}
                    </span>
                  @endif
                </td>

                {{-- Attendance Status & Permission Time Fields --}}
                <td class="py-3.5 px-4 text-left">
                  <div class="flex flex-wrap items-center gap-2.5">
                    <select name="attendance[{{ $emp->id }}][status]"
                            id="status-select-{{ $emp->id }}"
                            data-emp-id="{{ $emp->id }}"
                            class="attendance-status-select select select-xs text-xs font-bold rounded-lg border-slate-200 py-1 px-3 w-36 transition-colors"
                            onchange="handleStatusChange(this, '{{ $emp->id }}')">
                      <option value="present" @selected($status === 'present')>Present</option>
                      <option value="absent" @selected($status === 'absent')>Absent</option>
                      <option value="half_day" @selected($status === 'half_day')>Half Day</option>
                      <option value="on_duty" @selected($status === 'on_duty')>On Duty</option>
                      <option value="paid_off" @selected($status === 'paid_off')>Paid Off</option>
                      <option value="permission" @selected($status === 'permission')>Permission</option>
                    </select>

                    {{-- Permission Inline Time Fields (Visible ONLY when status is Permission) --}}
                    <div id="permission-fields-{{ $emp->id }}"
                         class="permission-time-box items-center gap-2 {{ $status === 'permission' ? 'flex' : 'hidden' }}">
                      <div class="flex items-center gap-1">
                        <label for="out-time-{{ $emp->id }}" class="text-[10px] font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Out Time:</label>
                        <input type="time"
                               name="attendance[{{ $emp->id }}][out_time]"
                               id="out-time-{{ $emp->id }}"
                               value="{{ $outTime }}"
                               class="permission-out-input input input-xs font-mono font-semibold text-xs border-slate-200 rounded-lg w-28 bg-slate-50 focus:bg-white text-slate-800"
                               {{ $status === 'permission' ? 'required' : '' }}>
                      </div>
                      <div class="flex items-center gap-1">
                        <label for="in-time-{{ $emp->id }}" class="text-[10px] font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">In Time:</label>
                        <input type="time"
                               name="attendance[{{ $emp->id }}][in_time]"
                               id="in-time-{{ $emp->id }}"
                               value="{{ $inTime }}"
                               placeholder="Optional"
                               class="permission-in-input input input-xs font-mono font-semibold text-xs border-slate-200 rounded-lg w-28 bg-slate-50 focus:bg-white text-slate-800">
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="py-12 text-center text-slate-400">
                  <p class="text-sm font-semibold text-slate-500">No staff members found for the selected category.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Form Submit Footer --}}
      <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="text-xs text-slate-500">
          Attendance will be saved for date <strong class="text-slate-800">{{ $date }}</strong>.
        </div>
        <div class="flex items-center gap-3">
          <a href="{{ route('hr.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
          <button type="submit" class="btn btn-primary btn-sm px-6 font-bold shadow-xs flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Save Attendance
          </button>
        </div>
      </div>

    </div>
  </form>

</div>

<script>
function handleStatusChange(sel, empId) {
  updateStatusColor(sel);
  const container = document.getElementById('permission-fields-' + empId);
  const outInput = document.getElementById('out-time-' + empId);
  if (container) {
    if (sel.value === 'permission') {
      container.classList.remove('hidden');
      container.classList.add('flex');
      if (outInput) {
        outInput.setAttribute('required', 'required');
      }
    } else {
      container.classList.remove('flex');
      container.classList.add('hidden');
      if (outInput) {
        outInput.removeAttribute('required');
      }
    }
  }
}

function updateStatusColor(sel) {
  sel.classList.remove(
    'bg-emerald-50', 'text-emerald-800', 'border-emerald-300',
    'bg-rose-50', 'text-rose-800', 'border-rose-300',
    'bg-blue-50', 'text-blue-800', 'border-blue-300',
    'bg-sky-50', 'text-sky-800', 'border-sky-300',
    'bg-purple-50', 'text-purple-800', 'border-purple-300',
    'bg-amber-50', 'text-amber-800', 'border-amber-300',
    'bg-slate-50', 'text-slate-800'
  );
  if (sel.value === 'present') {
    sel.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-300');
  } else if (sel.value === 'absent') {
    sel.classList.add('bg-rose-50', 'text-rose-800', 'border-rose-300');
  } else if (sel.value === 'half_day') {
    sel.classList.add('bg-blue-50', 'text-blue-800', 'border-blue-300');
  } else if (sel.value === 'on_duty') {
    sel.classList.add('bg-sky-50', 'text-sky-800', 'border-sky-300');
  } else if (sel.value === 'paid_off') {
    sel.classList.add('bg-purple-50', 'text-purple-800', 'border-purple-300');
  } else if (sel.value === 'permission') {
    sel.classList.add('bg-amber-50', 'text-amber-800', 'border-amber-300');
  } else {
    sel.classList.add('bg-slate-50', 'text-slate-800');
  }
}

function bulkSetStatus(statusVal) {
  document.querySelectorAll('.attendance-status-select').forEach(sel => {
    // Preserve existing permission records
    if (sel.value === 'permission') {
      return;
    }
    sel.value = statusVal;
    const empId = sel.dataset.empId;
    handleStatusChange(sel, empId);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.attendance-status-select').forEach(sel => {
    updateStatusColor(sel);
  });

  const markForm = document.getElementById('attendanceMarkForm');
  if (markForm) {
    markForm.addEventListener('submit', function(e) {
      let missingOutTime = false;
      let firstMissingInput = null;

      document.querySelectorAll('.attendance-status-select').forEach(sel => {
        if (sel.value === 'permission') {
          const empId = sel.dataset.empId;
          const outInput = document.getElementById('out-time-' + empId);
          if (!outInput || !outInput.value.trim()) {
            missingOutTime = true;
            if (!firstMissingInput && outInput) {
              firstMissingInput = outInput;
            }
          }
        }
      });

      if (missingOutTime) {
        e.preventDefault();
        alert('Out time is required for Permission attendance.');
        if (firstMissingInput) {
          firstMissingInput.focus();
        }
      }
    });
  }
});
</script>
@endsection
