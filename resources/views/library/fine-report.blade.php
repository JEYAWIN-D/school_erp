@extends('layouts.app')
@section('title', 'Fine Collection Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Fine Collection Report</h1>
    <a href="{{ route('library.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  {{-- Filters --}}
  <div class="card">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">From</label>
        <input type="date" name="from" value="{{ $from->toDateString() }}" class="input text-sm">
      </div>
      <div>
        <label class="label text-xs">To</label>
        <input type="date" name="to" value="{{ $to->toDateString() }}" class="input text-sm">
      </div>
      <div>
        <label class="label text-xs">Payment Status</label>
        <select name="paid" class="select text-sm">
          <option value="">All</option>
          <option value="yes" @selected(request('paid') === 'yes')>Paid</option>
          <option value="no"  @selected(request('paid') === 'no')>Pending</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </form>
  </div>

  {{-- Summary --}}
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-slate-800">₹{{ number_format($totalFine, 2) }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Fines</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-green-600">₹{{ number_format($totalPaid, 2) }}</p>
      <p class="text-xs text-slate-500 mt-1">Collected</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-red-600">₹{{ number_format($totalPending, 2) }}</p>
      <p class="text-xs text-slate-500 mt-1">Pending</p>
    </div>
  </div>

  {{-- Table --}}
  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Member</th>
          <th class="th">Book</th>
          <th class="th">Due Date</th>
          <th class="th">Return Date</th>
          <th class="th">Fine (₹)</th>
          <th class="th">Status</th>
          <th class="th">Receipt</th>
        </tr>
      </thead>
      <tbody>
        @forelse($issues as $issue)
        <tr class="tr {{ $issue->fine_paid ? '' : 'bg-red-50' }}">
          <td class="td text-slate-400">{{ $loop->iteration }}</td>
          <td class="td">
            @if($issue->student)
              {{ $issue->student->first_name }} {{ $issue->student->last_name }}<br>
              <span class="text-xs text-slate-400">{{ $issue->student->admission_number }}</span>
            @else
              Staff
            @endif
          </td>
          <td class="td">{{ $issue->book->title }}</td>
          <td class="td">{{ $issue->due_date->format('d M Y') }}</td>
          <td class="td">{{ $issue->return_date?->format('d M Y') ?? '—' }}</td>
          <td class="td font-semibold">{{ number_format($issue->fine_amount, 2) }}</td>
          <td class="td">
            @if($issue->fine_paid)
              <span class="badge-green">Paid</span>
            @else
              <span class="badge-red">Pending</span>
            @endif
          </td>
          <td class="td">
            <a href="{{ route('library.issue.receipt', $issue->id) }}" target="_blank" class="btn-xs btn-secondary">Receipt</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="td text-center text-slate-400 py-8">No fines in this period.</td></tr>
        @endforelse
      </tbody>
      @if($issues->count())
      <tfoot>
        <tr class="bg-slate-50">
          <td colspan="5" class="td font-semibold text-right">Total:</td>
          <td class="td font-bold">₹{{ number_format($totalFine, 2) }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
