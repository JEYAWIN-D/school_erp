@extends('layouts.app')
@section('title','Hostel Disciplinary Records')
@section('content')
<div class="space-y-6" x-data="{ showAdd: false }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Hostel Disciplinary Records</h1>
      <p class="page-subtitle">Log incidents, issue warnings, and track repeat offences</p>
    </div>
    <button @click="showAdd=!showAdd" class="btn btn-primary btn-sm">+ Log Incident</button>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  {{-- Add Form --}}
  <div x-show="showAdd" x-transition class="card space-y-4" style="display:none">
    <h3 class="font-semibold text-slate-700 pb-2 border-b">New Disciplinary Record</h3>
    <form method="POST" action="{{ route('hostel.disciplinary.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      <div>
        <label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select" required>
          <option value="">Select student</option>
          @foreach($students as $s)<option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->admission_number }})</option>@endforeach
        </select>
      </div>
      <div>
        <label class="label">Incident Date <span class="text-red-500">*</span></label>
        <input type="date" name="incident_date" class="input" value="{{ date('Y-m-d') }}" required>
      </div>
      <div class="md:col-span-2">
        <label class="label">Incident Description <span class="text-red-500">*</span></label>
        <textarea name="description" class="input h-20" required placeholder="Describe the incident..."></textarea>
      </div>
      <div>
        <label class="label">Action Taken</label>
        <input type="text" name="action_taken" class="input" placeholder="Counselled, suspended from activity, etc.">
      </div>
      <div>
        <label class="label">Severity</label>
        <select name="severity" class="select">
          <option value="minor">Minor</option>
          <option value="moderate">Moderate</option>
          <option value="severe">Severe</option>
        </select>
      </div>
      <div>
        <label class="label">Reported By</label>
        <select name="reported_by" class="select">
          <option value="">Select staff</option>
          @foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>@endforeach
        </select>
      </div>
      <div class="flex items-end gap-3">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="is_warning" value="1" class="w-4 h-4 rounded border-slate-300 text-amber-600">
          <span class="text-sm font-medium text-slate-700">Issue Formal Warning</span>
        </label>
      </div>
      <div class="md:col-span-2 flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Save Record</button>
        <button type="button" @click="showAdd=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>

  {{-- Filters --}}
  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 flex-wrap items-end">
      <select name="student_id" class="select w-48">
        <option value="">All Students</option>
        @foreach($students as $s)<option value="{{ $s->id }}" @selected(request('student_id')==$s->id)>{{ $s->full_name }}</option>@endforeach
      </select>
      <select name="severity" class="select w-32">
        <option value="">All Severity</option>
        <option value="minor" @selected(request('severity')==='minor')>Minor</option>
        <option value="moderate" @selected(request('severity')==='moderate')>Moderate</option>
        <option value="severe" @selected(request('severity')==='severe')>Severe</option>
      </select>
      <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="warning_only" value="1" @checked(request('warning_only')) class="rounded">
        Warnings only
      </label>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      @if(request()->hasAny(['student_id','severity','warning_only']))
        <a href="{{ route('hostel.disciplinary') }}" class="btn btn-secondary btn-sm text-slate-400">Clear</a>
      @endif
    </div>
  </form>

  {{-- Records Table --}}
  <div class="card overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Student','Date','Description','Severity','Action Taken','Warning','Reported By','Delete'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($records as $rec)
        <tr class="hover:bg-slate-50 {{ $rec->severity==='severe' ? 'bg-red-50/40' : '' }}">
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800">{{ $rec->student_name }}</p>
            <p class="text-xs text-slate-400">{{ $rec->admission_number }}
              @if(($repeatCounts[$rec->student_id] ?? 0) > 1)
                <span class="badge-red ml-1">{{ $repeatCounts[$rec->student_id] }} incidents</span>
              @endif
            </p>
          </td>
          <td class="px-4 py-3 text-slate-600">{{ \Carbon\Carbon::parse($rec->incident_date)->format('d M Y') }}</td>
          <td class="px-4 py-3 text-slate-500 max-w-xs"><p class="line-clamp-2">{{ $rec->description }}</p></td>
          <td class="px-4 py-3">
            <span class="badge-{{ $rec->severity==='severe'?'red':($rec->severity==='moderate'?'amber':'slate') }} capitalize text-xs">
              {{ $rec->severity ?? 'minor' }}
            </span>
          </td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $rec->action_taken ?? '—' }}</td>
          <td class="px-4 py-3 text-center">
            @if($rec->is_warning)
              <span class="badge-amber text-xs">Warning #{{ $rec->warning_number }}</span>
              @if($rec->warning_date)<p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($rec->warning_date)->format('d M Y') }}</p>@endif
            @else
              <span class="text-slate-300 text-xs">—</span>
            @endif
          </td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $rec->reported_by ? 'Staff #'.$rec->reported_by : '—' }}</td>
          <td class="px-4 py-3">
            <form method="POST" action="{{ route('hostel.disciplinary.delete', $rec->id) }}">
              @csrf @method('DELETE')
              <button type="submit" class="text-red-400 hover:text-red-600 text-xs" onclick="return confirm('Delete this record?')">Delete</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No disciplinary records.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($records->hasPages())<div class="px-4 pb-3">{{ $records->links() }}</div>@endif
  </div>

  {{-- Repeat Offence Summary --}}
  @php $repeatStudents = $repeatCounts->filter(fn($count) => $count >= 3); @endphp
  @if($repeatStudents->isNotEmpty())
  <div class="card border-l-4 border-red-400 bg-red-50/40">
    <h3 class="font-semibold text-red-700 mb-2">Repeat Offenders (3+ incidents)</h3>
    <div class="flex flex-wrap gap-2">
      @foreach($repeatStudents as $sid => $count)
        @php $stu = $students->firstWhere('id', $sid); @endphp
        @if($stu)
          <span class="badge-red text-xs">{{ $stu->full_name }} — {{ $count }} incidents</span>
        @endif
      @endforeach
    </div>
  </div>
  @endif
</div>
@endsection
