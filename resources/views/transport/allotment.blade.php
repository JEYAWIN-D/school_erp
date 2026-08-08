@extends('layouts.app')
@section('title','Student Route Allotment')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Student Route Allotment</h1>
    <a href="{{ route('transport.allotment.export') }}" class="btn btn-secondary btn-sm">Export Excel</a>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Assign Route</h3>
      <form method="POST" action="{{ route('transport.allotment.store') }}" class="space-y-3">
        @csrf
        <div><label class="label">Student <span class="text-red-500">*</span></label>
          <select name="enrollment_id" class="select" required>
            <option value="">Search student</option>
            @foreach($enrollments as $e)<option value="{{ $e->id }}">{{ $e->student?->full_name }} ({{ $e->class?->name }})</option>@endforeach
          </select>
        </div>
        <div><label class="label">Route <span class="text-red-500">*</span></label>
          <select name="route_id" class="select" required>
            <option value="">Select route</option>
            @foreach($routes as $r)<option value="{{ $r->id }}">{{ $r->route_name }} ({{ $r->vehicle?->vehicle_number }})</option>@endforeach
          </select>
        </div>
        <div><label class="label">Pickup Stop</label>
          <select name="stop_id" class="select">
            <option value="">Select stop</option>
            @foreach($stops as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div><label class="label">Pickup Time</label><input type="time" name="pickup_time" class="input"></div>
          <div><label class="label">Drop Time</label><input type="time" name="drop_time" class="input"></div>
        </div>
        <div><label class="label">Monthly Fee (₹)</label><input type="number" name="fee" class="input" step="0.01" min="0"></div>
        <button type="submit" class="btn btn-primary">Assign Route</button>
      </form>
    </div>
    <div class="lg:col-span-2 card overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-100 flex gap-3">
        <form method="GET" class="flex gap-3 flex-1">
          <select name="route_id" class="select w-44">
            <option value="">All Routes</option>
            @foreach($routes as $r)<option value="{{ $r->id }}" @selected(request('route_id')==$r->id)>{{ $r->route_name }}</option>@endforeach
          </select>
          <select name="class_id" class="select w-32">
            <option value="">All Classes</option>
            @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
          </select>
          <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
      </div>
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b"><tr>
          @foreach(['Student','Class','Route','Stop','Pickup','Drop','Fee','Action'] as $h)
          <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium">{{ $h }}</th>
          @endforeach
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($allotments as $a)
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 font-medium text-slate-800 text-sm">{{ $a->enrollment?->student?->full_name }}</td>
            <td class="px-4 py-3 text-slate-500 text-xs">{{ $a->enrollment?->class?->name }}</td>
            <td class="px-4 py-3 text-slate-500 text-xs">{{ $a->route?->route_name }}</td>
            <td class="px-4 py-3 text-slate-400 text-xs">{{ $a->stop?->name ?? '—' }}</td>
            <td class="px-4 py-3 text-slate-400 text-xs">{{ $a->pickup_time ?? '—' }}</td>
            <td class="px-4 py-3 text-slate-400 text-xs">{{ $a->drop_time ?? '—' }}</td>
            <td class="px-4 py-3 font-semibold text-xs">{{ $a->fee ? '₹'.number_format($a->fee,0) : '—' }}</td>
            <td class="px-4 py-3">
              <form method="POST" action="{{ route('transport.allotment.delete',$a->id) }}"
                    onsubmit="return confirm('Remove {{ addslashes($a->enrollment?->student?->full_name) }} from {{ addslashes($a->route?->route_name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-400 hover:underline text-xs">Remove</button>
              </form>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No allotments.</td></tr>
          @endforelse
        </tbody>
      </table>
      @if($allotments->hasPages())<div class="px-4 pb-3">{{ $allotments->links() }}</div>@endif
    </div>
  </div>
</div>
@endsection
