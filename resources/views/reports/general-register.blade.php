@extends('layouts.app')
@section('title', 'General Register')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">General Register</h1>
    <div class="flex gap-2">
      <a href="{{ route('dashboard') }}" class="btn-sm btn-secondary">← Dashboard</a>
      <button onclick="window.print()" class="btn-sm btn-primary">🖨 Print</button>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select">
        <option value="">All Classes</option>
        @foreach($classes as $cls)
          <option value="{{ $cls->id }}" @selected(request('class_id')==$cls->id)>{{ $cls->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="label">Gender</label>
      <select name="gender" class="select">
        <option value="">All</option>
        <option value="male" @selected(request('gender')=='male')>Male</option>
        <option value="female" @selected(request('gender')=='female')>Female</option>
        <option value="other" @selected(request('gender')=='other')>Other</option>
      </select>
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('reports.general-register') }}" class="btn-sm btn-secondary">Reset</a>
    <span class="text-sm text-slate-500 ml-auto self-center">Total: <strong>{{ $students->total() }}</strong> students</span>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Adm. No.</th>
          <th class="th">Name</th>
          <th class="th">Class</th>
          <th class="th">DOB</th>
          <th class="th">Gender</th>
          <th class="th">Blood</th>
          <th class="th">Father</th>
          <th class="th">Phone</th>
          <th class="th">Religion</th>
          <th class="th">Admission Date</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $i => $s)
        <tr class="tr">
          <td class="td text-slate-400">{{ $students->firstItem() + $i }}</td>
          <td class="td font-mono text-xs">{{ $s->admission_number }}</td>
          <td class="td font-medium">{{ $s->first_name }} {{ $s->last_name }}</td>
          <td class="td">{{ $s->class_name ?? '—' }}</td>
          <td class="td">{{ $s->dob ? \Carbon\Carbon::parse($s->dob)->format('d/m/Y') : '—' }}</td>
          <td class="td capitalize">{{ $s->gender }}</td>
          <td class="td">{{ $s->blood_group ?? '—' }}</td>
          <td class="td">{{ $s->father_name ?? '—' }}</td>
          <td class="td">{{ $s->phone ?? '—' }}</td>
          <td class="td">{{ $s->religion ?? '—' }}</td>
          <td class="td">{{ $s->admission_date ? \Carbon\Carbon::parse($s->admission_date)->format('d/m/Y') : '—' }}</td>
        </tr>
        @empty
        <tr><td class="td text-slate-400 text-center" colspan="11">No students found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $students->withQueryString()->links() }}</div>
</div>
@endsection
