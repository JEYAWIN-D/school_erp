<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; line-height: 1.5; }
  .header { text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 8px; margin-bottom: 12px; }
  .school-name { font-size: 15px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
  .exam-title { font-size: 12px; font-weight: bold; margin-top: 4px; text-transform: uppercase; }
  .meta-row { display: flex; justify-content: space-between; font-size: 10px; margin: 6px 0; }
  .instructions { border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px 8px; background: #f8fafc; margin-bottom: 12px; font-size: 9.5px; }
  .instructions strong { display: block; margin-bottom: 2px; font-size: 10px; }
  .section-header { background: #1e293b; color: white; padding: 3px 8px; font-weight: bold; font-size: 10px; margin: 10px 0 6px; border-radius: 2px; }
  .question { margin-bottom: 10px; }
  .q-header { display: flex; justify-content: space-between; margin-bottom: 3px; }
  .q-num { font-weight: bold; font-size: 11px; }
  .q-marks { color: #64748b; font-size: 10px; }
  .q-text { font-size: 11px; }
  .q-type { display: inline-block; border: 1px solid #cbd5e1; border-radius: 3px; padding: 0px 5px; font-size: 9px; color: #64748b; margin-right: 4px; }
  .options { margin-top: 4px; padding-left: 12px; }
  .option { font-size: 10px; color: #475569; margin: 1px 0; }
  .answer-space { border-bottom: 1px dashed #cbd5e1; height: 16px; margin: 2px 0; }
  .footer { border-top: 1px solid #cbd5e1; margin-top: 16px; padding-top: 6px; display: flex; justify-content: space-between; font-size: 9px; color: #94a3b8; }
  .page-break { page-break-before: always; }
</style>
</head>
<body>

<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'School' }}</div>
  @if($school?->address)<div style="font-size:9px;color:#64748b;margin-top:2px;">{{ $school->address }}</div>@endif
  <div class="exam-title">{{ $request->paper_title }}</div>
  @if($exam)<div style="font-size:10px;margin-top:2px;">{{ $exam->name }}</div>@endif
</div>

<div class="meta-row">
  <span>Total Marks: <strong>{{ $totalMarks }}</strong></span>
  <span>Duration: <strong>{{ $duration }} Minutes</strong></span>
  <span>Date: _________________</span>
</div>
<div class="meta-row">
  <span>Name: _______________________________________________</span>
  <span>Class: ________________</span>
  <span>Roll No.: ________</span>
</div>

@if($request->instructions)
<div class="instructions">
  <strong>Instructions:</strong>
  {{ $request->instructions }}
</div>
@endif

@php
  $grouped = $questions->groupBy('question_type');
  $sectionLetters = ['A','B','C','D','E','F'];
  $typeLabels = ['mcq'=>'Multiple Choice Questions','true_false'=>'True or False','short'=>'Short Answer Questions','long'=>'Long Answer Questions','descriptive'=>'Descriptive / Essay Questions'];
  $sectionIdx = 0;
  $qNum = 1;
@endphp

@foreach($grouped as $type => $typeQuestions)
<div class="section-header">
  SECTION {{ $sectionLetters[$sectionIdx++] }}: {{ strtoupper($typeLabels[$type] ?? str_replace('_',' ',$type)) }}
  ({{ $typeQuestions->count() }} Questions &times; marks as specified)
</div>

@foreach($typeQuestions as $q)
<div class="question">
  <div class="q-header">
    <div>
      <span class="q-num">Q{{ $qNum++ }}.</span>
      @if($q->chapter) <span class="q-type">{{ $q->chapter }}</span>@endif
    </div>
    <div class="q-marks">[{{ $q->marks }} Mark{{ $q->marks != 1 ? 's' : '' }}]</div>
  </div>
  <div class="q-text">{{ $q->question_text }}</div>
  @if($type === 'mcq' && $q->options)
    @php $opts = is_array($q->options) ? $q->options : json_decode($q->options, true); @endphp
    @if(is_array($opts))
    <div class="options">
      @foreach($opts as $i => $opt)
      <div class="option">{{ chr(65+$i) }}) {{ $opt['text'] ?? '' }}</div>
      @endforeach
    </div>
    @endif
  @elseif($type === 'true_false')
    <div class="options"><span class="option">( ) True &nbsp;&nbsp;&nbsp;&nbsp; ( ) False</span></div>
  @elseif($type === 'short')
    <div class="answer-space"></div><div class="answer-space"></div>
  @elseif(in_array($type, ['long','descriptive']))
    @for($i=0;$i<5;$i++)<div class="answer-space"></div>@endfor
  @endif
</div>
@endforeach
@endforeach

<div class="footer">
  <span>{{ $school?->school_name ?? '' }}</span>
  <span>{{ $request->paper_title }} — Total: {{ $totalMarks }} Marks</span>
  <span>Generated: {{ now()->format('d M Y') }}</span>
</div>
</body>
</html>
