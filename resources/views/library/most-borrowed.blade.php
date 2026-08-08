@extends('layouts.app')
@section('title', 'Most Borrowed Books')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Most Borrowed Books</h1>
      <p class="page-subtitle">Top books by borrow frequency</p>
    </div>
    <a href="{{ route('library.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Period</label>
        <select name="period" class="select">
          @foreach([7 => 'Last 7 days', 30 => 'Last 30 days', 90 => 'Last 3 months', 365 => 'Last 1 year'] as $days => $label)
          <option value="{{ $days }}" {{ $period == $days ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">#</th>
            <th class="th">Book Title</th>
            <th class="th">Author</th>
            <th class="th">Category</th>
            <th class="th text-center">Times Borrowed</th>
            <th class="th text-center">Available</th>
            <th class="th text-center">Total Copies</th>
          </tr>
        </thead>
        <tbody>
          @forelse($books as $book)
          @php $maxBorrow = $books->first()->borrow_count ?: 1; @endphp
          <tr class="tr">
            <td class="td text-slate-400">
              @if($loop->index < 3)
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-xs font-bold {{ ['bg-amber-500','bg-slate-400','bg-orange-600'][$loop->index] }}">{{ $loop->iteration }}</span>
              @else
                {{ $loop->iteration }}
              @endif
            </td>
            <td class="td">
              <div class="font-medium text-slate-800">{{ $book->title }}</div>
              <div class="text-xs text-slate-400 font-mono">{{ $book->accession_number }}</div>
            </td>
            <td class="td text-slate-500">{{ $book->author ?? '—' }}</td>
            <td class="td text-slate-500">{{ $book->category ?? '—' }}</td>
            <td class="td text-center">
              <div class="flex items-center justify-center gap-2">
                <div class="w-16 bg-slate-200 rounded-full h-2">
                  <div class="h-2 rounded-full bg-indigo-500" style="width:{{ $maxBorrow > 0 ? round($book->borrow_count/$maxBorrow*100) : 0 }}%"></div>
                </div>
                <span class="font-semibold text-indigo-600">{{ $book->borrow_count }}</span>
              </div>
            </td>
            <td class="td text-center {{ $book->available_copies == 0 ? 'text-red-500 font-semibold' : 'text-green-600' }}">{{ $book->available_copies }}</td>
            <td class="td text-center text-slate-500">{{ $book->total_copies }}</td>
          </tr>
          @empty
          <tr><td colspan="7" class="td text-center py-10 text-slate-400">No borrowing data for this period.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($books->hasPages())<div class="mt-4">{{ $books->links() }}</div>@endif
  </div>
</div>
@endsection
