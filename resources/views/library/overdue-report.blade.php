@extends('layouts.app')
@section('title', 'Overdue Books Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Overdue Books Report</h1>
      <p class="page-subtitle">Books not returned by their due date</p>
    </div>
    <a href="{{ route('library.currently-issued') }}" class="btn btn-secondary">All Issued</a>
  </div>

  @if($overdueIssues->count() > 0)
  <div class="alert-warning">
    <strong>{{ $overdueIssues->total() }} overdue books</strong> found. Please follow up with borrowers.
  </div>
  @endif

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Book</th>
            <th class="th">Student / Borrower</th>
            <th class="th">Issue Date</th>
            <th class="th">Due Date</th>
            <th class="th">Days Overdue</th>
            <th class="th">Fine (₹2/day)</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($overdueIssues as $issue)
          @php
            $daysOverdue = today()->diffInDays($issue->due_date);
            $fine = $daysOverdue * 2;
          @endphp
          <tr class="tr">
            <td class="td">
              <div class="font-medium text-slate-800">{{ $issue->book?->title }}</div>
              <div class="text-xs text-slate-400">{{ $issue->book?->accession_number }}</div>
            </td>
            <td class="td font-medium">{{ $issue->student?->first_name }} {{ $issue->student?->last_name }}</td>
            <td class="td text-sm">{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}</td>
            <td class="td text-sm text-red-600 font-semibold">{{ \Carbon\Carbon::parse($issue->due_date)->format('d M Y') }}</td>
            <td class="td">
              <span class="font-bold text-red-600">{{ $daysOverdue }}</span>
            </td>
            <td class="td font-semibold text-amber-600">₹{{ number_format($fine, 2) }}</td>
            <td class="td">
              <form method="POST" action="{{ route('library.return') }}" class="inline">
                @csrf
                <input type="hidden" name="issue_id" value="{{ $issue->id }}">
                <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Record return for this book?')">Return</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="td text-center py-10 text-green-600 font-semibold">No overdue books — great!</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($overdueIssues->hasPages())<div class="mt-4">{{ $overdueIssues->links() }}</div>@endif
  </div>
</div>
@endsection
