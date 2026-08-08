@extends('layouts.app')
@section('title', 'Annual Stock Audit')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Annual Stock Audit Report</h1>
    <div class="flex gap-2">
      <form method="GET" class="flex gap-2 items-center">
        <label class="label text-xs">Year</label>
        <select name="year" class="select text-sm" onchange="this.form.submit()">
          @for($y = now()->year; $y >= now()->year - 5; $y--)
          <option value="{{ $y }}" @selected($y == $year)>{{ $y }}</option>
          @endfor
        </select>
      </form>
      <a href="{{ route('library.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>
  </div>

  {{-- Summary Cards --}}
  <div class="grid grid-cols-4 gap-4">
    <div class="card text-center">
      <p class="text-2xl font-bold text-indigo-700">{{ $totalBooks }}</p>
      <p class="text-xs text-slate-500 mt-1">Unique Titles</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-slate-800">{{ $totalCopies }}</p>
      <p class="text-xs text-slate-500 mt-1">Total Copies</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-green-600">{{ $totalAvailable }}</p>
      <p class="text-xs text-slate-500 mt-1">Available</p>
    </div>
    <div class="card text-center">
      <p class="text-2xl font-bold text-red-500">{{ $totalDeaccessioned }}</p>
      <p class="text-xs text-slate-500 mt-1">Written Off</p>
    </div>
  </div>

  {{-- Table --}}
  <div class="card overflow-x-auto">
    <table class="table-wrap w-full text-sm">
      <thead>
        <tr>
          <th class="th">Accession No.</th>
          <th class="th">Title</th>
          <th class="th">Author</th>
          <th class="th">Total</th>
          <th class="th">Available</th>
          <th class="th">Issued</th>
          <th class="th">Lost</th>
          <th class="th">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($books as $row)
        @php $book = $row['book']; @endphp
        <tr class="tr {{ $row['deaccessioned'] ? 'opacity-50' : '' }}">
          <td class="td font-mono text-xs">{{ $book->accession_number }}</td>
          <td class="td font-medium">{{ $book->title }}</td>
          <td class="td text-slate-500">{{ $book->author ?? '—' }}</td>
          <td class="td text-center">{{ $row['total_copies'] }}</td>
          <td class="td text-center text-green-600 font-semibold">{{ $row['available'] }}</td>
          <td class="td text-center text-indigo-600">{{ $row['issued'] }}</td>
          <td class="td text-center {{ $row['lost'] > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">{{ $row['lost'] ?: '—' }}</td>
          <td class="td">
            @if($row['deaccessioned'])
              <span class="badge-red">Written Off</span>
            @elseif($row['available'] == 0)
              <span class="badge-amber">All Issued</span>
            @else
              <span class="badge-green">Available</span>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="td text-center text-slate-400 py-8">No books found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <p class="text-xs text-slate-400 text-right">Generated: {{ now()->format('d M Y, h:i A') }}</p>
</div>
@endsection
