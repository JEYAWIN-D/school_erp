@extends('layouts.app')
@section('title', 'Question Paper Templates')
@section('content')
<div class="space-y-6" x-data="{ showAdd: false }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Question Paper Templates</h1>
    <div class="flex gap-2">
      <a href="{{ route('examinations.question-papers') }}" class="btn btn-secondary btn-sm">← Question Papers</a>
      <button @click="showAdd=!showAdd" class="btn btn-primary btn-sm">+ New Template</button>
    </div>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  {{-- Add Template Form --}}
  <div x-show="showAdd" x-transition class="card">
    <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Create Question Paper Template</h3>
    <form method="POST" action="{{ route('examinations.paper-templates.store') }}" class="space-y-5">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="label">Template Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" class="input" required placeholder="e.g. Annual Exam Format">
        </div>
        <div>
          <label class="label">Exam Type</label>
          <select name="exam_type" class="select">
            <option value="">General</option>
            <option value="annual">Annual Exam</option>
            <option value="unit_test">Unit Test</option>
            <option value="midterm">Mid-term</option>
            <option value="pre_board">Pre-Board</option>
            <option value="weekly">Weekly Test</option>
          </select>
        </div>
      </div>

      <div class="border-t border-slate-100 pt-4">
        <p class="text-sm font-medium text-slate-600 mb-3">Header Options</p>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          @foreach(['show_school_logo'=>'School Logo','show_school_name'=>'School Name','show_school_address'=>'Address','show_affiliation'=>'Affiliation No.'] as $k=>$label)
          <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="{{ $k }}" value="1" checked class="w-4 h-4 rounded">{{ $label }}
          </label>
          @endforeach
        </div>
      </div>

      <div>
        <label class="label">Header Instructions</label>
        <input type="text" name="header_instructions" class="input" placeholder="e.g. Time: 3 Hrs. | Maximum Marks: 80">
      </div>

      <div>
        <label class="label">General Instructions (printed at top of paper)</label>
        <textarea name="general_instructions" class="input" rows="4" placeholder="1. All questions are compulsory.&#10;2. Write clearly in blue or black pen.&#10;3. Calculators are not allowed."></textarea>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="label">Question Numbering</label>
          <select name="question_numbering" class="select">
            <option value="numeric">1, 2, 3...</option>
            <option value="alpha">a, b, c...</option>
            <option value="roman">i, ii, iii...</option>
          </select>
        </div>
        <div>
          <label class="label">Paper Size</label>
          <select name="paper_size" class="select">
            <option value="A4">A4</option>
            <option value="Legal">Legal</option>
          </select>
        </div>
        <div>
          <label class="label">Font Size (pt)</label>
          <select name="font_size" class="select">
            <option value="11">11pt</option>
            <option value="12" selected>12pt</option>
            <option value="13">13pt</option>
            <option value="14">14pt</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <label class="flex items-center gap-2 text-sm text-slate-600">
          <input type="checkbox" name="show_marks_per_question" value="1" checked class="w-4 h-4 rounded">Show Marks/Question
        </label>
        <label class="flex items-center gap-2 text-sm text-slate-600">
          <input type="checkbox" name="show_section_totals" value="1" checked class="w-4 h-4 rounded">Show Section Totals
        </label>
        <label class="flex items-center gap-2 text-sm text-slate-600" x-data="{lines:false}">
          <input type="checkbox" name="show_answer_lines" value="1" x-model="lines" class="w-4 h-4 rounded">Answer Lines
          <input x-show="lines" type="number" name="answer_lines_count" value="5" min="1" max="20" class="input w-16 text-sm ml-1">
        </label>
        <label class="flex items-center gap-2 text-sm text-slate-600">
          <input type="checkbox" name="is_default" value="1" class="w-4 h-4 rounded">Set as Default
        </label>
      </div>

      <div>
        <label class="label">Watermark Text (optional)</label>
        <input type="text" name="watermark_text" class="input" placeholder="CONFIDENTIAL / DRAFT">
      </div>

      <div class="flex gap-3 pt-2">
        <button type="submit" class="btn btn-primary">Save Template</button>
        <button type="button" @click="showAdd=false" class="btn btn-secondary">Cancel</button>
      </div>
    </form>
  </div>

  {{-- Templates List --}}
  @forelse($templates as $t)
  <div class="card">
    <div class="flex items-start justify-between">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <h3 class="font-semibold text-slate-800">{{ $t->name }}</h3>
          @if($t->is_default)<span class="badge-green text-xs">Default</span>@endif
          @if($t->exam_type)<span class="badge-slate text-xs capitalize">{{ str_replace('_',' ',$t->exam_type) }}</span>@endif
        </div>
        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 mt-2">
          <span>Paper: {{ $t->paper_size }}</span>
          <span>Font: {{ $t->font_size }}pt</span>
          <span>Numbering: {{ ucfirst($t->question_numbering) }}</span>
          @if($t->show_school_name)<span class="text-green-600">School Name ✓</span>@endif
          @if($t->show_affiliation)<span class="text-green-600">Affiliation ✓</span>@endif
          @if($t->show_marks_per_question)<span class="text-green-600">Marks/Question ✓</span>@endif
          @if($t->watermark_text)<span class="text-amber-600">Watermark: {{ $t->watermark_text }}</span>@endif
        </div>
        @if($t->general_instructions)
        <p class="text-xs text-slate-400 mt-2 italic max-w-xl line-clamp-2">{{ $t->general_instructions }}</p>
        @endif
      </div>
      <form method="POST" action="{{ route('examinations.paper-templates.delete', $t->id) }}" onsubmit="return confirm('Delete this template?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-xs text-red-400 hover:text-red-600">Delete</button>
      </form>
    </div>
  </div>
  @empty
  <div class="card text-center py-12 text-slate-400">
    <p class="text-sm">No templates created yet.</p>
    <p class="text-xs mt-1">Create a template to control the letterhead and format of printed question papers.</p>
  </div>
  @endforelse
</div>
@endsection
