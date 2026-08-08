@extends('layouts.app')
@section('title', 'Acquisition Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Acquisition Report</h1>
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
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </form>
  </div>

  {{-- Summary --}}
  <div class="grid grid-cols-3 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-indigo-700">{{ $books->count() }}</p>
      <p class="text-xs text-slate-500 mt-1">Titles Acquired</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-slate-800">{{ $totalCopies }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Copies</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-green-600">₹{{ number_format($totalValue, 2) }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Value</p>
    </div>
  </div>

  {{-- Table --}}
  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">#</th>
          <th class="th">Accession No.</th>
          <th class="th">Title</th>
          <th class="th">Author</th>
          <th class="th">Publisher</th>
          <th class="th">ISBN</th>
          <th class="th">Copies</th>
          <th class="th">Price (₹)</th>
          <th class="th">Total (₹)</th>
          <th class="th">Purchase Date</th>
          <th class="th">Location</th>
        </tr>
      </thead>
      <tbody>
        @forelse($books as $book)
        <tr class="tr">
          <td class="td text-slate-400">{{ $loop->iteration }}</td>
          <td class="td font-mono text-xs">{{ $book->accession_number }}</td>
          <td class="td font-medium">{{ $book->title }}</td>
          <td class="td text-slate-500">{{ $book->author ?? '—' }}</td>
          <td class="td text-slate-500">{{ $book->publisher ?? '—' }}</td>
          <td class="td font-mono text-xs">{{ $book->isbn ?? '—' }}</td>
          <td class="td text-center">{{ $book->total_copies }}</td>
          <td class="td text-right">{{ $book->purchase_price ? number_format($book->purchase_price, 2) : '—' }}</td>
          <td class="td text-right font-semibold">{{ $book->purchase_price ? number_format($book->purchase_price * $book->total_copies, 2) : '—' }}</td>
          <td class="td">{{ $book->purchase_date?->format('d M Y') ?? '—' }}</td>
          <td class="td text-slate-500">{{ $book->location ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="11" class="td text-center text-slate-400 py-8">No acquisitions in this period.</td></tr>
        @endforelse
      </tbody>
      @if($books->count())
      <tfoot>
        <tr class="bg-slate-50 font-semibold">
          <td colspan="6" class="td text-right">Total:</td>
          <td class="td text-center">{{ $totalCopies }}</td>
          <td class="td"></td>
          <td class="td text-right">₹{{ number_format($totalValue, 2) }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>

  <p class="text-xs text-slate-400 text-right">{{ $from->format('d M Y') }} – {{ $to->format('d M Y') }} | Generated: {{ now()->format('d M Y, h:i A') }}</p>
</div>
@endsection
