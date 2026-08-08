@extends('layouts.app')
@section('title', 'Annual Appraisal')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Annual Staff Appraisal</h1>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form --}}
    <div class="lg:col-span-1">
      <form method="POST" action="{{ route('hr.appraisal.save') }}" class="card space-y-4">
        @csrf
        <h2 class="font-semibold text-slate-800">New Appraisal Entry</h2>
        <div>
          <label class="label">Employee *</label>
          <select name="employee_id" required class="select w-full">
            <option value="">Select Employee</option>
            @foreach($employees as $e)
              <option value="{{ $e->id }}">{{ $e->full_name }} ({{ $e->employee_code }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Appraisal Year *</label>
          <input type="number" name="appraisal_year" value="{{ date('Y') }}" min="2020" max="2099" required class="input w-full">
        </div>
        <div>
          <label class="label mb-2 block">KPI Ratings (1–5)</label>
          <div class="space-y-2">
            @foreach($kpiList as $kpi)
            <div class="flex items-center justify-between">
              <label class="text-sm text-slate-700 flex-1">{{ $kpi }}</label>
              <select name="ratings[{{ $kpi }}]" class="select w-20 text-sm">
                @for($i=1; $i<=5; $i++)
                  <option value="{{ $i }}">{{ $i }}</option>
                @endfor
              </select>
            </div>
            @endforeach
          </div>
        </div>
        <div>
          <label class="label">HOD Remarks</label>
          <textarea name="hod_remarks" rows="2" class="input w-full" placeholder="Optional..."></textarea>
        </div>
        <div>
          <label class="label">Principal Remarks</label>
          <textarea name="principal_remarks" rows="2" class="input w-full" placeholder="Optional..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-full">Save Appraisal</button>
      </form>

      <form method="GET" class="card mt-4">
        <label class="label">View Appraisals for Year</label>
        <div class="flex gap-2">
          <input type="number" name="year" value="{{ request('year', date('Y')) }}" class="input flex-1" placeholder="{{ date('Y') }}">
          <button type="submit" class="btn btn-secondary btn-sm">Load</button>
        </div>
      </form>
    </div>

    {{-- Appraisal list --}}
    <div class="lg:col-span-2">
      @if($appraisals->count())
      <div class="card overflow-hidden">
        <h2 class="font-semibold text-slate-800 px-4 pt-4 pb-2">Appraisals — {{ request('year', date('Y')) }}</h2>
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-100"><tr>
            <th class="th">Employee</th>
            <th class="th text-center">Score</th>
            <th class="th">Rating</th>
            <th class="th">Appraised By</th>
            <th class="th">Date</th>
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($appraisals as $a)
            @php
              $badgeClass = match($a->rating_label) {
                'Outstanding' => 'badge-green',
                'Very Good'   => 'badge-blue',
                'Good'        => 'badge-purple',
                'Average'     => 'badge-amber',
                default       => 'badge-red',
              };
            @endphp
            <tr class="hover:bg-slate-50" x-data="{open:false}">
              <td class="td">
                <div class="font-medium text-slate-800">{{ $a->employee?->full_name }}</div>
                <div class="text-xs text-slate-400">{{ $a->employee?->employee_code }}</div>
              </td>
              <td class="td text-center font-bold text-slate-700">{{ $a->overall_score }}/5</td>
              <td class="td"><span class="{{ $badgeClass }}">{{ $a->rating_label }}</span></td>
              <td class="td text-xs text-slate-500">{{ $a->appraisedBy?->name }}</td>
              <td class="td text-xs text-slate-400">{{ $a->appraised_at?->format('d M Y') }}</td>
            </tr>
            @if($a->hod_remarks || $a->principal_remarks)
            <tr class="bg-slate-50 text-xs text-slate-500">
              <td colspan="5" class="px-4 py-2">
                @if($a->hod_remarks)<span class="font-medium">HOD: </span>{{ $a->hod_remarks }}  @endif
                @if($a->principal_remarks)<span class="font-medium ml-4">Principal: </span>{{ $a->principal_remarks }}@endif
              </td>
            </tr>
            @endif
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="card text-center py-10 text-slate-400">
        @if(request('year'))
          No appraisals found for {{ request('year') }}.
        @else
          Select a year to view appraisals.
        @endif
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
