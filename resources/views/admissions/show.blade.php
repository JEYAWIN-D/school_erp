@extends('layouts.app')

@section('title', 'Enquiry — ' . $enquiry->enquiry_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{ statusModal: false }">

  {{-- Back + Title --}}
  <div class="flex items-center gap-4">
    <a href="{{ route('admissions.index') }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <div class="flex-1">
      <div class="flex items-center gap-3">
        <h1 class="page-title">{{ $enquiry->student_name }}</h1>
        <span class="{{ $enquiry->status_color }}">{{ $enquiry->status_label }}</span>
      </div>
      <p class="page-subtitle font-mono">{{ $enquiry->enquiry_number }} &bull; {{ $enquiry->created_at->format('d M Y') }}</p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <button @click="statusModal = true" class="btn btn-secondary btn-sm">Update Status</button>
      <a href="{{ route('admissions.edit', $enquiry->id) }}" class="btn btn-secondary btn-sm">Edit</a>
      @if(in_array($enquiry->status, ['converted', 'confirmed']))
        <a href="{{ route('students.create', ['enquiry_id' => $enquiry->id]) }}"
           class="btn btn-sm bg-green-600 hover:bg-green-700 text-white flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
          Admit as Student
        </a>
      @endif
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left: Details --}}
    <div class="lg:col-span-2 space-y-6">

      {{-- Student Card --}}
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Student Information</h3>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Name</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $enquiry->student_name }}</dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Gender</dt><dd class="mt-0.5 capitalize">{{ $enquiry->gender ?? '—' }}</dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Date of Birth</dt><dd class="mt-0.5">{{ $enquiry->dob?->format('d M Y') ?? '—' }}</dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Class Applied</dt><dd class="mt-0.5 font-medium">{{ $enquiry->class?->name ?? '—' }}</dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Source</dt><dd class="mt-0.5 capitalize">{{ str_replace('-', ' ', $enquiry->source ?? '—') }}</dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Follow-up Date</dt><dd class="mt-0.5">{{ $enquiry->follow_up_date?->format('d M Y') ?? '—' }}</dd></div>
        </dl>
      </div>

      {{-- Uploaded Documents --}}
      @if($enquiry->documents && count($enquiry->documents))
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-3 pb-2 border-b border-slate-100">Uploaded Documents</h3>
        <ul class="space-y-2">
          @foreach($enquiry->documents as $doc)
          <li class="flex items-center gap-2 text-sm">
            <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="text-indigo-600 hover:underline truncate">
              {{ basename($doc) }}
            </a>
          </li>
          @endforeach
        </ul>
      </div>
      @endif

      {{-- Parent Card --}}
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Parent / Guardian</h3>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Name</dt><dd class="font-medium text-slate-800 mt-0.5">{{ $enquiry->parent_name }}</dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Mobile</dt><dd class="mt-0.5"><a href="tel:{{ $enquiry->parent_mobile }}" class="text-blue-600 font-mono">{{ $enquiry->parent_mobile }}</a></dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Email</dt><dd class="mt-0.5">{{ $enquiry->parent_email ?? '—' }}</dd></div>
          <div class="col-span-2"><dt class="text-slate-400 text-xs uppercase tracking-wide">Address</dt><dd class="mt-0.5">{{ $enquiry->address ?? '—' }}</dd></div>
        </dl>
      </div>

      @if($enquiry->previous_school)
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Previous School</h3>
        <dl class="grid grid-cols-3 gap-x-6 gap-y-3 text-sm">
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">School</dt><dd class="font-medium mt-0.5">{{ $enquiry->previous_school }}</dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">Class</dt><dd class="mt-0.5">{{ $enquiry->previous_class ?? '—' }}</dd></div>
          <div><dt class="text-slate-400 text-xs uppercase tracking-wide">%</dt><dd class="mt-0.5">{{ $enquiry->previous_percentage ?? '—' }}</dd></div>
        </dl>
      </div>
      @endif

      @if($enquiry->notes)
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-2">Notes</h3>
        <p class="text-sm text-slate-600 whitespace-pre-line">{{ $enquiry->notes }}</p>
      </div>
      @endif

    </div>

    {{-- Right: Follow-up Timeline --}}
    <div class="space-y-4">
      <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4">Follow-up History</h3>
        @forelse($enquiry->followUps->sortByDesc('created_at') as $fu)
          <div class="relative pl-6 pb-4 last:pb-0 border-l-2 border-slate-100">
            <div class="absolute -left-[9px] top-0.5 w-4 h-4 rounded-full bg-white border-2 border-blue-400"></div>
            <div class="bg-slate-50 rounded-xl p-3">
              <div class="flex items-center justify-between mb-1">
                <span class="{{ match($fu->status) { 'new' => 'badge-blue', 'follow_up' => 'badge-amber', 'converted' => 'badge-green', 'lost' => 'badge-red', default => 'badge-slate' } }} text-xs">{{ ucfirst(str_replace('_', ' ', $fu->status)) }}</span>
                <span class="text-xs text-slate-400">{{ $fu->created_at->diffForHumans() }}</span>
              </div>
              <p class="text-sm text-slate-700">{{ $fu->notes }}</p>
              @if($fu->next_follow_up_date)
                <p class="text-xs text-slate-400 mt-1">Next: {{ $fu->next_follow_up_date->format('d M Y') }}</p>
              @endif
            </div>
          </div>
        @empty
          <p class="text-sm text-slate-400 text-center py-4">No follow-ups yet.</p>
        @endforelse
      </div>
    </div>

  </div>

  {{-- Status Update Modal --}}
  <div x-show="statusModal" x-cloak class="modal-overlay" @click.self="statusModal = false">
    <div class="modal-box" @click.stop>
      <div class="modal-header">
        <h3 class="font-semibold text-slate-800">Update Enquiry Status</h3>
        <button @click="statusModal = false" class="btn-icon">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
      <form method="POST" action="{{ route('admissions.status', $enquiry->id) }}">
        @csrf @method('PATCH')
        <div class="modal-body space-y-4">
          <div>
            <label class="label">New Status</label>
            <select name="status" class="select" required>
              <option value="new"        @selected($enquiry->status === 'new')>New</option>
              <option value="follow_up"  @selected($enquiry->status === 'follow_up')>Follow Up</option>
              <option value="converted"  @selected($enquiry->status === 'converted')>Converted</option>
              <option value="lost"       @selected($enquiry->status === 'lost')>Lost</option>
            </select>
          </div>
          <div>
            <label class="label">Follow-up Date</label>
            <input type="date" name="follow_up_date" class="input" min="{{ now()->toDateString() }}">
          </div>
          <div>
            <label class="label">Notes</label>
            <textarea name="notes" rows="3" class="input resize-none" placeholder="Add follow-up notes…"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" @click="statusModal = false" class="btn btn-secondary btn-sm">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Update</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Admission Action Panels --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Entrance Test --}}
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Entrance Test</h3>
      @if($enquiry->entrance_test_date)
        <dl class="text-sm space-y-1">
          <div class="flex justify-between"><dt class="text-slate-400">Date</dt><dd>{{ $enquiry->entrance_test_date->format('d M Y') }}{{ $enquiry->entrance_test_time ? ' at '.$enquiry->entrance_test_time : '' }}</dd></div>
          <div class="flex justify-between"><dt class="text-slate-400">Venue</dt><dd>{{ $enquiry->entrance_test_venue ?? '—' }}</dd></div>
          <div class="flex justify-between"><dt class="text-slate-400">Invigilator</dt><dd>{{ $enquiry->entrance_test_invigilator ?? '—' }}</dd></div>
          <div class="flex justify-between"><dt class="text-slate-400">Marks</dt><dd>{{ $enquiry->entrance_test_marks ?? 'Not entered' }}</dd></div>
        </dl>
      @endif
      <form method="POST" action="{{ route('admissions.entrance-test.save', $enquiry->id) }}" class="space-y-2">
        @csrf
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label text-xs">Date</label>
            <input type="date" name="entrance_test_date" class="input text-sm" value="{{ $enquiry->entrance_test_date?->toDateString() }}">
          </div>
          <div>
            <label class="label text-xs">Time</label>
            <input type="time" name="entrance_test_time" class="input text-sm" value="{{ $enquiry->entrance_test_time }}">
          </div>
        </div>
        <div>
          <label class="label text-xs">Venue / Room</label>
          <input type="text" name="entrance_test_venue" class="input text-sm" value="{{ $enquiry->entrance_test_venue }}" placeholder="Hall / Room No.">
        </div>
        <div>
          <label class="label text-xs">Invigilator Name</label>
          <input type="text" name="entrance_test_invigilator" class="input text-sm" value="{{ $enquiry->entrance_test_invigilator }}" placeholder="Teacher / Staff name">
        </div>
        <div>
          <label class="label text-xs">Marks Obtained</label>
          <input type="number" name="entrance_test_marks" step="0.01" min="0" class="input text-sm" value="{{ $enquiry->entrance_test_marks }}" placeholder="After test is conducted">
        </div>
        <button type="submit" class="btn btn-sm btn-secondary w-full">Save Entrance Test</button>
      </form>
      @if($enquiry->entrance_test_date)
        <a href="{{ route('admissions.entrance-test.hallticket', $enquiry->id) }}" target="_blank"
           class="btn btn-sm btn-primary w-full text-center block mt-1">
          Print Admit Card (PDF)
        </a>
      @endif
    </div>

    {{-- Interview --}}
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Interview</h3>
      @if($enquiry->interview_date)
        <dl class="text-sm space-y-1">
          <div class="flex justify-between"><dt class="text-slate-400">Date</dt><dd>{{ $enquiry->interview_date->format('d M Y') }}{{ $enquiry->interview_time ? ' at '.$enquiry->interview_time : '' }}</dd></div>
          <div class="flex justify-between"><dt class="text-slate-400">Interviewer</dt><dd>{{ $enquiry->interview_interviewer ?? '—' }}</dd></div>
          <div class="flex justify-between items-start"><dt class="text-slate-400">Feedback</dt><dd class="text-right max-w-[200px]">{{ $enquiry->interview_feedback ?? '—' }}</dd></div>
        </dl>
      @endif
      <form method="POST" action="{{ route('admissions.interview.save', $enquiry->id) }}" class="space-y-2">
        @csrf
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label text-xs">Interview Date</label>
            <input type="date" name="interview_date" class="input text-sm" value="{{ $enquiry->interview_date?->toDateString() }}">
          </div>
          <div>
            <label class="label text-xs">Time</label>
            <input type="time" name="interview_time" class="input text-sm" value="{{ $enquiry->interview_time }}">
          </div>
        </div>
        <div>
          <label class="label text-xs">Interviewer Name</label>
          <input type="text" name="interview_interviewer" class="input text-sm" value="{{ $enquiry->interview_interviewer }}" placeholder="Staff / Teacher name">
        </div>
        <div>
          <label class="label text-xs">Interviewer Feedback</label>
          <textarea name="interview_feedback" rows="2" class="input text-sm">{{ $enquiry->interview_feedback }}</textarea>
        </div>
        <button type="submit" class="btn btn-sm btn-secondary w-full">Save Interview</button>
      </form>
    </div>

    {{-- Document Verification Checklist --}}
    <div class="card space-y-3" x-data="{ saved: false }">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Document Verification Checklist</h3>
      @php
        $docItems = [
          'birth_certificate'    => 'Birth Certificate',
          'aadhaar'              => 'Aadhaar Card',
          'transfer_certificate' => 'Transfer Certificate (TC)',
          'photo'                => 'Passport-size Photograph',
          'caste_certificate'    => 'Caste Certificate',
          'address_proof'        => 'Address Proof',
          'marksheet'            => 'Previous Class Marksheet',
          'migration_certificate'=> 'Migration Certificate',
          'medical_fitness'      => 'Medical Fitness Certificate',
        ];
        $checked = $enquiry->doc_checklist ?? [];
        $totalDocs = count($docItems);
        $verifiedCount = count(array_intersect(array_keys($docItems), $checked));
      @endphp
      <div class="flex items-center gap-3 mb-2">
        <div class="flex-1 bg-slate-100 rounded-full h-2">
          <div class="bg-indigo-500 h-2 rounded-full transition-all" style="width: {{ $totalDocs > 0 ? round($verifiedCount / $totalDocs * 100) : 0 }}%"></div>
        </div>
        <span class="text-xs font-medium text-slate-600 whitespace-nowrap">{{ $verifiedCount }}/{{ $totalDocs }} verified</span>
      </div>
      <form method="POST" action="{{ route('admissions.doc-checklist.save', $enquiry->id) }}" class="space-y-2">
        @csrf
        <div class="grid grid-cols-1 gap-2">
          @foreach($docItems as $key => $label)
          <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 cursor-pointer">
            <input type="checkbox" name="docs[]" value="{{ $key }}"
                   class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                   {{ in_array($key, $checked) ? 'checked' : '' }}>
            <span class="text-sm {{ in_array($key, $checked) ? 'text-slate-800 font-medium' : 'text-slate-600' }}">
              {{ $label }}
            </span>
            @if(in_array($key, $checked))
            <span class="ml-auto">
              <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </span>
            @endif
          </label>
          @endforeach
        </div>
        <button type="submit" class="btn btn-sm btn-primary w-full mt-1">Update Checklist</button>
      </form>
      @if($verifiedCount === $totalDocs)
        <p class="text-xs text-green-600 font-medium text-center">All documents verified</p>
      @elseif($verifiedCount === 0)
        <p class="text-xs text-amber-600 text-center">No documents verified yet</p>
      @else
        <p class="text-xs text-amber-600 text-center">{{ $totalDocs - $verifiedCount }} document(s) still pending</p>
      @endif
    </div>

    {{-- Flag Missing Documents --}}
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Flag Missing Documents</h3>
      @if(!empty($enquiry->missing_docs))
        <div class="alert-error text-sm">
          <strong>Missing documents flagged:</strong>
          {{ collect($enquiry->missing_docs)->map(fn($k) => $docItems[$k] ?? $k)->implode(', ') }}
          @if($enquiry->docs_flag_note)
            <br>Note: {{ $enquiry->docs_flag_note }}
          @endif
        </div>
      @endif
      <form method="POST" action="{{ route('admissions.flag-missing-docs', $enquiry->id) }}" class="space-y-3">
        @csrf
        <div class="grid grid-cols-1 gap-2">
          @foreach($docItems as $key => $label)
            <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-red-50 cursor-pointer">
              <input type="checkbox" name="missing_docs[]" value="{{ $key }}"
                     class="w-4 h-4 rounded border-slate-300 text-red-600 focus:ring-red-500"
                     {{ in_array($key, $enquiry->missing_docs ?? []) ? 'checked' : '' }}>
              <span class="text-sm text-slate-700">{{ $label }}</span>
              @if(in_array($key, $enquiry->doc_checklist ?? []))
                <span class="ml-auto text-xs text-green-600">Received</span>
              @endif
            </label>
          @endforeach
        </div>
        <div>
          <label class="label text-xs">Note to parent (optional)</label>
          <input type="text" name="docs_flag_note" class="input text-sm" placeholder="e.g. Please bring originals for verification"
                 value="{{ $enquiry->docs_flag_note }}">
        </div>
        <button type="submit" class="btn btn-sm btn-secondary w-full">Update Missing Document Flags</button>
      </form>
    </div>

    {{-- Reject --}}
    @if(!in_array($enquiry->status, ['rejected', 'enrolled']))
    <div class="card space-y-3 border-red-100">
      <h3 class="font-semibold text-red-700 pb-2 border-b border-red-100">Reject Application</h3>
      @if($enquiry->rejection_reason)
        <p class="text-sm text-slate-500">Reason on record: <em>{{ $enquiry->rejection_reason }}</em></p>
      @endif
      <form method="POST" action="{{ route('admissions.reject', $enquiry->id) }}" class="space-y-2"
            onsubmit="return confirm('Reject this application?')">
        @csrf
        <div>
          <label class="label text-xs">Rejection Reason <span class="text-red-500">*</span></label>
          <textarea name="rejection_reason" rows="2" class="input text-sm" required placeholder="Reason for rejection…"></textarea>
        </div>
        <button type="submit" class="btn btn-sm bg-red-600 text-white hover:bg-red-700 w-full">Reject Application</button>
      </form>
    </div>
    @elseif($enquiry->status === 'rejected')
    <div class="card border-red-100">
      <h3 class="font-semibold text-red-700 pb-2 border-b border-red-100">Rejected</h3>
      <p class="text-sm text-slate-600 mt-2">{{ $enquiry->rejection_reason ?? 'No reason recorded.' }}</p>
    </div>
    @endif

    {{-- Waitlist --}}
    @if(!in_array($enquiry->status, ['enrolled', 'rejected', 'waitlisted']))
    <div class="card space-y-3 border-amber-100">
      <h3 class="font-semibold text-amber-700 pb-2 border-b border-amber-100">Add to Waitlist</h3>
      <p class="text-xs text-slate-500">If no seat is available, place this applicant on the waitlist with a position number.</p>
      <form method="POST" action="{{ route('admissions.waitlist.add', $enquiry->id) }}"
            onsubmit="return confirm('Add to waitlist?')">
        @csrf
        <button type="submit" class="btn btn-sm bg-amber-500 text-white hover:bg-amber-600 w-full">Add to Waitlist</button>
      </form>
    </div>
    @elseif($enquiry->status === 'waitlisted')
    <div class="card border-amber-100">
      <h3 class="font-semibold text-amber-700 pb-2 border-b border-amber-100">On Waitlist</h3>
      <p class="text-sm text-slate-600 mt-2">Position: <strong class="text-amber-700">#{{ $enquiry->waitlist_position }}</strong></p>
      <form method="POST" action="{{ route('admissions.waitlist.promote', $enquiry->id) }}" class="mt-3"
            onsubmit="return confirm('Promote to Confirmed?')">
        @csrf
        <button type="submit" class="btn btn-sm bg-green-600 text-white hover:bg-green-700 w-full">Promote to Confirmed</button>
      </form>
    </div>
    @endif

  </div>

</div>
@endsection
