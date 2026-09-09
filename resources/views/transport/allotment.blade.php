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
      <div class="pb-2 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Assign Route</h3>
        <p class="text-xs text-slate-400">Allocate student to a school route and bus stop</p>
      </div>
      <form method="POST" action="{{ route('transport.allotment.store') }}" class="space-y-3" x-data="{
        selectedRoute: '{{ old('route_id') }}',
        selectedStop: '{{ old('stop_id') }}',
        stopsData: {{ json_encode($stops->map(fn($s) => [
          'id' => $s->id,
          'route_id' => $s->route_id,
          'name' => $s->name,
          'distance_km' => $s->distance_km,
          'van' => $s->effective_van_number,
          'fare' => $s->fare,
          'landmark' => $s->landmark
        ])) }},
        get availableStops() {
          return this.selectedRoute ? this.stopsData.filter(s => s.route_id == this.selectedRoute) : this.stopsData;
        },
        get currentStopInfo() {
          return this.stopsData.find(s => s.id == this.selectedStop);
        }
      }">
        @csrf
        <div>
          <label class="label">Student <span class="text-red-500">*</span></label>
          <select name="enrollment_id" class="select" required>
            <option value="">Search &amp; select student</option>
            @foreach($enrollments as $e)
              <option value="{{ $e->id }}">{{ $e->student?->full_name }} ({{ $e->class?->name }})</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="label">Route <span class="text-red-500">*</span></label>
          <select name="route_id" class="select" x-model="selectedRoute" required>
            <option value="">Select route</option>
            @foreach($routes as $r)
              <option value="{{ $r->id }}">{{ $r->route_name }} @if($r->vehicle) ({{ $r->vehicle->vehicle_number }}) @endif</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="label">Pickup Stop</label>
          <select name="stop_id" class="select" x-model="selectedStop">
            <option value="">Select bus stop</option>
            <template x-for="st in availableStops" :key="st.id">
              <option :value="st.id" x-text="st.name + (st.distance_km ? ' • ' + st.distance_km + ' km' : '')"></option>
            </template>
          </select>
        </div>

        {{-- Stop Details Preview Card --}}
        <div x-show="currentStopInfo" x-cloak class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl space-y-1.5 text-xs">
          <div class="flex justify-between items-center">
            <span class="text-slate-500 font-medium">Assigned Van:</span>
            <span class="font-bold text-blue-900" x-text="currentStopInfo?.van || 'Route Van'"></span>
          </div>
          <div class="flex justify-between items-center" x-show="currentStopInfo?.distance_km">
            <span class="text-slate-500 font-medium">Distance from School:</span>
            <span class="font-mono font-bold text-slate-800" x-text="currentStopInfo?.distance_km + ' km'"></span>
          </div>
          <div class="flex justify-between items-center pt-1 border-t border-blue-200/50" x-show="currentStopInfo?.fare">
            <span class="text-slate-500 font-bold">Annual Bus Fee:</span>
            <span class="font-mono font-extrabold text-emerald-700" x-text="'₹' + Number(currentStopInfo?.fare || 0).toLocaleString('en-IN')"></span>
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-full shadow-xs">Assign Route</button>
      </form>
    </div>

    <div class="lg:col-span-2 card overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-100 flex gap-3 flex-wrap items-center justify-between">
        <form method="GET" class="flex gap-2 flex-wrap items-center flex-1">
          <select name="route_id" class="select w-44 text-xs">
            <option value="">All Routes</option>
            @foreach($routes as $r)
              <option value="{{ $r->id }}" @selected(request('route_id')==$r->id)>{{ $r->route_name }}</option>
            @endforeach
          </select>
          <select name="class_id" class="select w-32 text-xs">
            <option value="">All Classes</option>
            @foreach($classes as $c)
              <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
        <span class="text-xs font-bold text-slate-500">{{ $allotments->total() }} Students Allotted</span>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
              <th class="text-left px-4 py-3 text-slate-600 text-[10px] uppercase font-bold tracking-wider">Student Name</th>
              <th class="text-left px-4 py-3 text-slate-600 text-[10px] uppercase font-bold tracking-wider">Standard</th>
              <th class="text-left px-4 py-3 text-slate-600 text-[10px] uppercase font-bold tracking-wider">Route</th>
              <th class="text-left px-4 py-3 text-slate-600 text-[10px] uppercase font-bold tracking-wider">Bus Stop &amp; Distance</th>
              <th class="text-left px-4 py-3 text-slate-600 text-[10px] uppercase font-bold tracking-wider">Van / Bus No</th>
              <th class="text-center px-4 py-3 text-slate-600 text-[10px] uppercase font-bold tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 font-medium">
            @forelse($allotments as $a)
            <tr class="hover:bg-slate-50/70 transition">
              <td class="px-4 py-3">
                <span class="font-bold text-slate-900 block text-xs">{{ $a->enrollment?->student?->full_name }}</span>
                <span class="text-[10px] font-mono text-slate-400">{{ $a->enrollment?->student?->admission_number }}</span>
              </td>
              <td class="px-4 py-3 text-slate-700 text-xs font-semibold whitespace-nowrap">
                {{ $a->enrollment?->class?->name }}
              </td>
              <td class="px-4 py-3 text-slate-600 text-xs">
                {{ $a->route?->route_name }}
              </td>
              <td class="px-4 py-3">
                <div class="text-xs font-bold text-slate-800">{{ $a->stop?->name ?? '—' }}</div>
                @if($a->stop?->distance_km)
                  <div class="text-[10px] text-slate-500 font-mono">{{ $a->stop->distance_km }} km from campus</div>
                @endif
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200">
                  🚐 {{ $a->stop?->effective_van_number ?? ($a->route?->vehicle?->vehicle_number ?? 'Van Assigned') }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <form method="POST" action="{{ route('transport.allotment.delete', $a->id) }}"
                      onsubmit="return confirm('Remove {{ addslashes($a->enrollment?->student?->full_name) }} from route?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                    Remove
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-12 text-center text-slate-400 font-medium">No students allotted to routes yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($allotments->hasPages())<div class="px-4 py-3 border-t border-slate-100">{{ $allotments->links() }}</div>@endif
    </div>
  </div>
</div>
@endsection
