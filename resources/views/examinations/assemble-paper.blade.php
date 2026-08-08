@extends('layouts.app')
@section('title','Assemble Question Paper')
@section('content')
<div class="space-y-6" x-data="{ selected: [] }">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Manual Question Paper Assembly</h1>
      <p class="page-subtitle">Select questions from the bank to assemble a custom question paper</p>
    </div>
    <a href="{{ route('examinations.question-papers') }}" class="btn btn-secondary btn-sm">← Question Papers</a>
  </div>

  {{-- Filter bar --}}
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap items-end">
    <div>
      <label class="label">Class</label>
      <select name="class_id" class="select w-36">
        <option value="">All</option>
        @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
      </select>
    </div>
    <div>
      <label class="label">Subject <span class="text-red-400">*</span></label>
      <select name="subject_id" class="select w-40">
        <option value="">Select subject</option>
        @foreach($subjects as $s)<option value="{{ $s->id }}" @selected(request('subject_id')==$s->id)>{{ $s->name }}</option>@endforeach
      </select>
    </div>
    <div>
      <label class="label">Type</label>
      <select name="question_type" class="select w-36">
        <option value="">All Types</option>
        @foreach(['mcq'=>'MCQ','true_false'=>'True/False','short'=>'Short Answer','long'=>'Long Answer','descriptive'=>'Descriptive'] as $k=>$v)
        <option value="{{ $k }}" @selected(request('question_type')===$k)>{{ $v }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="label">Difficulty</label>
      <select name="difficulty" class="select w-32">
        <option value="">All</option>
        <option value="easy" @selected(request('difficulty')==='easy')>Easy</option>
        <option value="medium" @selected(request('difficulty')==='medium')>Medium</option>
        <option value="hard" @selected(request('difficulty')==='hard')>Hard</option>
      </select>
    </div>
    <div>
      <label class="label">Chapter</label>
      <input type="text" name="chapter" value="{{ request('chapter') }}" class="input w-36" placeholder="Chapter name">
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Load Questions</button>
  </div></form>

  @if($questions->isEmpty() && !request()->hasAny(['subject_id','class_id']))
    <div class="card text-center py-12 text-slate-400">
      <p class="text-lg font-medium mb-2">Select a Subject to Load Questions</p>
      <p class="text-sm">Filter by subject and class above, then select questions to assemble your paper.</p>
    </div>
  @else

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Question list --}}
    <div class="lg:col-span-2 space-y-3">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold text-slate-700">
          {{ $questions->count() }} questions found
          @if($questions->isNotEmpty())
          <span x-text="' — ' + selected.length + ' selected (' + selected.reduce((sum, id) => {
            const el = document.querySelector(`[data-q=\"${id}\"]`);
            return sum + (el ? parseInt(el.dataset.marks) : 0);
          }, 0) + \" marks)\""></span>
          @endif
        </h3>
        @if($questions->isNotEmpty())
        <button type="button"
          @click="selected = selected.length === {{ $questions->count() }} ? [] : [{{ $questions->pluck('id')->join(',') }}]"
          class="btn btn-secondary btn-sm text-xs">Toggle All</button>
        @endif
      </div>

      @forelse($questions as $q)
      <div class="card cursor-pointer border-2 transition-colors"
           :class="selected.includes({{ $q->id }}) ? 'border-indigo-400 bg-indigo-50/40' : 'border-transparent'"
           @click="selected.includes({{ $q->id }}) ? selected = selected.filter(x => x !== {{ $q->id }}) : selected.push({{ $q->id }})"
           data-q="{{ $q->id }}" data-marks="{{ $q->marks }}">
        <div class="flex items-start gap-3">
          <div class="mt-1 shrink-0">
            <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors"
                 :class="selected.includes({{ $q->id }}) ? 'border-indigo-500 bg-indigo-500' : 'border-slate-300'">
              <svg x-show="selected.includes({{ $q->id }})" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
              <span class="badge-{{ ['mcq'=>'indigo','short'=>'green','long'=>'amber','true_false'=>'slate','descriptive'=>'red'][$q->question_type] ?? 'slate' }} text-xs capitalize">
                {{ str_replace('_',' ',$q->question_type) }}
              </span>
              <span class="badge-{{ $q->difficulty_level==='easy'?'green':($q->difficulty_level==='hard'?'red':'amber') }} text-xs capitalize">
                {{ $q->difficulty_level }}
              </span>
              <span class="text-xs text-slate-400">{{ $q->marks }} mark{{ $q->marks != 1 ? 's' : '' }}</span>
              @if($q->chapter)<span class="text-xs text-slate-400">| {{ $q->chapter }}</span>@endif
            </div>
            <p class="text-sm text-slate-800">{{ \Illuminate\Support\Str::limit($q->question_text, 150) }}</p>
            @if($q->question_type === 'mcq' && $q->options)
              @php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true); @endphp
              @if(is_array($opts))
              <div class="mt-1 grid grid-cols-2 gap-1">
                @foreach($opts as $i => $opt)
                <p class="text-xs {{ ($opt['correct'] ?? false) ? 'text-green-600 font-medium' : 'text-slate-400' }}">
                  {{ chr(65+$i) }}. {{ $opt['text'] ?? '' }}
                </p>
                @endforeach
              </div>
              @endif
            @endif
          </div>
        </div>
      </div>
      @empty
      <div class="card text-center py-8 text-slate-400">No questions match the filters.</div>
      @endforelse
    </div>

    {{-- Paper settings & generate --}}
    <div class="space-y-4">
      <div class="card sticky top-4">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Paper Settings</h3>
        <form method="POST" action="{{ route('examinations.assemble-paper.generate') }}" target="_blank">
          @csrf
          {{-- Hidden selected question IDs --}}
          <template x-for="id in selected" :key="id">
            <input type="hidden" name="question_ids[]" :value="id">
          </template>
          <div class="space-y-3">
            <div>
              <label class="label">Paper Title <span class="text-red-500">*</span></label>
              <input type="text" name="paper_title" class="input" required
                placeholder="e.g. Half-Yearly Science Exam — Class X">
            </div>
            <div>
              <label class="label">Exam</label>
              <select name="exam_id" class="select">
                <option value="">Select exam (optional)</option>
                @foreach($exams as $e)<option value="{{ $e->id }}">{{ $e->name }}</option>@endforeach
              </select>
            </div>
            <div>
              <label class="label">Duration (minutes)</label>
              <input type="number" name="duration" class="input" value="180" min="30" step="30">
            </div>
            <div>
              <label class="label">Template</label>
              <select name="template_id" class="select text-sm">
                <option value="">Default Format</option>
                @foreach(\App\Models\QuestionPaperTemplate::orderByDesc('is_default')->orderBy('name')->get() as $tpl)
                <option value="{{ $tpl->id }}" @selected($tpl->is_default)>{{ $tpl->name }}@if($tpl->is_default) (Default)@endif</option>
                @endforeach
              </select>
              <a href="{{ route('examinations.paper-templates') }}" class="text-xs text-indigo-500 hover:underline mt-1 block">Manage templates →</a>
            </div>
            <div>
              <label class="label">Instructions</label>
              <textarea name="instructions" class="input h-16 text-xs" placeholder="General instructions for students...">All questions are compulsory. Write clearly. No calculators allowed.</textarea>
            </div>
            <div class="pt-2 border-t border-slate-100">
              <div class="flex justify-between text-sm mb-1">
                <span class="text-slate-500">Selected Questions</span>
                <span class="font-bold text-indigo-600" x-text="selected.length"></span>
              </div>
            </div>
            <button type="submit"
                    :disabled="selected.length === 0"
                    :class="selected.length === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                    class="btn btn-primary w-full">
              Generate PDF Paper
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection
