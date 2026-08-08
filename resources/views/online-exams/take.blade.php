@extends('layouts.app')
@section('title', $exam->title)
@section('content')
<div class="max-w-3xl mx-auto space-y-6"
     x-data="examRunner({{ $remaining }}, {{ $attempt->id }}, '{{ csrf_token() }}')"
     x-init="startTimer()">

  {{-- Header with timer --}}
  <div class="card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="page-title">{{ $exam->title }}</h1>
      <p class="page-subtitle">{{ $exam->class?->name }} @if($exam->subject) | {{ $exam->subject->name }} @endif</p>
    </div>
    <div class="text-center">
      <p class="text-xs text-slate-400 mb-1">Time Remaining</p>
      <p class="text-2xl font-bold font-mono" :class="timeLeft <= 60 ? 'text-red-600' : 'text-slate-800'" x-text="formatTime()"></p>
    </div>
  </div>

  @if($exam->instructions)
    <div class="card-flat py-3 text-sm text-slate-600 bg-blue-50 border-blue-200">
      <strong>Instructions:</strong> {{ $exam->instructions }}
    </div>
  @endif

  {{-- Questions --}}
  <form id="exam-form">
    <div class="space-y-4">
      @foreach($orderedQs as $idx => $q)
        <div class="card" id="q-{{ $q->id }}">
          <p class="font-semibold text-slate-800 mb-3">{{ $idx + 1 }}. {{ $q->question }}
            <span class="text-xs text-slate-400 font-normal ml-2">({{ $q->marks }} mark{{ $q->marks != 1 ? 's' : '' }})</span>
          </p>

          @if($q->question_type === 'mcq')
            @foreach(['a' => $q->option_a, 'b' => $q->option_b, 'c' => $q->option_c, 'd' => $q->option_d] as $opt => $text)
              @if($text)
                <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition mb-2">
                  <input type="radio" name="q_{{ $q->id }}" value="{{ $opt }}"
                         @if(isset($responses[$q->id]) && $responses[$q->id] === $opt) checked @endif
                         @change="saveAnswer({{ $q->id }}, $el.value)" class="text-blue-600">
                  <span class="text-sm text-slate-700"><strong>{{ strtoupper($opt) }}.</strong> {{ $text }}</span>
                </label>
              @endif
            @endforeach

          @elseif($q->question_type === 'true_false')
            @foreach(['true' => 'True', 'false' => 'False'] as $val => $label)
              <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition mb-2">
                <input type="radio" name="q_{{ $q->id }}" value="{{ $val }}"
                       @if(isset($responses[$q->id]) && $responses[$q->id] === $val) checked @endif
                       @change="saveAnswer({{ $q->id }}, $el.value)" class="text-blue-600">
                <span class="text-sm text-slate-700">{{ $label }}</span>
              </label>
            @endforeach

          @elseif($q->question_type === 'fill_blank')
            <input type="text" name="q_{{ $q->id }}" class="input"
                   value="{{ $responses[$q->id] ?? '' }}"
                   placeholder="Type your answer…"
                   @change="saveAnswer({{ $q->id }}, $el.value)"
                   @keyup.debounce.500ms="saveAnswer({{ $q->id }}, $el.value)">

          @elseif($q->question_type === 'short_answer')
            <textarea name="q_{{ $q->id }}" class="input" rows="3"
                      placeholder="Write your answer…"
                      @keyup.debounce.800ms="saveAnswer({{ $q->id }}, $el.value)">{{ $responses[$q->id] ?? '' }}</textarea>
          @endif
        </div>
      @endforeach
    </div>
  </form>

  {{-- Submit button --}}
  <div class="flex justify-end gap-3">
    <button type="button" @click="confirmSubmit(false)"
            class="btn btn-primary"
            :disabled="submitting">
      <span x-show="!submitting">Submit Exam</span>
      <span x-show="submitting">Submitting…</span>
    </button>
  </div>

</div>

{{-- Hidden auto-submit form --}}
<form id="submit-form" method="POST" action="{{ route('online-exams.submit', $attempt->id) }}" style="display:none">
  @csrf
  <input type="hidden" name="auto_submit" id="auto-submit-flag" value="0">
</form>
@endsection

@push('scripts')
<script>
function examRunner(remainingSeconds, attemptId, csrfToken) {
  return {
    timeLeft: remainingSeconds,
    submitting: false,
    timer: null,

    startTimer() {
      this.timer = setInterval(() => {
        this.timeLeft--;
        if (this.timeLeft <= 0) {
          clearInterval(this.timer);
          this.autoSubmit();
        }
      }, 1000);
    },

    formatTime() {
      const h = Math.floor(this.timeLeft / 3600);
      const m = Math.floor((this.timeLeft % 3600) / 60);
      const s = this.timeLeft % 60;
      return (h > 0 ? h + ':' : '') +
             String(m).padStart(2, '0') + ':' +
             String(s).padStart(2, '0');
    },

    async saveAnswer(qId, answer) {
      try {
        await fetch('/online-exams/attempt/' + attemptId + '/answer', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
          },
          body: JSON.stringify({ question_id: qId, answer: answer }),
        });
      } catch (e) {
        console.warn('Save failed:', e);
      }
    },

    confirmSubmit(isAuto) {
      if (!isAuto && !confirm('Are you sure you want to submit the exam? You cannot change answers after submission.')) return;
      this.submitting = true;
      clearInterval(this.timer);
      document.getElementById('auto-submit-flag').value = isAuto ? '1' : '0';
      document.getElementById('submit-form').submit();
    },

    autoSubmit() {
      this.confirmSubmit(true);
    }
  }
}
</script>
@endpush
