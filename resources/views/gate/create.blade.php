@extends('layouts.app')
@section('title', 'Log Visitor')
@section('content')
<div class="max-w-2xl space-y-6" x-data="gateForm()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Log Visitor</h1>
    <a href="{{ route('gate.index') }}" class="btn-sm btn-secondary">← Gate</a>
  </div>

  @if($errors->any()) <div class="alert-danger">@foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach</div> @endif

  <form method="POST" action="{{ route('gate.store') }}" class="card space-y-4" @submit="capturePhoto">
    @csrf
    <input type="hidden" name="photo_data" x-ref="photoData">

    <div class="grid grid-cols-2 gap-4">
      <div class="col-span-2">
        <label class="label">Visitor Name <span class="text-red-500">*</span></label>
        <input type="text" name="visitor_name" class="input" required autofocus>
      </div>
      <div>
        <label class="label">Phone</label>
        <input type="tel" name="visitor_phone" class="input">
      </div>
      <div>
        <label class="label">Vehicle Number</label>
        <input type="text" name="vehicle_number" class="input" placeholder="e.g. MH12AB1234">
      </div>
      <div>
        <label class="label">ID Type</label>
        <select name="visitor_id_type" class="select">
          <option value="">Select</option>
          <option>Aadhaar</option><option>PAN</option><option>Driving Licence</option><option>Passport</option><option>Voter ID</option>
        </select>
      </div>
      <div>
        <label class="label">ID Number</label>
        <input type="text" name="visitor_id_number" class="input">
      </div>
      <div class="col-span-2">
        <label class="label">Purpose <span class="text-red-500">*</span></label>
        <input type="text" name="purpose" class="input" required>
      </div>
      <div>
        <label class="label">Whom to Meet</label>
        <input type="text" name="whom_to_meet" class="input" list="staff-list" autocomplete="off" placeholder="Search staff…">
        <datalist id="staff-list">
          @foreach($staff as $s)
          <option value="{{ $s->first_name }} {{ $s->last_name }}{{ $s->designation ? ' ('.$s->designation.')' : '' }}">
          @endforeach
        </datalist>
      </div>
      <div>
        <label class="label">Department</label>
        <input type="text" name="department" class="input">
      </div>
      <div class="col-span-2">
        <label class="label">Remarks</label>
        <textarea name="remarks" rows="2" class="input"></textarea>
      </div>
    </div>

    {{-- Webcam --}}
    <div>
      <label class="label">Visitor Photo (optional)</label>
      <div class="flex gap-4 items-start">
        <video x-ref="video" class="rounded-xl w-40 h-32 object-cover bg-slate-100" autoplay playsinline x-show="streaming"></video>
        <canvas x-ref="canvas" class="rounded-xl w-40 h-32 object-cover hidden"></canvas>
        <img x-ref="preview" class="rounded-xl w-40 h-32 object-cover" x-show="captured" alt="Preview">
        <div class="space-y-2">
          <button type="button" @click="startCamera" x-show="!streaming && !captured" class="btn-sm btn-secondary">Start Camera</button>
          <button type="button" @click="snap" x-show="streaming" class="btn-sm btn-primary">📸 Capture</button>
          <button type="button" @click="retake" x-show="captured" class="btn-sm btn-secondary">Retake</button>
        </div>
      </div>
    </div>

    <div class="flex gap-2">
      <button type="submit" class="btn-primary">Log Visitor &amp; Print Pass</button>
      <a href="{{ route('gate.index') }}" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
function gateForm() {
  return {
    streaming: false, captured: false, stream: null,
    async startCamera() {
      try {
        this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        this.$refs.video.srcObject = this.stream;
        this.streaming = true;
      } catch(e) { alert('Camera not available: ' + e.message); }
    },
    snap() {
      const v = this.$refs.video, c = this.$refs.canvas;
      c.width = v.videoWidth; c.height = v.videoHeight;
      c.getContext('2d').drawImage(v, 0, 0);
      const data = c.toDataURL('image/jpeg', 0.8);
      this.$refs.photoData.value = data;
      this.$refs.preview.src = data;
      this.captured = true; this.streaming = false;
      if (this.stream) this.stream.getTracks().forEach(t => t.stop());
    },
    retake() { this.captured = false; this.$refs.photoData.value = ''; this.startCamera(); },
    capturePhoto() { /* already set */ },
  };
}
</script>
@endpush
@endsection
