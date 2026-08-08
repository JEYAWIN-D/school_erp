@extends('layouts.app')
@section('title', 'Accident & Incident Log')
@section('content')
<div class="space-y-6" x-data="{ showAdd: false }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Accident & Incident Log</h1>
    <div class="flex gap-2">
      <a href="{{ route('transport.drivers') }}" class="btn btn-secondary">← Drivers</a>
      <button @click="showAdd=!showAdd" class="btn btn-primary">Log Incident</button>
    </div>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  {{-- Add Incident Form --}}
  <div x-show="showAdd" x-transition class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Log New Incident</h3>
    <form method="POST" action="{{ route('transport.incidents.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      <div>
        <label class="label">Date <span class="text-red-500">*</span></label>
        <input type="date" name="incident_date" class="input" value="{{ date('Y-m-d') }}" required>
      </div>
      <div>
        <label class="label">Type <span class="text-red-500">*</span></label>
        <select name="incident_type" class="select" required>
          <option value="">Select type</option>
          <option value="accident">Accident</option>
          <option value="breakdown">Breakdown</option>
          <option value="theft">Theft</option>
          <option value="vandalism">Vandalism</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div>
        <label class="label">Vehicle</label>
        <select name="vehicle_id" class="select">
          <option value="">Select vehicle</option>
          @foreach($vehicles as $v)<option value="{{ $v->id }}">{{ $v->vehicle_number }}</option>@endforeach
        </select>
      </div>
      <div>
        <label class="label">Route</label>
        <select name="route_id" class="select">
          <option value="">Select route</option>
          @foreach($routes as $r)<option value="{{ $r->id }}">{{ $r->route_name }}</option>@endforeach
        </select>
      </div>
      <div>
        <label class="label">Location</label>
        <input type="text" name="location" class="input" placeholder="Where it occurred">
      </div>
      <div>
        <label class="label">Severity <span class="text-red-500">*</span></label>
        <select name="severity" class="select" required>
          <option value="minor">Minor</option>
          <option value="moderate">Moderate</option>
          <option value="major">Major</option>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="label">Description <span class="text-red-500">*</span></label>
        <textarea name="description" class="input" rows="3" required placeholder="What happened?"></textarea>
      </div>
      <div class="md:col-span-2">
        <label class="label">Action Taken</label>
        <textarea name="action_taken" class="input" rows="2" placeholder="Immediate response, repairs initiated, etc."></textarea>
      </div>
      <div>
        <label class="label">Reported By</label>
        <input type="text" name="reported_by" class="input" placeholder="Driver / warden / admin">
      </div>
      <div>
        <label class="label">FIR Number</label>
        <input type="text" name="fir_number" class="input" placeholder="If police case filed">
      </div>
      <div>
        <label class="label">Estimated Loss (₹)</label>
        <input type="number" name="estimated_loss" class="input" min="0" step="0.01">
      </div>
      <div class="md:col-span-2 flex gap-3">
        <button type="submit" class="btn btn-primary">Save Incident</button>
        <button type="button" @click="showAdd=false" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>

  {{-- Filters --}}
  <form method="GET" class="card flex flex-wrap gap-3 items-end">
    <div>
      <label class="label text-xs">Vehicle</label>
      <select name="vehicle_id" class="select text-sm py-1.5" onchange="this.form.submit()">
        <option value="">All Vehicles</option>
        @foreach($vehicles as $v)<option value="{{ $v->id }}" @selected(request('vehicle_id')==$v->id)>{{ $v->vehicle_number }}</option>@endforeach
      </select>
    </div>
    <div>
      <label class="label text-xs">Type</label>
      <select name="incident_type" class="select text-sm py-1.5" onchange="this.form.submit()">
        <option value="">All Types</option>
        <option value="accident"   @selected(request('incident_type')=='accident')>Accident</option>
        <option value="breakdown"  @selected(request('incident_type')=='breakdown')>Breakdown</option>
        <option value="theft"      @selected(request('incident_type')=='theft')>Theft</option>
        <option value="vandalism"  @selected(request('incident_type')=='vandalism')>Vandalism</option>
        <option value="other"      @selected(request('incident_type')=='other')>Other</option>
      </select>
    </div>
    <div>
      <label class="label text-xs">Status</label>
      <select name="status" class="select text-sm py-1.5" onchange="this.form.submit()">
        <option value="">All</option>
        <option value="open"         @selected(request('status')=='open')>Open</option>
        <option value="under_review" @selected(request('status')=='under_review')>Under Review</option>
        <option value="resolved"     @selected(request('status')=='resolved')>Resolved</option>
        <option value="closed"       @selected(request('status')=='closed')>Closed</option>
      </select>
    </div>
  </form>

  {{-- Incidents Table --}}
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">Date</th>
        <th class="th">Type</th>
        <th class="th">Vehicle / Route</th>
        <th class="th">Location</th>
        <th class="th">Severity</th>
        <th class="th">Description</th>
        <th class="th">Status</th>
        <th class="th">Action</th>
      </tr></thead>
      <tbody>
        @forelse($incidents as $inc)
        <tr class="tr">
          <td class="td whitespace-nowrap">{{ \Carbon\Carbon::parse($inc->incident_date)->format('d M Y') }}</td>
          <td class="td">
            @php $tc = ['accident'=>'red','breakdown'=>'amber','theft'=>'orange','vandalism'=>'purple','other'=>'slate']; @endphp
            <span class="badge-{{ $tc[$inc->incident_type] ?? 'slate' }} capitalize">{{ $inc->incident_type }}</span>
          </td>
          <td class="td text-sm">
            {{ $inc->vehicle_number ?? '—' }}<br>
            <span class="text-xs text-slate-400">{{ $inc->route_name ?? '' }}</span>
          </td>
          <td class="td text-sm text-slate-600">{{ $inc->location ?? '—' }}</td>
          <td class="td">
            @php $sc = ['minor'=>'green','moderate'=>'amber','major'=>'red']; @endphp
            <span class="badge-{{ $sc[$inc->severity] ?? 'slate' }} capitalize">{{ $inc->severity }}</span>
          </td>
          <td class="td text-sm max-w-xs">
            <p class="truncate max-w-[200px]" title="{{ $inc->description }}">{{ $inc->description }}</p>
            @if($inc->fir_number)<p class="text-xs text-slate-400 mt-0.5">FIR: {{ $inc->fir_number }}</p>@endif
            @if($inc->estimated_loss)<p class="text-xs text-slate-400">Loss: ₹{{ number_format($inc->estimated_loss, 2) }}</p>@endif
          </td>
          <td class="td">
            @php $stc = ['open'=>'red','under_review'=>'amber','resolved'=>'green','closed'=>'slate']; @endphp
            <span class="badge-{{ $stc[$inc->status] ?? 'slate' }} capitalize text-xs">{{ str_replace('_',' ',$inc->status) }}</span>
          </td>
          <td class="td">
            <div x-data="{ open: false }">
              <button @click="open=!open" class="btn btn-xs btn-secondary">Update</button>
              <form x-show="open" x-transition method="POST" action="{{ route('transport.incidents.update-status', $inc->id) }}" class="mt-2 space-y-1 min-w-[180px]">
                @csrf
                <select name="status" class="select text-xs py-1">
                  <option value="open"         @selected($inc->status=='open')>Open</option>
                  <option value="under_review" @selected($inc->status=='under_review')>Under Review</option>
                  <option value="resolved"     @selected($inc->status=='resolved')>Resolved</option>
                  <option value="closed"       @selected($inc->status=='closed')>Closed</option>
                </select>
                <textarea name="resolution_notes" class="input text-xs" rows="2" placeholder="Resolution notes">{{ $inc->resolution_notes }}</textarea>
                <button type="submit" class="btn btn-xs btn-primary">Save</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="td text-center text-slate-400 py-8">No incidents logged.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($incidents->hasPages())<div class="mt-4">{{ $incidents->links() }}</div>@endif
</div>
@endsection
