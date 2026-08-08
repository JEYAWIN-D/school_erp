@extends('layouts.app')
@section('title','Hostel Visitors')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Hostel Visitor Management</h1>
    <div class="flex gap-2">
      <a href="{{ route('hostel.visiting-hours') }}" class="btn-sm btn-secondary">Visiting Hours Config</a>
      <button x-data @click="$dispatch('open-modal','add-visitor')" class="btn btn-primary btn-sm">+ Log Visitor</button>
    </div>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
  @if($errors->any())<div class="alert-error">{{ $errors->first() }}</div>@endif

  {{-- Visiting hours status --}}
  @php $allowedNow = \App\Models\HostelVisitingHours::isCurrentlyAllowed(); @endphp
  @if(!$allowedNow)
  <div class="alert-error">
    <strong>Outside visiting hours:</strong> New visitors cannot be logged at this time.
  </div>
  @else
  <div class="alert-success">Visiting hours are currently active. Visitors may be logged.</div>
  @endif

  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <input type="date" name="date" value="{{ request('date',today()->toDateString()) }}" class="input w-36">
    <select name="hostel_id" class="select w-44">
      <option value="">All Hostels</option>
      @foreach($hostels as $h)<option value="{{ $h->id }}" @selected(request('hostel_id')==$h->id)>{{ $h->name }}</option>@endforeach
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
  </div></form>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Photo','Visitor','Relation','Student','Hostel','In Time','Pass Until','Status','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($visitors as $v)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3">
            @if($v->visitor_photo)
              <img src="{{ Storage::url($v->visitor_photo) }}" class="w-10 h-12 object-cover rounded border border-slate-200">
            @else
              <div class="w-10 h-12 bg-slate-100 rounded flex items-center justify-center text-slate-400 text-xs">—</div>
            @endif
          </td>
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800 text-sm">{{ $v->visitor_name }}</p>
            <p class="text-xs text-slate-400">{{ $v->visitor_phone ?? $v->visitor_mobile }}</p>
            @if($v->id_type && $v->id_number)
              <p class="text-xs text-slate-400">{{ $v->id_type }}: {{ $v->id_number }}</p>
            @endif
          </td>
          <td class="px-4 py-3 text-slate-500 text-xs capitalize">{{ $v->relation }}</td>
          <td class="px-4 py-3 text-slate-700 text-sm">{{ $v->student?->full_name }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $v->hostel?->name }}</td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $v->entry_time?->format('h:i A') }}</td>
          <td class="px-4 py-3 text-xs {{ $v->isPassValid() ? 'text-green-700 font-medium' : 'text-red-500' }}">
            {{ $v->pass_valid_until?->format('h:i A') }}
          </td>
          <td class="px-4 py-3">
            @if($v->exit_time)
              <span class="badge-green text-xs">Out {{ $v->exit_time->format('h:i A') }}</span>
            @elseif($v->isPassValid())
              <span class="badge-indigo text-xs">Active</span>
            @else
              <span class="badge-red text-xs">Expired Pass</span>
            @endif
          </td>
          <td class="px-4 py-3">
            <div class="flex gap-1">
              <a href="{{ route('hostel.visitors.pass', $v->id) }}" class="btn-xs btn-secondary">Pass</a>
              @if(!$v->exit_time)
              <form method="POST" action="{{ route('hostel.visitors.checkout', $v->id) }}" class="inline">@csrf
                <button type="submit" class="btn-xs btn-secondary">Check Out</button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No visitors logged today.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($visitors->hasPages())<div class="px-4 pb-3">{{ $visitors->links() }}</div>@endif
  </div>
</div>

{{-- Add Visitor Modal with webcam photo --}}
<div x-data="visitorForm()" x-init="init()"
  x-on:open-modal.window="show=($event.detail==='add-visitor')"
  x-show="show" style="display:none"
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-xl max-h-screen overflow-y-auto" @click.stop>
    <h3 class="font-semibold text-slate-700 mb-4">Log Visitor</h3>
    <form method="POST" action="{{ route('hostel.visitors.store') }}" class="space-y-3">
      @csrf

      {{-- Webcam photo capture --}}
      <div class="bg-slate-50 rounded-lg p-3 space-y-2">
        <div class="flex items-center justify-between">
          <label class="label mb-0 text-xs">Visitor Photo (optional)</label>
          <button type="button" @click="toggleCamera()" class="btn-xs btn-secondary" x-text="cameraOn ? 'Stop Camera' : 'Open Camera'"></button>
        </div>
        <div x-show="cameraOn" class="space-y-2">
          <video id="visitor-cam" autoplay playsinline class="w-full rounded max-h-36 bg-black"></video>
          <button type="button" @click="capturePhoto()" class="btn-xs btn-primary">Capture Photo</button>
        </div>
        <div x-show="photoPreview" class="flex items-center gap-3">
          <img :src="photoPreview" class="w-16 h-20 object-cover rounded border border-slate-200">
          <button type="button" @click="photoPreview='';photoData=''" class="btn-xs btn-secondary">Remove</button>
        </div>
        <input type="hidden" name="photo_data" x-model="photoData">
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Visitor Name <span class="text-red-500">*</span></label><input type="text" name="visitor_name" class="input" required></div>
        <div><label class="label">Phone</label><input type="tel" name="visitor_phone" class="input"></div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">ID Type</label>
          <select name="id_type" class="select">
            <option value="">Select</option>
            @foreach(['Aadhaar'=>'Aadhaar','PAN'=>'PAN','Voter ID'=>'Voter ID','Passport'=>'Passport','Driving Licence'=>'Driving Licence','Other'=>'Other'] as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div><label class="label">ID Number</label><input type="text" name="id_number" class="input"></div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Relation</label>
          <select name="relation" class="select">
            @foreach(['parent'=>'Parent','guardian'=>'Guardian','sibling'=>'Sibling','relative'=>'Relative','other'=>'Other'] as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div><label class="label">Student <span class="text-red-500">*</span></label>
          <select name="student_id" class="select" required>
            <option value="">Select</option>
            @foreach($students as $s)<option value="{{ $s->id }}">{{ $s->full_name }}</option>@endforeach
          </select>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Hostel <span class="text-red-500">*</span></label>
          <select name="hostel_id" class="select" required>
            @foreach($hostels as $h)<option value="{{ $h->id }}">{{ $h->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="label">Pass Valid (hours)</label>
          <select name="pass_valid_hours" class="select">
            @foreach([1=>'1 hour',2=>'2 hours',3=>'3 hours',4=>'4 hours',6=>'6 hours'] as $v=>$l)
            <option value="{{ $v }}" {{ $v==2?'selected':'' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Purpose</label><input type="text" name="purpose" class="input"></div>
        <div><label class="label">Destination/Room</label><input type="text" name="destination" class="input"></div>
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Log Entry & Get Pass</button>
        <button type="button" @click="show=false;stopCamera()" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function visitorForm() {
  return {
    show: false,
    cameraOn: false,
    photoPreview: '',
    photoData: '',
    stream: null,
    init() {},
    async toggleCamera() {
      if (this.cameraOn) { this.stopCamera(); }
      else {
        try {
          this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
          document.getElementById('visitor-cam').srcObject = this.stream;
          this.cameraOn = true;
        } catch(e) { alert('Camera access denied: ' + e.message); }
      }
    },
    stopCamera() {
      if (this.stream) { this.stream.getTracks().forEach(t => t.stop()); this.stream = null; }
      this.cameraOn = false;
    },
    capturePhoto() {
      const video = document.getElementById('visitor-cam');
      const canvas = document.createElement('canvas');
      canvas.width = 240; canvas.height = 320;
      canvas.getContext('2d').drawImage(video, 0, 0, 240, 320);
      this.photoData = canvas.toDataURL('image/jpeg', 0.85);
      this.photoPreview = this.photoData;
      this.stopCamera();
    },
  };
}
</script>
@endpush
@endsection
