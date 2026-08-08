@extends('layouts.app')
@section('title','Organisation Chart')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Reporting Hierarchy / Org Chart</h1>
      <p class="page-subtitle">View the management chain and set reporting managers for employees</p>
    </div>
    <a href="{{ route('hr.employees') }}" class="btn btn-secondary btn-sm">← Employees</a>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif

  {{-- Set manager panel --}}
  <div class="card" x-data="{ open: false }">
    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
      <h2 class="font-semibold text-slate-700">Assign Reporting Managers</h2>
      <svg class="w-5 h-5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </div>
    <div x-show="open" x-transition class="mt-4 overflow-x-auto">
      <table class="table-wrap w-full text-sm">
        <thead>
          <tr>
            <th class="th">Employee</th>
            <th class="th">Designation</th>
            <th class="th">Department</th>
            <th class="th">Current Manager</th>
            <th class="th">Change Manager</th>
          </tr>
        </thead>
        <tbody>
          @foreach($allEmployees as $emp)
          <tr class="tr">
            <td class="td font-medium text-slate-800">{{ $emp->full_name }}</td>
            <td class="td text-slate-500 text-xs">{{ $emp->designation?->name ?? '—' }}</td>
            <td class="td text-slate-500 text-xs">{{ $emp->department?->name ?? '—' }}</td>
            <td class="td text-slate-600">{{ $emp->manager?->full_name ?? '—' }}</td>
            <td class="td">
              <form method="POST" action="{{ route('hr.employees.set-manager', $emp->id) }}" class="flex gap-2 items-center">
                @csrf
                <select name="manager_id" class="select text-xs w-44">
                  <option value="">— No Manager —</option>
                  @foreach($allEmployees->where('id', '!=', $emp->id) as $mgr)
                    <option value="{{ $mgr->id }}" @selected($emp->manager_id == $mgr->id)>
                      {{ $mgr->full_name }} ({{ $mgr->designation?->name ?? '—' }})
                    </option>
                  @endforeach
                </select>
                <button type="submit" class="btn-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded px-2 py-0.5 text-xs whitespace-nowrap">Set</button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Org chart visual --}}
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-5 pb-2 border-b border-slate-100">Organisational Chart</h2>

    @php
      function renderOrgNode($employee, $allEmployees, $depth = 0): string {
          $reports = $allEmployees->where('manager_id', $employee->id);
          $indent  = $depth * 24;
          $html = '<div class="flex items-start gap-0 mt-3" style="margin-left:' . $indent . 'px">';
          if ($depth > 0) {
              $html .= '<div class="flex flex-col items-center mr-2 pt-1"><div class="w-px h-4 bg-slate-300"></div><div class="w-3 h-px bg-slate-300"></div></div>';
          }
          $html .= '<div class="rounded-xl border border-slate-200 bg-white shadow-sm px-3 py-2 min-w-0" style="max-width:220px">';
          $html .= '<p class="font-semibold text-slate-800 text-sm truncate">' . e($employee->full_name) . '</p>';
          $html .= '<p class="text-xs text-indigo-600 truncate">' . e($employee->designation?->name ?? '—') . '</p>';
          $html .= '<p class="text-xs text-slate-400 truncate">' . e($employee->department?->name ?? '—') . '</p>';
          if ($reports->isNotEmpty()) {
              $html .= '<p class="text-xs text-slate-400 mt-1">' . $reports->count() . ' direct report' . ($reports->count() !== 1 ? 's' : '') . '</p>';
          }
          $html .= '</div></div>';
          foreach ($reports as $report) {
              $html .= renderOrgNode($report, $allEmployees, $depth + 1);
          }
          return $html;
      }
    @endphp

    @if($roots->isEmpty())
      <p class="text-slate-400 text-sm text-center py-8">No employees found. Add employees and assign managers to build the org chart.</p>
    @else
    <div class="overflow-x-auto">
      @foreach($roots as $root)
        {!! renderOrgNode($root, $allEmployees) !!}
      @endforeach
    </div>
    @endif
  </div>

  {{-- Department view --}}
  <div class="card">
    <h2 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Department-wise Hierarchy</h2>
    <div class="space-y-4">
      @forelse($departments as $dept)
      <div class="border border-slate-200 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="font-semibold text-slate-800">{{ $dept->name }}</h3>
          <span class="text-xs text-slate-400">{{ $dept->employees->count() }} staff</span>
        </div>
        @if($dept->employees->isEmpty())
          <p class="text-slate-400 text-xs">No active employees in this department.</p>
        @else
        <div class="space-y-2">
          @foreach($dept->employees->sortBy('designation.grade') as $emp)
          <div class="flex items-center gap-3 text-sm">
            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold shrink-0">
              {{ strtoupper(substr($emp->first_name, 0, 1)) }}{{ strtoupper(substr($emp->last_name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-slate-800 truncate">{{ $emp->full_name }}</p>
              <p class="text-xs text-slate-400">{{ $emp->designation?->name ?? '—' }}
                @if($emp->manager)
                  · Reports to: <span class="text-indigo-600">{{ $emp->manager->full_name }}</span>
                @endif
                @if($emp->directReports->isNotEmpty())
                  · <span class="text-green-600">{{ $emp->directReports->count() }} direct report{{ $emp->directReports->count() !== 1 ? 's' : '' }}</span>
                @endif
              </p>
            </div>
            @if($emp->designation?->pay_band_min)
            <div class="text-right shrink-0">
              <p class="text-xs text-slate-400">Pay Band</p>
              <p class="text-xs font-mono text-slate-600">
                ₹{{ number_format($emp->designation->pay_band_min/1000, 0) }}k–{{ number_format($emp->designation->pay_band_max/1000, 0) }}k
              </p>
            </div>
            @endif
          </div>
          @endforeach
        </div>
        @endif
      </div>
      @empty
      <p class="text-slate-400 text-sm text-center py-4">No departments configured.</p>
      @endforelse
    </div>
  </div>

</div>
@endsection
