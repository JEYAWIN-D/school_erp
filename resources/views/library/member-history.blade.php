@extends('layouts.app')
@section('title', 'Borrowing History — ' . $student->first_name)
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div class="flex items-center gap-4">
      <a href="{{ route('library.members') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
      <div>
        <h1 class="page-title">{{ $student->first_name }} {{ $student->last_name }}</h1>
        <p class="page-subtitle">Borrowing History</p>
      </div>
    </div>
  </div>

  @php
    $totalBorrowed = $issues->total();
    $currentlyHeld = $issues->where('status', 'issued')->count();
    $overdue       = $issues->where('status', 'issued')->filter(fn($i) => today()->gt($i->due_date))->count();
  @endphp
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center py-4"><p class="text-2xl font-bold text-slate-700">{{ $totalBorrowed }}</p><p class="text-sm text-slate-500">Total Borrowed</p></div>
    <div class="card text-center py-4"><p class="text-2xl font-bold text-blue-600">{{ $currentlyHeld }}</p><p class="text-sm text-slate-500">Currently Held</p></div>
    <div class="card text-center py-4"><p class="text-2xl font-bold text-red-500">{{ $overdue }}</p><p class="text-sm text-slate-500">Overdue</p></div>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Book</th>
            <th class="th">Issue Date</th>
            <th class="th">Due Date</th>
            <th class="th">Return Date</th>
            <th class="th">Fine</th>
            <th class="th">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($issues as $issue)
          <tr class="tr">
            <td class="td">
              <div class="font-medium">{{ $issue->book?->title }}</div>
              <div class="text-xs text-slate-400">{{ $issue->book?->accession_number }}</div>
            </td>
            <td class="td text-sm">{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}</td>
            <td class="td text-sm">{{ \Carbon\Carbon::parse($issue->due_date)->format('d M Y') }}</td>
            <td class="td text-sm">{{ $issue->return_date ? \Carbon\Carbon::parse($issue->return_date)->format('d M Y') : '—' }}</td>
            <td class="td">{{ $issue->fine_amount > 0 ? '₹' . $issue->fine_amount : '—' }}</td>
            <td class="td">
              @php $isOverdue = $issue->status === 'issued' && today()->gt($issue->due_date); @endphp
              <span class="badge-{{ $issue->status === 'returned' ? 'green' : ($isOverdue ? 'red' : 'blue') }}">
                {{ $isOverdue ? 'Overdue' : ucfirst($issue->status) }}
              </span>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="td text-center py-10 text-slate-400">No borrowing history found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($issues->hasPages())<div class="mt-4">{{ $issues->links() }}</div>@endif
  </div>
</div>
@endsection
