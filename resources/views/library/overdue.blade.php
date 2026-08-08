@extends('layouts.app')
@section('title', 'Overdue Books')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Overdue Books</h1>
    <form method="POST" action="{{ route('library.send-overdue-reminders') }}">
      @csrf
      <button type="submit" class="btn-sm btn-primary"
        onclick="return confirm('Send overdue email reminders to all members with overdue books?')">
        Send Email Reminders
      </button>
    </form>
  </div>
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr><th class="th">Book</th><th class="th">Student</th><th class="th">Issue Date</th><th class="th">Due Date</th><th class="th">Days Overdue</th><th class="th">Fine</th><th class="th text-right">Action</th></tr></thead>
      <tbody>
        @forelse($overdueIssues as $issue)
          <tr class="tr">
            <td class="td font-medium">{{ $issue->book?->title }}</td>
            <td class="td">{{ $issue->student?->full_name ?? '—' }}</td>
            <td class="td">{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}</td>
            <td class="td text-red-600 font-semibold">{{ \Carbon\Carbon::parse($issue->due_date)->format('d M Y') }}</td>
            <td class="td"><span class="badge-red">{{ now()->diffInDays($issue->due_date) }} days</span></td>
            <td class="td font-semibold text-red-600">₹{{ now()->diffInDays($issue->due_date) * 2 }}</td>
            <td class="td text-right">
              <form method="POST" action="{{ route('library.return') }}">
                @csrf
                <input type="hidden" name="issue_id" value="{{ $issue->id }}">
                <button type="submit" class="btn btn-secondary btn-sm">Return</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="td text-center py-10 text-slate-400">No overdue books.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($overdueIssues->hasPages())<div class="text-sm mt-3">{{ $overdueIssues->links() }}</div>@endif
</div>
@endsection
