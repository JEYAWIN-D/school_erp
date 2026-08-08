@extends('layouts.app')
@section('title', 'Stock Issuances')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Stock Issuances</h1>
    <div class="flex gap-2">
      <a href="{{ route('inventory.index') }}" class="btn-sm btn-secondary">← Inventory</a>
      <a href="{{ route('inventory.issuances.create') }}" class="btn-primary btn-sm">+ Issue Stock</a>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">Issue No.</th>
          <th class="th">Date</th>
          <th class="th">Issued To</th>
          <th class="th">Purpose</th>
          <th class="th">Issued By</th>
          <th class="th">Items</th>
        </tr>
      </thead>
      <tbody>
        @forelse($issuances as $iss)
        <tr class="tr" x-data="{ open: false }">
          <td class="td font-mono font-semibold text-indigo-600 cursor-pointer" @click="open=!open">{{ $iss->issue_number }}</td>
          <td class="td">{{ \Carbon\Carbon::parse($iss->issue_date)->format('d M Y') }}</td>
          <td class="td font-medium">{{ $iss->issued_to }}</td>
          <td class="td text-xs text-slate-500">{{ Str::limit($iss->purpose, 50) ?? '—' }}</td>
          <td class="td text-xs">{{ $iss->issuedBy?->name ?? '—' }}</td>
          <td class="td text-center">{{ $iss->items->count() }}</td>
        </tr>
        <tr x-show="open" x-cloak class="bg-slate-50">
          <td colspan="6" class="px-6 py-3">
            <table class="w-full text-xs">
              <thead><tr class="text-slate-500"><th class="text-left py-1">Item</th><th class="text-right">Qty</th><th class="text-left pl-4">Remark</th></tr></thead>
              <tbody>
                @foreach($iss->items as $si)
                <tr>
                  <td class="py-1">{{ $si->item?->name }}</td>
                  <td class="text-right">{{ $si->quantity }} {{ $si->item?->unit }}</td>
                  <td class="pl-4 text-slate-500">{{ $si->remark ?? '—' }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </td>
        </tr>
        @empty
        <tr><td class="td text-center text-slate-400" colspan="6">No issuances recorded.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div>{{ $issuances->withQueryString()->links() }}</div>
</div>
@endsection
