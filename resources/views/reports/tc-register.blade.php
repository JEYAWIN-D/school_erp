@extends('layouts.app')
@section('title', 'TC Register')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Transfer Certificate Register</h1>
    <div class="flex gap-2">
      <a href="{{ route('dashboard') }}" class="btn-sm btn-secondary">← Dashboard</a>
      <button onclick="window.print()" class="btn-sm btn-primary flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print
      </button>
    </div>
  </div>

  <form method="GET" class="card flex flex-wrap gap-4 items-end">
    <div>
      <label class="label">From Date</label>
      <input type="date" name="from" value="{{ request('from') }}" class="input">
    </div>
    <div>
      <label class="label">To Date</label>
      <input type="date" name="to" value="{{ request('to') }}" class="input">
    </div>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('reports.tc-register') }}" class="btn-sm btn-secondary">Reset</a>
    <span class="text-sm text-slate-500 ml-auto self-center">Total: <strong>{{ $students->total() }}</strong> TCs</span>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">TC No.</th>
          <th class="th">Adm. No.</th>
          <th class="th">Student Name</th>
          <th class="th">Father</th>
          <th class="th">DOB</th>
          <th class="th">Gender</th>
          <th class="th">Last Class</th>
          <th class="th">Leaving Date</th>
          <th class="th">Issue Date</th>
          <th class="th">Reason</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $s)
        <tr class="tr">
          <td class="td font-mono font-semibold">{{ $s->tc_number }}</td>
          <td class="td font-mono text-xs">{{ $s->admission_number }}</td>
          <td class="td font-medium">{{ $s->first_name }} {{ $s->last_name }}</td>
          <td class="td">{{ $s->father_name ?? '—' }}</td>
          <td class="td">{{ $s->dob ? \Carbon\Carbon::parse($s->dob)->format('d/m/Y') : '—' }}</td>
          <td class="td capitalize">{{ $s->gender }}</td>
          <td class="td">{{ $s->last_class ?? '—' }}</td>
          <td class="td">{{ $s->leaving_date ? \Carbon\Carbon::parse($s->leaving_date)->format('d/m/Y') : '—' }}</td>
          <td class="td">{{ $s->issue_date ? \Carbon\Carbon::parse($s->issue_date)->format('d/m/Y') : '—' }}</td>
          <td class="td text-xs text-slate-500">{{ $s->reason ?? '—' }}</td>
        </tr>
        @empty
        <tr><td class="td text-slate-400 text-center" colspan="10">No TC records found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $students->withQueryString()->links() }}</div>
</div>
@endsection
