@extends('layouts.app')
@section('title','Student Fee Ledger')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Student Fee Ledger</h1>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="class_id" class="select w-36">
      <option value="">Select Class</option>
      @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="student_id" class="select w-56">
      <option value="">Select Student</option>
      @foreach($students as $s)<option value="{{ $s->id }}" @selected(request('student_id')==$s->id)>{{ $s->full_name }}</option>@endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Load Ledger</button>
  </div></form>

  @if(isset($enrollment))
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="card text-center py-4"><p class="text-xs text-slate-400 uppercase">Total Fees</p><p class="text-xl font-bold text-slate-800 mt-1">₹{{ number_format($totalFees,2) }}</p></div>
    <div class="card text-center py-4"><p class="text-xs text-slate-400 uppercase">Paid</p><p class="text-xl font-bold text-green-700 mt-1">₹{{ number_format($totalPaid,2) }}</p></div>
    <div class="card text-center py-4"><p class="text-xs text-slate-400 uppercase">Concession</p><p class="text-xl font-bold text-indigo-600 mt-1">₹{{ number_format($totalConcession,2) }}</p></div>
    <div class="card text-center py-4"><p class="text-xs text-slate-400 uppercase">Balance Due</p><p class="text-xl font-bold {{ $balance > 0 ? 'text-red-600' : 'text-slate-800' }} mt-1">₹{{ number_format($balance,2) }}</p></div>
  </div>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <div>
        <p class="font-semibold text-slate-800">{{ $enrollment->student?->full_name }}</p>
        <p class="text-xs text-slate-400">{{ $enrollment->class?->name }} {{ $enrollment->section?->name }} | {{ $enrollment->roll_number }}</p>
      </div>
      <a href="{{ route('fees.ledger.pdf', request()->query()) }}" class="btn btn-secondary btn-sm">Print Ledger</a>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Date','Receipt No','Fee Head','Charged','Paid','Late Fee','Discount','Balance','Mode','Status'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($ledger as $row)
        <tr class="{{ $row['type']==='charge'?'bg-amber-50/40':'' }} hover:bg-slate-50">
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $row['date'] }}</td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700">{{ $row['receipt'] ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-700 text-xs">{{ $row['fee_head'] }}</td>
          <td class="px-4 py-3 text-amber-600 font-semibold text-xs">{{ $row['charged'] > 0 ? '₹'.number_format($row['charged'],2) : '' }}</td>
          <td class="px-4 py-3 text-green-700 font-semibold text-xs">{{ $row['paid'] > 0 ? '₹'.number_format($row['paid'],2) : '' }}</td>
          <td class="px-4 py-3 text-red-400 text-xs">{{ $row['late_fee'] > 0 ? '₹'.number_format($row['late_fee'],2) : '—' }}</td>
          <td class="px-4 py-3 text-indigo-500 text-xs">{{ $row['discount'] > 0 ? '₹'.number_format($row['discount'],2) : '—' }}</td>
          <td class="px-4 py-3 font-semibold text-xs {{ $row['balance'] > 0 ? 'text-red-600' : 'text-slate-700' }}">₹{{ number_format($row['balance'],2) }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs capitalize">{{ $row['mode'] ?? '—' }}</td>
          <td class="px-4 py-3"><span class="badge-{{ $row['status']==='paid'?'green':($row['status']==='partial'?'amber':'slate') }} capitalize text-xs">{{ $row['status'] ?? '—' }}</span></td>
        </tr>
        @empty
        <tr><td colspan="10" class="px-4 py-8 text-center text-slate-400">No transactions found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
