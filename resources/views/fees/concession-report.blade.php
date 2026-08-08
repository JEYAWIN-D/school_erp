@extends('layouts.app')
@section('title', 'Concession Report')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Concession Report</h1>

  {{-- Summary cards --}}
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach($summary as $s)
    <div class="card text-center">
      <div class="text-xl font-bold text-slate-800">{{ $s->count }}</div>
      <div class="text-xs text-slate-500 mt-1 capitalize">{{ str_replace('_', ' ', $s->concession_type) }}</div>
      <div class="text-sm font-semibold text-indigo-600 mt-1">₹{{ number_format($s->total_value, 2) }}</div>
    </div>
    @endforeach
  </div>

  {{-- Filters --}}
  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label">Class</label>
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          @foreach($classes as $c)
            <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Concession Type</label>
        <select name="concession_type" class="select w-40">
          <option value="">All Types</option>
          @foreach(['percentage'=>'Percentage','flat'=>'Flat Amount','sibling'=>'Sibling','merit'=>'Merit','staff_ward'=>'Staff Ward','government'=>'Government'] as $v=>$l)
            <option value="{{ $v }}" @selected(request('concession_type')===$v)>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        @foreach(['Student','Class','Concession Type','Value','Scheme','Valid From','Valid To','Granted By','Status'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($concessions as $c)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800">{{ $c->student?->full_name }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $c->student?->currentEnrollment?->class?->name ?? '—' }}</td>
          <td class="px-4 py-3 capitalize text-slate-600">{{ str_replace('_', ' ', $c->concession_type) }}</td>
          <td class="px-4 py-3 font-medium">
            @if($c->value_type === 'percentage')
              {{ $c->value }}%
            @else
              ₹{{ number_format($c->value, 2) }}
            @endif
          </td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $c->scheme?->name ?? '—' }}</td>
          <td class="px-4 py-3 text-xs text-slate-500">{{ $c->valid_from ? \Carbon\Carbon::parse($c->valid_from)->format('d M Y') : '—' }}</td>
          <td class="px-4 py-3 text-xs @if($c->valid_to && \Carbon\Carbon::parse($c->valid_to)->isPast()) text-red-500 @else text-slate-500 @endif">
            {{ $c->valid_to ? \Carbon\Carbon::parse($c->valid_to)->format('d M Y') : 'Ongoing' }}
          </td>
          <td class="px-4 py-3 text-xs text-slate-500">{{ $c->grantedBy?->name ?? '—' }}</td>
          <td class="px-4 py-3"><span class="{{ $c->status === 'active' ? 'badge-green' : 'badge-slate' }} capitalize">{{ $c->status }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No concessions found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($concessions->hasPages())
    <div class="px-4 pb-3 text-sm">{{ $concessions->links() }}</div>
    @endif
  </div>
</div>
@endsection
