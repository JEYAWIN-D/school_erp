@extends('layouts.app')

@section('title', 'Admission Approvals Queue — DASA EduERP')

@section('content')
<div class="space-y-6" x-data="{
  activeTab: '{{ $tab }}',
  rejectModalOpen: false,
  rejectStudentId: null,
  rejectStudentName: '',
  openRejectModal(id, name) {
    this.rejectStudentId = id;
    this.rejectStudentName = name;
    this.rejectModalOpen = true;
  }
}">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2.5">
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Admission Approvals Queue</h1>
        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
          2-Tier Governance
        </span>
      </div>
      <p class="text-xs text-slate-500 font-medium mt-1">
        Workflow: Admin Desk Submission &rarr; <strong>Principal Review</strong> &rarr; <strong>Admin Final Confirmation</strong> &rarr; Active Student Register
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admissions.create') }}" class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>New Admission</span>
      </a>
    </div>
  </div>

  {{-- ── Pipeline KPI Stat Cards ────────────────────────────────────── --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    
    {{-- Stage 1: Principal Review --}}
    <a href="{{ route('admissions.approvals', ['tab' => 'principal']) }}"
       class="p-4 rounded-3xl border transition block shadow-2xs {{ $tab === 'principal' ? 'bg-amber-50 border-amber-300 ring-2 ring-amber-400/40' : 'bg-white border-slate-200/80 hover:bg-slate-50' }}">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-amber-700 uppercase tracking-wider">Principal Review</span>
        <span class="w-2.5 h-2.5 rounded-full {{ $pendingPrincipalCount > 0 ? 'bg-amber-500 animate-pulse' : 'bg-slate-300' }}"></span>
      </div>
      <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 font-mono">{{ $pendingPrincipalCount }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Awaiting Principal approval</p>
    </a>

    {{-- Stage 2: Admin Confirmation --}}
    <a href="{{ route('admissions.approvals', ['tab' => 'admin']) }}"
       class="p-4 rounded-3xl border transition block shadow-2xs {{ $tab === 'admin' ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-400/40' : 'bg-white border-slate-200/80 hover:bg-slate-50' }}">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">Admin Confirmation</span>
        <span class="w-2.5 h-2.5 rounded-full {{ $pendingAdminCount > 0 ? 'bg-blue-500 animate-pulse' : 'bg-slate-300' }}"></span>
      </div>
      <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 font-mono">{{ $pendingAdminCount }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Principal approved, pending final confirm</p>
    </a>

    {{-- Stage 3: Confirmed & Active --}}
    <a href="{{ route('admissions.approvals', ['tab' => 'active']) }}"
       class="p-4 rounded-3xl border transition block shadow-2xs {{ $tab === 'active' ? 'bg-emerald-50 border-emerald-300 ring-2 ring-emerald-400/40' : 'bg-white border-slate-200/80 hover:bg-slate-50' }}">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Confirmed &amp; Active</span>
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
      </div>
      <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 font-mono">{{ $activeCount }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Enrolled on general register</p>
    </a>

    {{-- Stage 4: Rejected --}}
    <a href="{{ route('admissions.approvals', ['tab' => 'rejected']) }}"
       class="p-4 rounded-3xl border transition block shadow-2xs {{ $tab === 'rejected' ? 'bg-rose-50 border-rose-300 ring-2 ring-rose-400/40' : 'bg-white border-slate-200/80 hover:bg-slate-50' }}">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">Rejected</span>
        <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
      </div>
      <p class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 font-mono">{{ $rejectedCount }}</p>
      <p class="text-[11px] text-slate-500 mt-0.5">Declined applications</p>
    </a>

  </div>

  {{-- ── Filter Navigation Tabs ──────────────────────────────────────── --}}
  <div class="border-b border-slate-200 flex items-center gap-2 overflow-x-auto pb-px">
    <a href="{{ route('admissions.approvals', ['tab' => 'principal']) }}"
       class="py-3 px-4 text-xs font-bold border-b-2 transition whitespace-nowrap flex items-center gap-2 {{ $tab === 'principal' ? 'border-amber-600 text-amber-700 bg-amber-50/50' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
      <span>Awaiting Principal Approval</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $tab === 'principal' ? 'bg-amber-200 text-amber-900' : 'bg-slate-200 text-slate-700' }}">{{ $pendingPrincipalCount }}</span>
    </a>

    <a href="{{ route('admissions.approvals', ['tab' => 'admin']) }}"
       class="py-3 px-4 text-xs font-bold border-b-2 transition whitespace-nowrap flex items-center gap-2 {{ $tab === 'admin' ? 'border-blue-600 text-blue-700 bg-blue-50/50' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
      <span>Awaiting Admin Confirmation</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $tab === 'admin' ? 'bg-blue-200 text-blue-900' : 'bg-slate-200 text-slate-700' }}">{{ $pendingAdminCount }}</span>
    </a>

    <a href="{{ route('admissions.approvals', ['tab' => 'active']) }}"
       class="py-3 px-4 text-xs font-bold border-b-2 transition whitespace-nowrap flex items-center gap-2 {{ $tab === 'active' ? 'border-emerald-600 text-emerald-700 bg-emerald-50/50' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
      <span>Confirmed &amp; Active</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $tab === 'active' ? 'bg-emerald-200 text-emerald-900' : 'bg-slate-200 text-slate-700' }}">{{ $activeCount }}</span>
    </a>

    <a href="{{ route('admissions.approvals', ['tab' => 'rejected']) }}"
       class="py-3 px-4 text-xs font-bold border-b-2 transition whitespace-nowrap flex items-center gap-2 {{ $tab === 'rejected' ? 'border-rose-600 text-rose-700 bg-rose-50/50' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
      <span>Rejected</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold {{ $tab === 'rejected' ? 'bg-rose-200 text-rose-900' : 'bg-slate-200 text-slate-700' }}">{{ $rejectedCount }}</span>
    </a>
  </div>

  {{-- ── Admission Applications Ledger Table / Cards ──────────────────── --}}
  @if($students->isEmpty())
    <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 shadow-xs space-y-3">
      <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-2xl">
        📋
      </div>
      <h3 class="text-base font-bold text-slate-800">No Applications in this Queue</h3>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">
        @if($tab === 'principal')
          All student admissions have been reviewed by the Principal.
        @elseif($tab === 'admin')
          No admissions are currently pending final Admin confirmation.
        @else
          No records found in this view.
        @endif
      </p>
    </div>
  @else
    <div class="space-y-4">
      @foreach($students as $s)
      <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden transition hover:border-blue-300">
        
        <div class="p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
          
          {{-- Student & Class Identity --}}
          <div class="flex items-start gap-4 min-w-0 lg:w-1/3">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 border-2 border-slate-200 overflow-hidden shrink-0 shadow-xs">
              @if($s->photo)
                <img src="{{ asset('storage/' . $s->photo) }}" class="w-full h-full object-cover" alt="{{ $s->full_name }}">
              @else
                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-600 font-bold text-xl">
                  {{ substr($s->first_name, 0, 1) }}
                </div>
              @endif
            </div>
            <div class="min-w-0 space-y-1">
              <div class="flex items-center gap-2">
                <h3 class="text-base font-black text-slate-900 truncate leading-snug">{{ $s->full_name }}</h3>
                @if($s->blood_group)
                  <span class="px-1.5 py-0.2 rounded text-[10px] font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200">{{ $s->blood_group }}</span>
                @endif
              </div>
              <p class="text-xs font-mono font-bold text-blue-700">Adm No: {{ $s->admission_no }}</p>
              <p class="text-xs text-slate-600 font-medium">
                Class {{ $s->currentEnrollment?->class?->name ?? '—' }} ({{ $s->currentEnrollment?->section?->name ?? 'A' }}) &bull; Roll: {{ $s->roll_number ?? 'Auto' }}
              </p>
              <p class="text-[11px] text-slate-400">Applied on: {{ $s->created_at?->format('d M Y, h:i A') }}</p>
            </div>
          </div>

          {{-- Parents & Documents Summary --}}
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs lg:w-5/12 border-t lg:border-t-0 lg:border-l lg:border-r border-slate-100 pt-3 lg:pt-0 lg:px-5">
            {{-- Father --}}
            <div>
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Father</span>
              <p class="font-bold text-slate-800 truncate">{{ $s->father_name ?? $s->parent_name }}</p>
              <p class="text-[11px] font-mono text-slate-500">{{ $s->father_mobile ?? $s->parent_mobile }}</p>
            </div>

            {{-- Mother --}}
            <div>
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Mother</span>
              <p class="font-bold text-slate-800 truncate">{{ $s->mother_name ?? '—' }}</p>
              <p class="text-[11px] font-mono text-slate-500">{{ $s->mother_mobile ?? '—' }}</p>
            </div>

            {{-- Documents Status --}}
            <div>
              <span class="text-[10px] uppercase font-bold text-slate-400 block">Documents</span>
              @php
                $docCount = $s->documents->count();
              @endphp
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold {{ $docCount >= 3 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                📁 {{ $docCount }} Uploaded
              </span>
              <a href="{{ route('students.visitor-card', $s->id) }}" target="_blank" class="block text-[11px] text-indigo-600 font-bold hover:underline mt-0.5">
                Visitor Card Pass &rarr;
              </a>
            </div>
          </div>

          {{-- Approval Action Buttons --}}
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 lg:w-1/4 justify-end">
            
            @if($s->status === 'pending_principal')
              {{-- Principal Action --}}
              @if(auth()->user()->hasRole('principal') || auth()->user()->hasRole('super_admin') || auth()->user()->can('approve admissions'))
                <form method="POST" action="{{ route('admissions.principal-approve', $s->id) }}" class="inline">
                  @csrf
                  <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Approve as Principal
                  </button>
                </form>

                <button type="button" @click="openRejectModal({{ $s->id }}, '{{ addslashes($s->full_name) }}')"
                        class="px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition cursor-pointer">
                  Reject
                </button>
              @else
                <div class="text-right space-y-1">
                  <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 inline-block">
                    Awaiting Principal Approval
                  </span>
                  <p class="text-[10px] text-slate-400">Assigned: principal@schoolerp.in</p>
                </div>
              @endif

            @elseif($s->status === 'principal_approved')
              {{-- Admin Final Confirmation Action --}}
              @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super_admin') || auth()->user()->can('approve admissions'))
                <form method="POST" action="{{ route('admissions.admin-confirm', $s->id) }}" class="inline">
                  @csrf
                  <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Confirm &amp; Finalize Admission
                  </button>
                </form>

                <button type="button" @click="openRejectModal({{ $s->id }}, '{{ addslashes($s->full_name) }}')"
                        class="px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition cursor-pointer">
                  Reject
                </button>
              @else
                <div class="text-right space-y-1">
                  <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 inline-block">
                    Principal Approved &bull; Pending Admin
                  </span>
                  <p class="text-[10px] text-slate-400">By: {{ $s->principalApprover?->name ?? 'Principal' }}</p>
                </div>
              @endif

            @elseif($s->status === 'active')
              {{-- Active Student Shortcuts --}}
              <div class="flex items-center gap-2">
                <a href="{{ route('students.show', $s->id) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition border border-slate-200">
                  Profile
                </a>
                <a href="{{ route('students.id-card.single', $s->id) }}" class="px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs transition border border-indigo-200">
                  ID Card
                </a>
                <a href="{{ route('students.visitor-card', $s->id) }}" class="px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs transition border border-emerald-200">
                  Visitor Pass
                </a>
              </div>

            @elseif($s->status === 'rejected')
              <div class="text-right text-xs">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200 inline-block">
                  Rejected
                </span>
                <p class="text-[11px] text-slate-500 mt-1">Reason: {{ $s->rejection_reason ?? 'Criteria unfulfilled' }}</p>
              </div>
            @endif

          </div>

        </div>

      </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    <div class="pt-4">
      {{ $students->appends(['tab' => $tab])->links() }}
    </div>
  @endif

  {{-- ── Rejection Reason Modal ──────────────────────────────────────── --}}
  <div x-show="rejectModalOpen" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" style="display: none;">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200" @click.outside="rejectModalOpen = false">
      <div class="flex items-center justify-between">
        <h3 class="text-base font-black text-slate-900">Reject Admission Application</h3>
        <button @click="rejectModalOpen = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg">&times;</button>
      </div>

      <p class="text-xs text-slate-600">
        Please state the reason for declining admission for <strong x-text="rejectStudentName" class="text-slate-900"></strong>. This reason will be recorded in the student audit dossier.
      </p>

      <form :action="'/admissions/' + rejectStudentId + '/reject-application'" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Rejection Reason</label>
          <textarea name="rejection_reason" rows="3" required
                    placeholder="e.g. Incomplete documentation, age criteria not met, class seat full..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-rose-200 focus:border-rose-500"></textarea>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
          <button type="button" @click="rejectModalOpen = false" class="btn btn-secondary btn-sm">Cancel</button>
          <button type="submit" class="btn btn-sm bg-rose-600 hover:bg-rose-700 text-white font-bold">Confirm Rejection</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection
