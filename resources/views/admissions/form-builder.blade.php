@extends('layouts.app')
@section('title', 'Application Form Builder')
@section('content')
<div class="space-y-6" x-data="{ showAdd: false }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Application Form Builder</h1>
    <div class="flex gap-2">
      <a href="{{ route('admissions.applications') }}" class="btn btn-secondary btn-sm">View Submissions</a>
      <button @click="showAdd=!showAdd" class="btn btn-primary btn-sm">+ Create Form</button>
    </div>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  {{-- Create Form --}}
  <div x-show="showAdd" x-transition class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Configure Application Form</h3>
    <form method="POST" action="{{ route('admissions.form-builder.store') }}" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="label">Form Title <span class="text-red-500">*</span></label>
          <input type="text" name="title" class="input" required placeholder="e.g. Admission Application 2025-26 — Class VI">
        </div>
        <div>
          <label class="label">Academic Year <span class="text-red-500">*</span></label>
          <select name="academic_year_id" class="select" required>
            <option value="">Select year</option>
            @foreach($years as $y)<option value="{{ $y->id }}">{{ $y->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label class="label">For Class (leave blank for all)</label>
          <select name="class_id" class="select">
            <option value="">All Classes</option>
            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label class="label">Open From</label>
          <input type="date" name="open_from" class="input">
        </div>
        <div>
          <label class="label">Open Until</label>
          <input type="date" name="open_until" class="input">
        </div>
        <div>
          <label class="label">Application Fee (₹)</label>
          <input type="number" name="application_fee" class="input" value="0" min="0">
        </div>
        <div class="md:col-span-2">
          <label class="label">Form Description</label>
          <textarea name="description" class="input" rows="2" placeholder="Shown to applicants at the top of the form"></textarea>
        </div>
      </div>

      {{-- Form Fields --}}
      <div class="border-t border-slate-100 pt-4">
        <h4 class="text-sm font-semibold text-slate-600 mb-3">Form Fields</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-4 text-sm">
          @php
          $allFields = [
            'student_name' => 'Student Name',
            'dob' => 'Date of Birth',
            'gender' => 'Gender',
            'nationality' => 'Nationality',
            'religion' => 'Religion',
            'caste' => 'Caste',
            'category' => 'Category (Gen/SC/ST/OBC)',
            'parent_name' => 'Parent/Guardian Name',
            'parent_mobile' => 'Parent Mobile',
            'parent_email' => 'Parent Email',
            'address' => 'Address',
            'previous_school' => 'Previous School',
            'siblings_in_school' => 'Siblings in this School',
            'annual_income' => 'Annual Family Income',
          ];
          @endphp
          @foreach($allFields as $key => $label)
          <div class="flex items-center gap-2 bg-slate-50 rounded-lg px-3 py-2">
            <input type="checkbox" name="field_{{ $key }}" value="1" id="f_{{ $key }}" class="w-4 h-4"
                   @checked(in_array($key, ['student_name','parent_name','parent_mobile','class_id','dob','gender']))>
            <label for="f_{{ $key }}" class="text-sm text-slate-700 flex-1">{{ $label }}</label>
            <input type="checkbox" name="req_{{ $key }}" value="1" title="Required"
                   class="w-3.5 h-3.5 accent-red-500"
                   @checked(in_array($key, ['student_name','parent_name','parent_mobile']))>
            <span class="text-xs text-red-400">Req</span>
          </div>
          @endforeach
        </div>
      </div>

      {{-- Document Fields --}}
      <div class="border-t border-slate-100 pt-4">
        <h4 class="text-sm font-semibold text-slate-600 mb-3">Document Upload Fields</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-4 text-sm">
          @php
          $docFields = [
            'birth_certificate' => 'Birth Certificate',
            'aadhaar_card' => 'Aadhaar Card',
            'transfer_certificate' => 'Transfer Certificate',
            'passport_photo' => 'Passport Photo',
            'caste_certificate' => 'Caste Certificate',
            'address_proof' => 'Address Proof',
            'income_certificate' => 'Income Certificate',
          ];
          @endphp
          @foreach($docFields as $key => $label)
          <div class="flex items-center gap-2 bg-amber-50 rounded-lg px-3 py-2">
            <input type="checkbox" name="doc_{{ $key }}" value="1" id="d_{{ $key }}" class="w-4 h-4">
            <label for="d_{{ $key }}" class="text-sm text-slate-700 flex-1">{{ $label }}</label>
            <input type="checkbox" name="docreq_{{ $key }}" value="1" title="Required" class="w-3.5 h-3.5 accent-red-500">
            <span class="text-xs text-red-400">Req</span>
          </div>
          @endforeach
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Create Form</button>
        <button type="button" @click="showAdd=false" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>

  {{-- Existing Forms --}}
  @forelse($configs as $config)
  <div class="card">
    <div class="flex items-start justify-between flex-wrap gap-3">
      <div>
        <div class="flex items-center gap-2 flex-wrap">
          <h3 class="font-semibold text-slate-800">{{ $config->title }}</h3>
          <span class="badge-{{ $config->is_active ? 'green' : 'slate' }} text-xs">{{ $config->is_active ? 'Active' : 'Inactive' }}</span>
          @if($config->class)<span class="badge-indigo text-xs">{{ $config->class->name }}</span>@endif
        </div>
        <p class="text-xs text-slate-400 mt-1">
          {{ $config->academicYear?->name }}
          @if($config->open_from) | Open: {{ $config->open_from->format('d M Y') }}@endif
          @if($config->open_until) – {{ $config->open_until->format('d M Y') }}@endif
          | Fields: {{ count($config->fields ?? []) }}
          | Docs: {{ count($config->document_fields ?? []) }}
          | Submissions: {{ $config->applications_count }}
        </p>
        {{-- Shareable link --}}
        <div class="flex items-center gap-2 mt-2">
          <span class="text-xs text-slate-400">Form link:</span>
          <code class="text-xs bg-slate-100 rounded px-2 py-0.5 select-all">{{ url('/apply/' . $config->link_token) }}</code>
          <button onclick="navigator.clipboard.writeText('{{ url('/apply/' . $config->link_token) }}')" class="text-xs text-indigo-500 hover:underline">Copy</button>
        </div>
      </div>
      <div class="flex gap-2 shrink-0">
        <a href="{{ url('/apply/' . $config->link_token) }}" target="_blank" class="btn btn-secondary btn-xs">Preview</a>
        <form method="POST" action="{{ route('admissions.form-builder.toggle', $config->id) }}" class="inline">
          @csrf
          <button type="submit" class="btn btn-secondary btn-xs">{{ $config->is_active ? 'Deactivate' : 'Activate' }}</button>
        </form>
        <form method="POST" action="{{ route('admissions.form-builder.delete', $config->id) }}" onsubmit="return confirm('Delete this form?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-xs text-red-400">Delete</button>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="card text-center py-12 text-slate-400">
    <p class="text-sm">No application forms created yet.</p>
    <p class="text-xs mt-1">Create a form to generate a shareable link for parents to apply online.</p>
  </div>
  @endforelse
</div>
@endsection
