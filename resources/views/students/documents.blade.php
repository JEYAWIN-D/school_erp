@extends('layouts.app')
@section('title','Student Documents — '.$student->full_name)
@section('content')
<div class="space-y-6">
  <nav class="text-sm text-slate-400 flex items-center gap-1.5 mb-1">
    <a href="{{ route('students.index') }}" class="hover:text-slate-600">Students</a>
    <span>/</span>
    <a href="{{ route('students.show',$student->id) }}" class="hover:text-slate-600">{{ $student->full_name }}</a>
    <span>/</span>
    <span class="text-slate-600">Documents</span>
  </nav>
  <div class="flex items-center gap-3">
    <a href="{{ route('students.show',$student->id) }}" class="text-slate-400 hover:text-slate-700">←</a>
    <h1 class="page-title">Documents — {{ $student->full_name }}</h1>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <form method="POST" action="{{ route('students.documents.upload',$student->id) }}" enctype="multipart/form-data" class="card space-y-4">
      @csrf
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Upload Document</h3>
      <div><label class="label">Document Type <span class="text-red-500">*</span></label>
        <select name="document_type" class="select">
          @foreach(['aadhaar'=>'Aadhaar Card','birth_certificate'=>'Birth Certificate','tc'=>'Transfer Certificate','marksheet'=>'Previous Marksheet','migration_certificate'=>'Migration Certificate','caste'=>'Caste Certificate','address_proof'=>'Address Proof','medical'=>'Medical Certificate','scholarship_sanction'=>'Scholarship Sanction Letter','other'=>'Other'] as $k=>$v)
          <option value="{{ $k }}">{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div><label class="label">File (PDF/JPG/PNG, max 2MB) <span class="text-red-500">*</span></label>
        <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" class="input" required>
      </div>
      <div>
        <label class="label">Expiry Date <span class="text-slate-400 text-xs">(optional, for medical/TC etc.)</span></label>
        <input type="date" name="expiry_date" class="input">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Upload</button>
    </form>
    <div class="lg:col-span-2 card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Uploaded Documents</h3>
      @forelse($documents as $doc)
      <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          </div>
          <div>
            <p class="text-sm font-medium text-slate-800">{{ str_replace('_',' ',ucfirst($doc->document_type)) }}</p>
            <p class="text-xs text-slate-400">{{ $doc->original_name }} · {{ $doc->created_at->format('d M Y') }}
              @if($doc->expiry_date)
                · <span class="{{ $doc->expiry_date->isPast() ? 'text-red-500 font-semibold' : ($doc->expiry_date->diffInDays(today()) <= 30 ? 'text-amber-500' : 'text-slate-400') }}">
                  Exp: {{ $doc->expiry_date->format('d M Y') }}
                </span>
              @endif
            </p>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <span class="badge-{{ $doc->status === 'verified' ? 'green' : ($doc->status === 'rejected' ? 'red' : 'amber') }} text-xs capitalize">{{ $doc->status }}</span>
          <a href="{{ route('students.documents.download', [$student->id, $doc->id]) }}" target="_blank" class="btn btn-ghost btn-xs">View / Download</a>
          @if($doc->status === 'pending')
          <form method="POST" action="{{ route('students.documents.verify',$doc->id) }}" class="inline">@csrf
            <button type="submit" class="btn btn-ghost btn-xs text-green-600">Verify</button>
          </form>
          @endif
        </div>
      </div>
      @empty
      <p class="text-slate-400 text-sm text-center py-6">No documents uploaded.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
