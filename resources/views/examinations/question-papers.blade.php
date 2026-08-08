@extends('layouts.app')
@section('title', 'Question Papers')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Question Paper Management</h1>
    <div class="flex gap-2 flex-wrap">
      <a href="{{ route('examinations.paper-templates') }}" class="btn btn-secondary btn-sm">Paper Templates</a>
      <a href="{{ route('examinations.assemble-paper') }}" class="btn btn-secondary btn-sm">Assemble from Bank</a>
      <a href="{{ route('examinations.index') }}" class="btn btn-secondary btn-sm">Back</a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Upload Form --}}
    <div class="card space-y-4">
      <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Upload Question Paper (PDF)</h2>
      <form method="POST" action="{{ route('examinations.question-papers.upload') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <div>
          <label class="label text-xs">Title <span class="text-red-500">*</span></label>
          <input type="text" name="title" class="input text-sm" required value="{{ old('title') }}" placeholder="e.g. Annual Exam - Mathematics 2025">
        </div>
        <div>
          <label class="label text-xs">PDF File <span class="text-red-500">*</span></label>
          <input type="file" name="file" accept=".pdf" required
                 class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
          <p class="text-xs text-slate-400 mt-1">PDF only — max 20 MB</p>
        </div>
        <div>
          <label class="label text-xs">Exam</label>
          <select name="exam_id" class="select text-sm">
            <option value="">Select Exam (optional)</option>
            @foreach($exams as $exam)
              <option value="{{ $exam->id }}" @selected(old('exam_id') == $exam->id)>{{ $exam->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label text-xs">Class</label>
          <select name="class_id" class="select text-sm">
            <option value="">All Classes</option>
            @foreach($classes as $class)
              <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label text-xs">Subject</label>
          <select name="subject_id" class="select text-sm">
            <option value="">Select Subject (optional)</option>
            @foreach($subjects as $sub)
              <option value="{{ $sub->id }}" @selected(old('subject_id') == $sub->id)>{{ $sub->name }}</option>
            @endforeach
          </select>
        </div>
        <div x-data="{ restricted: {{ old('is_restricted', 1) ? 'true' : 'false' }} }">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="is_restricted" value="1" x-model="restricted"
                   class="w-4 h-4 rounded border-slate-300 text-indigo-600">
            <span class="text-sm text-slate-700">Restrict access until exam date</span>
          </label>
          <div x-show="restricted" x-transition class="mt-2">
            <label class="label text-xs">Accessible From</label>
            <input type="date" name="accessible_from" class="input text-sm" value="{{ old('accessible_from') }}">
          </div>
        </div>
        <button type="submit" class="btn btn-primary w-full">Upload</button>
      </form>
    </div>

    {{-- Papers List --}}
    <div class="lg:col-span-2 card overflow-x-auto">
      <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
        <h2 class="font-semibold text-slate-700">Uploaded Papers</h2>
        <form method="GET" class="flex gap-2">
          <select name="exam_id" class="select text-xs">
            <option value="">All Exams</option>
            @foreach($exams as $exam)
              <option value="{{ $exam->id }}" @selected(request('exam_id') == $exam->id)>{{ $exam->name }}</option>
            @endforeach
          </select>
          <select name="class_id" class="select text-xs">
            <option value="">All Classes</option>
            @foreach($classes as $class)
              <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
        </form>
      </div>
      @if($papers->isEmpty())
        <p class="text-slate-400 text-sm text-center py-8">No question papers uploaded yet.</p>
      @else
      <table class="table-wrap w-full text-sm">
        <thead>
          <tr>
            <th class="th">Title</th>
            <th class="th">Exam</th>
            <th class="th">Class</th>
            <th class="th">Subject</th>
            <th class="th">Access</th>
            <th class="th">Uploaded</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($papers as $paper)
          <tr class="tr">
            <td class="td font-medium">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{ $paper->title }}
              </div>
            </td>
            <td class="td text-slate-500">{{ $paper->exam?->name ?? '—' }}</td>
            <td class="td text-slate-500">{{ $paper->class?->name ?? '—' }}</td>
            <td class="td text-slate-500">{{ $paper->subject?->name ?? '—' }}</td>
            <td class="td">
              @if(!$paper->is_restricted)
                <span class="badge-green">Open</span>
              @elseif($paper->accessible_from && $paper->accessible_from->isFuture())
                <span class="badge-amber">From {{ $paper->accessible_from->format('d M Y') }}</span>
              @else
                <span class="badge-green">Available</span>
              @endif
            </td>
            <td class="td text-xs text-slate-400">{{ $paper->created_at->format('d M Y') }}</td>
            <td class="td">
              <div class="flex gap-1 flex-wrap">
                <a href="{{ route('examinations.question-papers.print', $paper->id) }}" target="_blank" class="btn-xs bg-slate-100 text-slate-700 hover:bg-slate-200 rounded px-2 py-0.5 text-xs">Print</a>
                <a href="{{ route('examinations.question-papers.download', $paper->id) }}" class="btn-xs btn-primary">Download</a>
                <form method="POST" action="{{ route('examinations.question-papers.delete', $paper->id) }}"
                      onsubmit="return confirm('Delete this paper?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn-xs bg-red-100 text-red-600 hover:bg-red-200 rounded px-2 py-0.5 text-xs">Del</button>
                </form>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>
  </div>
</div>
@endsection
