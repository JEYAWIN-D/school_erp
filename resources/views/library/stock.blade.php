@extends('layouts.app')
@section('title','Stock Register')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Stock Register</h1>
    <a href="{{ route('library.stock.export') }}" class="btn btn-secondary btn-sm">Export Excel</a>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Accession No','Title','Author','Publisher','Category','Total','Available','Issued','Lost','Price','Purchase Date'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($books as $b)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-mono text-xs text-indigo-700">{{ $b->accession_number }}</td>
          <td class="px-4 py-3 font-medium text-slate-800 text-sm max-w-xs">{{ $b->title }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $b->author ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $b->publisher ?? '—' }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $b->category ?? '—' }}</td>
          <td class="px-4 py-3 text-center font-semibold">{{ $b->total_copies }}</td>
          <td class="px-4 py-3 text-center text-green-700 font-semibold">{{ $b->available_copies }}</td>
          <td class="px-4 py-3 text-center text-amber-600 font-semibold">{{ $b->total_copies - $b->available_copies }}</td>
          <td class="px-4 py-3 text-center text-red-500 text-xs">{{ $b->lost_copies ?? 0 }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $b->purchase_price ? '₹'.number_format($b->purchase_price,2) : '—' }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $b->purchase_date ? \Carbon\Carbon::parse($b->purchase_date)->format('d M Y') : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="11" class="px-4 py-8 text-center text-slate-400">No books in catalogue.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($books->hasPages())<div class="px-4 pb-3">{{ $books->links() }}</div>@endif
  </div>
</div>
@endsection
