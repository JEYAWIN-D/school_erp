@extends('layouts.app')
@section('title', 'Deaccession Register')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Deaccession Register</h1>
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
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </form>
  </div>

  @if($books->isEmpty())
    <div class="card text-center py-12 text-slate-400">
      No books deaccessioned in this period.
    </div>
  @else
  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Accession No.</th>
          <th class="th">Title</th>
          <th class="th">Author</th>
          <th class="th">Publisher</th>
          <th class="th">Total Copies</th>
          <th class="th">Write-off Date</th>
          <th class="th">Reason</th>
        </tr>
      </thead>
      <tbody>
        @foreach($books as $book)
        <tr class="tr">
          <td class="td text-slate-400">{{ $loop->iteration }}</td>
          <td class="td font-mono text-xs">{{ $book->accession_number }}</td>
          <td class="td font-medium">{{ $book->title }}</td>
          <td class="td text-slate-500">{{ $book->author ?? '—' }}</td>
          <td class="td text-slate-500">{{ $book->publisher ?? '—' }}</td>
          <td class="td text-center">{{ $book->total_copies }}</td>
          <td class="td">{{ $book->deaccession_date?->format('d M Y') ?? '—' }}</td>
          <td class="td">{{ $book->deaccession_reason ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="bg-slate-50 font-semibold">
          <td colspan="5" class="td text-right">Total Books Written Off:</td>
          <td class="td text-center">{{ $books->sum('total_copies') }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
  @endif

  <p class="text-xs text-slate-400 text-right">Period: {{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</p>
</div>
@endsection
