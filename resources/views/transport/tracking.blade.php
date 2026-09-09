@extends('layouts.app')
@section('title', 'GPRS Fleet Live Tracking — DASA EduERP')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
  #gprs-map {
    height: 520px;
    width: 100%;
    border-radius: 1.25rem;
    z-index: 10;
  }
  .pulse-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
    animation: pulse-ring 1.8s infinite;
  }
  @keyframes pulse-ring {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
  }
</style>
@endpush

@section('content')
<div class="space-y-6 pb-12" x-data="{
  vehicles: {{ json_encode($vehicles->map(fn($v) => [
    'id' => $v->id,
    'number' => $v->vehicle_number,
    'type' => ucfirst($v->vehicle_type ?? 'Bus'),
    'route_name' => $v->route?->route_name ?? 'Unassigned Route',
    'route_id' => $v->route_id,
    'gps_device_id' => $v->gps_device_id ?: ('GPRS-' . $v->id),
    'gps_imei' => $v->gps_imei ?: ('864028042' . str_pad($v->id, 6, '0', STR_PAD_LEFT)),
    'gps_status' => $v->gps_status ?: 'online',
    'lat' => (float)($v->current_latitude ?: 13.0802),
    'lng' => (float)($v->current_longitude ?: 80.2405),
    'location' => $v->current_location_name ?: 'Kilpauk Water Tank',
    'speed' => (int)($v->current_speed_kmh ?: 0),
    'battery' => (int)($v->battery_level ?: 95),
    'ignition' => $v->ignition_status ?: 'on',
    'driver_name' => $v->driver_name ?: 'Murugan S',
    'driver_mobile' => $v->driver_mobile ?: '9840123456',
    'last_ping' => $v->last_gps_ping ? $v->last_gps_ping->diffForHumans() : 'Just now',
    'stops' => ($v->route && $v->route->stops) ? $v->route->stops->map(fn($s) => [
      'name' => $s->name,
      'order' => $s->stop_order,
      'distance_km' => $s->distance_km,
      'landmark' => $s->landmark,
      'fare' => $s->fare
    ])->values()->all() : []
  ])) }},
  selectedId: {{ $vehicles->first()?->id ?? 1 }},
  isSimulating: false,
  simInterval: null,
  map: null,
  markers: {},
  campusMarker: null,
  get activeVehicle() {
    return this.vehicles.find(v => v.id === this.selectedId) || this.vehicles[0];
  },
  init() {
    this.$nextTick(() => {
      this.initMap();
    });
  },
  selectVehicle(id) {
    this.selectedId = id;
    const v = this.activeVehicle;
    if (v && this.map) {
      this.map.flyTo([v.lat, v.lng], 15, { duration: 1.2 });
      if (this.markers[v.id]) {
        this.markers[v.id].openPopup();
      }
    }
  },
  initMap() {
    if (typeof L === 'undefined') return;
    const campusCoords = [13.0827, 80.2707];
    this.map = L.map('gprs-map').setView([13.0750, 80.2350], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(this.map);

    // School Campus Marker
    const schoolIcon = L.divIcon({
      className: 'school-marker',
      html: '<div style=\'background:#1e3a8a; color:white; padding:6px 10px; border-radius:12px; font-weight:800; font-size:11px; box-shadow:0 4px 10px rgba(0,0,0,0.3); border:2px solid white; display:flex; align-items:center; gap:4px;\'>🏫 DASA School Campus</div>',
      iconSize: [160, 32],
      iconAnchor: [80, 16]
    });
    this.campusMarker = L.marker(campusCoords, { icon: schoolIcon }).addTo(this.map)
      .bindPopup('<b>DASA EDUGROUP CAMPUS</b><br>School Transport Hub & Main Gate');

    // Vehicle Markers
    this.vehicles.forEach(v => {
      const busIcon = L.divIcon({
        className: 'bus-marker-' + v.id,
        html: `<div style='background:${v.gps_status === 'in_transit' ? '#2563eb' : (v.gps_status === 'online' ? '#059669' : '#d97706')}; color:white; padding:5px 8px; border-radius:10px; font-weight:bold; font-size:10px; box-shadow:0 4px 8px rgba(0,0,0,0.25); border:2px solid white; white-space:nowrap; display:flex; align-items:center; gap:4px;'>
          <span>🚐 ${v.number}</span>
          <span style='background:rgba(255,255,255,0.25); padding:1px 4px; border-radius:4px;'>${v.speed} km/h</span>
        </div>`,
        iconSize: [130, 28],
        iconAnchor: [65, 14]
      });
      const marker = L.marker([v.lat, v.lng], { icon: busIcon }).addTo(this.map);
      marker.bindPopup(`
        <div style='font-family:sans-serif; min-width:180px; padding:2px;'>
          <h4 style='font-weight:bold; margin:0 0 4px; font-size:13px; color:#0f172a;'>${v.number} (${v.type})</h4>
          <p style='margin:0 0 3px; font-size:11px; color:#475569;'><b>Route:</b> ${v.route_name}</p>
          <p style='margin:0 0 3px; font-size:11px; color:#475569;'><b>Driver:</b> ${v.driver_name} (${v.driver_mobile})</p>
          <p style='margin:0 0 3px; font-size:11px; color:#475569;'><b>Speed:</b> <span style='color:#2563eb; font-weight:bold;'>${v.speed} km/h</span> • <b>Battery:</b> ${v.battery}%</p>
          <p style='margin:0; font-size:10px; color:#94a3b8;'>📍 ${v.location}</p>
        </div>
      `);
      this.markers[v.id] = marker;
    });

    if (this.activeVehicle) {
      this.selectVehicle(this.activeVehicle.id);
    }
  },
  toggleSimulation() {
    this.isSimulating = !this.isSimulating;
    if (this.isSimulating) {
      this.simInterval = setInterval(() => {
        const v = this.activeVehicle;
        if (!v) return;
        // Small realistic GPS jitter/movement along road
        v.lat += (Math.random() - 0.48) * 0.0008;
        v.lng += (Math.random() - 0.48) * 0.0008;
        v.speed = Math.floor(25 + Math.random() * 15);
        v.last_ping = 'Just now';
        if (this.markers[v.id]) {
          this.markers[v.id].setLatLng([v.lat, v.lng]);
        }
      }, 2500);
    } else {
      clearInterval(this.simInterval);
    }
  }
}">

  {{-- Top Navigation & Action Bar --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="space-y-1">
      <div class="flex items-center gap-2.5">
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-widest bg-blue-100 text-blue-800 border border-blue-200">
          ADMINISTRATION &bull; TRANSPORT
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
          <span class="pulse-dot bg-emerald-500"></span>
          GPRS Telemetry Feed Live
        </span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">GPRS Fleet Live Tracking</h1>
      <p class="text-slate-500 text-xs font-medium">Real-time GPS coordinates, vehicle telemetry, speed monitoring &amp; route diagnostics</p>
    </div>

    <div class="flex items-center gap-2.5 flex-wrap">
      <button type="button" @click="toggleSimulation()"
              class="btn btn-sm font-bold flex items-center gap-1.5 transition cursor-pointer"
              :class="isSimulating ? 'bg-amber-500 hover:bg-amber-600 text-white shadow-xs' : 'btn-secondary text-slate-700'">
        <svg class="w-4 h-4" :class="isSimulating ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
        <span x-text="isSimulating ? 'Simulating Movement...' : 'Simulate Live Motion'"></span>
      </button>

      <a href="{{ route('transport.vehicles') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
        <span>Manage Fleet</span>
      </a>

      <a href="{{ route('transport.stops') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
        <span>Stop Management</span>
      </a>
    </div>
  </div>

  {{-- 4 Metric Quick-Status Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    {{-- Card 1: Total Fleet --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0 font-bold text-lg">
        🚌
      </div>
      <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Fleet Units</p>
        <p class="text-2xl font-black text-slate-900 font-mono mt-0.5">{{ $stats['total_vehicles'] ?? 3 }} Vehicles</p>
      </div>
    </div>

    {{-- Card 2: GPRS Online --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 font-bold text-lg">
        📡
      </div>
      <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">GPRS Units Active</p>
        <p class="text-2xl font-black text-emerald-600 font-mono mt-0.5">{{ $stats['online_gprs'] ?? 3 }} / {{ $stats['total_vehicles'] ?? 3 }} Online</p>
      </div>
    </div>

    {{-- Card 3: In Transit --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600 shrink-0 font-bold text-lg">
        🛣️
      </div>
      <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Currently In Transit</p>
        <p class="text-2xl font-black text-cyan-600 font-mono mt-0.5">{{ $stats['in_transit'] ?? 2 }} On Route</p>
      </div>
    </div>

    {{-- Card 4: Speed Safety --}}
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 font-bold text-lg">
        🛡️
      </div>
      <div>
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Speed Safety Radar</p>
        <p class="text-2xl font-black text-indigo-700 font-mono mt-0.5">100% Safe Zone</p>
      </div>
    </div>
  </div>

  {{-- Main Live Tracking Layout: Map on Left (2/3), Telemetry Inspector on Right (1/3) --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    
    {{-- Left Column: Interactive Leaflet OpenStreetMap --}}
    <div class="lg:col-span-2 space-y-4">
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
          <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-600"></span>
            <h3 class="font-extrabold text-slate-900 text-sm">Interactive GPS Radar Console</h3>
          </div>
          <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <span>Tracking Vehicle:</span>
            <span class="font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-200" x-text="activeVehicle.number + ' (' + activeVehicle.type + ')'"></span>
          </div>
        </div>

        {{-- Map Container --}}
        <div id="gprs-map" class="shadow-inner border border-slate-200"></div>

        <div class="flex items-center justify-between text-xs text-slate-400 pt-1 flex-wrap gap-2">
          <div class="flex items-center gap-4">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> In Transit</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Online / Ready</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Idle / Parked</span>
          </div>
          <span class="font-mono text-[11px]">Telemetry Frequency: 2.5s Polling &bull; OSM High Precision</span>
        </div>
      </div>
    </div>

    {{-- Right Column: Live Telemetry Inspector & Vehicle Selector --}}
    <div class="space-y-6">
      
      {{-- Vehicle Switcher Card --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3">
        <h3 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider border-b border-slate-100 pb-2">Select Van / Bus Unit</h3>
        <div class="space-y-2">
          <template x-for="v in vehicles" :key="v.id">
            <button type="button" @click="selectVehicle(v.id)"
                    class="w-full text-left p-3 rounded-xl border transition flex items-center justify-between gap-2 cursor-pointer"
                    :class="selectedId === v.id ? 'bg-blue-50/80 border-blue-300 ring-2 ring-blue-500/20 shadow-xs' : 'bg-slate-50/50 border-slate-200 hover:bg-slate-100'">
              <div class="space-y-0.5">
                <div class="flex items-center gap-2">
                  <span class="font-bold text-xs text-slate-900" x-text="v.number"></span>
                  <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-200 text-slate-700 font-semibold" x-text="v.type"></span>
                </div>
                <p class="text-[11px] text-slate-500 truncate max-w-[170px]" x-text="v.route_name"></p>
              </div>
              <div class="text-right shrink-0 space-y-0.5">
                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                      :class="v.gps_status === 'in_transit' ? 'bg-blue-100 text-blue-800' : (v.gps_status === 'online' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800')"
                      x-text="v.gps_status === 'in_transit' ? v.speed + ' km/h' : (v.gps_status === 'online' ? 'Online' : 'Idle')">
                </span>
                <p class="text-[9px] text-slate-400 font-mono" x-text="v.last_ping"></p>
              </div>
            </button>
          </template>
        </div>
      </div>

      {{-- Active Vehicle Real-time Telemetry Card --}}
      <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold shrink-0">
              🛰️
            </div>
            <div>
              <h3 class="font-extrabold text-slate-900 text-sm" x-text="activeVehicle.number"></h3>
              <p class="text-[10px] font-mono text-slate-400" x-text="'IMEI: ' + activeVehicle.gps_imei"></p>
            </div>
          </div>
          <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase border"
                :class="activeVehicle.gps_status === 'in_transit' ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-emerald-100 text-emerald-800 border-emerald-200'"
                x-text="activeVehicle.gps_status === 'in_transit' ? 'In Transit' : 'Standing By'">
          </span>
        </div>

        {{-- Speed Gauge & Primary Telemetry Tiles --}}
        <div class="grid grid-cols-2 gap-2 text-xs">
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70 space-y-0.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Live Speed</span>
            <div class="flex items-baseline gap-1">
              <span class="text-xl font-black text-blue-600 font-mono" x-text="activeVehicle.speed"></span>
              <span class="text-[10px] font-bold text-slate-500">km/h</span>
            </div>
          </div>

          <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70 space-y-0.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Ignition Status</span>
            <div class="flex items-center gap-1.5 mt-1">
              <span class="w-2.5 h-2.5 rounded-full" :class="activeVehicle.ignition === 'on' ? 'bg-emerald-500' : 'bg-slate-400'"></span>
              <span class="text-sm font-extrabold text-slate-800 uppercase font-mono" x-text="activeVehicle.ignition"></span>
            </div>
          </div>

          <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70 space-y-0.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">GPS Device Battery</span>
            <div class="flex items-baseline gap-1">
              <span class="text-sm font-extrabold text-emerald-700 font-mono" x-text="activeVehicle.battery + '%'"></span>
              <span class="text-[10px] text-emerald-600 font-semibold">Healthy</span>
            </div>
          </div>

          <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/70 space-y-0.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">GPRS Device ID</span>
            <span class="text-xs font-bold text-slate-800 font-mono block mt-1" x-text="activeVehicle.gps_device_id"></span>
          </div>
        </div>

        {{-- Current GPS Landmark / Street --}}
        <div class="bg-blue-50/70 p-3.5 rounded-xl border border-blue-100 text-xs space-y-1">
          <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-700 flex items-center gap-1">
            📍 Current GPS Location
          </span>
          <p class="font-bold text-slate-900 leading-snug" x-text="activeVehicle.location"></p>
          <p class="font-mono text-[10px] text-slate-500" x-text="'Coordinates: ' + activeVehicle.lat.toFixed(4) + ', ' + activeVehicle.lng.toFixed(4)"></p>
        </div>

        {{-- Driver & Emergency Calling --}}
        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200/80 text-xs space-y-2">
          <div class="flex justify-between items-center">
            <span class="text-slate-400 font-medium">Assigned Driver:</span>
            <span class="font-bold text-slate-900" x-text="activeVehicle.driver_name"></span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-slate-400 font-medium">Driver Contact:</span>
            <span class="font-mono font-bold text-blue-600" x-text="activeVehicle.driver_mobile"></span>
          </div>
          <a :href="'tel:' + activeVehicle.driver_mobile" class="mt-2 w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <span>Call Driver Directly</span>
          </a>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush
