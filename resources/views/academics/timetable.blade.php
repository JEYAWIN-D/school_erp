@extends('layouts.app')
@section('title', 'Timetable')
@section('content')
<div class="space-y-6" x-data="timetableEditor()" @keydown.escape.window="close()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Class Timetable</h1>
    <div class="flex gap-2">
      <span class="btn btn-secondary btn-sm text-slate-400 border-slate-200 cursor-not-allowed" title="Conflict checker coming soon">Check Conflicts</span>
      @if(request('class_id'))
        <a href="{{ route('academics.timetable.pdf', request()->only(['class_id','section_id'])) }}" target="_blank" class="btn btn-secondary btn-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
          Print PDF
        </a>
      @endif
    </div>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="class_id" class="select w-36" onchange="this.form.submit()">
      <option value="">Select Class</option>
      @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="section_id" class="select w-36">
      <option value="">Select Section</option>
      @foreach($sections as $s)<option value="{{ $s->id }}" @selected(request('section_id')==$s->id)>{{ $s->name }}</option>@endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">View Timetable</button>
  </div></form>
  @if(count($timetable))
  @if(request('class_id'))
  <div class="flex items-center justify-between text-xs text-slate-500 bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg">
    <span>💡 Click on any period box to edit its subject, assigned handling teacher, or timings.</span>
    <span class="text-indigo-600 font-semibold">Teacher names are displayed inside each scheduled period</span>
  </div>
  @endif
  <div class="card overflow-x-auto shadow-sm">
    @php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; @endphp
    <table class="min-w-full text-sm border-collapse">
      <thead><tr class="bg-slate-100/80">
        <th class="border border-slate-200 px-3 py-2.5 text-slate-700 font-bold w-24 text-left">Day</th>
        @php $periods = range(1, $timetable->flatten()->max('period_number') ?: 8); @endphp
        @foreach($periods as $p)<th class="border border-slate-200 px-3 py-2.5 text-slate-700 font-bold text-center">Period {{ $p }}</th>@endforeach
      </tr></thead>
      <tbody>
        @foreach($days as $day)
        @php
          $dayMap = ['Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6];
          $dayNum = $dayMap[$day] ?? null;
          $daySlots = $timetable[$day] ?? ($dayNum ? ($timetable[$dayNum] ?? collect()) : collect());
        @endphp
        <tr class="hover:bg-slate-50">
          <td class="border border-slate-200 px-3 py-3 font-semibold text-slate-700 bg-slate-50/50">{{ $day }}</td>
          @foreach($periods as $p)
          @php
            $slot = $daySlots->firstWhere('period_number', $p);
            $type = $slot?->period_type ?? 'class';
            $slotTeacherId = $slot?->teacher_id ?? $slot?->employee_id;
            $slotTeacherName = $slot?->teacher?->full_name ?: ($slot?->teacher?->first_name ? $slot->teacher->first_name . ' ' . $slot->teacher->last_name : null);
          @endphp
          <td class="border border-slate-200 px-2 py-2.5 text-center cursor-pointer hover:bg-indigo-50/80 transition group
            {{ $type === 'break' ? 'bg-amber-50/60' : ($type === 'lunch' ? 'bg-emerald-50/60' : ($type === 'free' ? 'bg-slate-50/60' : '')) }}"
            @click="openEditor('{{ $day }}', {{ $p }}, '{{ request('class_id') }}', '{{ request('section_id') }}', {{ $slot ? "'{$slot->subject_id}'" : 'null' }}, {{ $slotTeacherId ? "'{$slotTeacherId}'" : 'null' }}, '{{ $slot?->period_type ?? 'class' }}', '{{ $slot?->start_time ?? '' }}', '{{ $slot?->end_time ?? '' }}')">
            @if($slot)
              @if(in_array($type, ['break','lunch','free']))
                <span class="text-xs font-bold uppercase tracking-wider {{ $type === 'break' ? 'text-amber-700' : ($type === 'lunch' ? 'text-emerald-700' : 'text-slate-400') }}">
                  {{ ucfirst($type) }}
                </span>
                @if($slot->start_time && $slot->end_time)
                <span class="block text-[11px] text-slate-400 mt-0.5 font-mono">{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i') }}</span>
                @endif
              @else
                <div class="font-bold text-slate-900 text-xs tracking-tight">{{ $slot->subject?->name ?? 'Subject' }}</div>
                
                {{-- Prominent Handling Teacher Display --}}
                <div class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-200/80 text-[11px] font-semibold text-indigo-700 max-w-[145px] truncate" title="{{ $slotTeacherName ?: 'Teacher not specified' }}">
                  <svg class="w-3 h-3 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                  <span class="truncate">{{ $slotTeacherName ?: 'Faculty In-charge' }}</span>
                </div>

                @if($slot->start_time && $slot->end_time)
                  <span class="block text-[10px] text-slate-400 mt-0.5 font-mono">{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i') }}</span>
                @endif
              @endif
            @else
              <span class="text-slate-300 group-hover:text-indigo-500 text-xs font-medium">+ Add</span>
            @endif
          </td>
          @endforeach
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Edit Modal --}}
  <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="close">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm" @click.stop>
      <div class="flex items-center justify-between p-4 border-b">
        <h3 class="font-semibold text-slate-700" x-text="'Edit ' + day + ' Period ' + period"></h3>
        <button @click="close" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
      </div>
      <form method="POST" action="{{ route('academics.timetable.save') }}" class="p-4 space-y-3">
        @csrf
        <input type="hidden" name="class_id" :value="classId">
        <input type="hidden" name="entries[0][class_id]" :value="classId">
        <input type="hidden" name="entries[0][section_id]" :value="sectionId">
        <input type="hidden" name="entries[0][day_of_week]" :value="day">
        <input type="hidden" name="entries[0][period_number]" :value="period">
        <div>
          <label class="label">Period Type</label>
          <select name="entries[0][period_type]" class="select w-full" x-model="periodType">
            <option value="class">Class Session</option>
            <option value="break">Interval / Break</option>
            <option value="lunch">Lunch Break</option>
            <option value="free">Free / Study Period</option>
          </select>
        </div>
        <div x-show="periodType === 'class'">
          <label class="label">Subject *</label>
          <select name="entries[0][subject_id]" class="select w-full" x-model="subjectId">
            <option value="">— Select Subject —</option>
            @foreach($subjects as $sub)
            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
            @endforeach
          </select>
        </div>
        <div x-show="periodType === 'class'">
          <label class="label">Handling Teacher *</label>
          <select name="entries[0][teacher_id]" class="select w-full" x-model="teacherId">
            <option value="">— Select Handling Teacher —</option>
            @foreach($teachers as $t)
            <option value="{{ $t->id }}">{{ $t->full_name ?: $t->first_name . ' ' . $t->last_name }} ({{ $t->employee_code ?: 'EMP-' . $t->id }})</option>
            @endforeach
          </select>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="label">Start Time</label>
            <input type="time" name="entries[0][start_time]" class="input w-full" x-model="startTime">
          </div>
          <div>
            <label class="label">End Time</label>
            <input type="time" name="entries[0][end_time]" class="input w-full" x-model="endTime">
          </div>
        </div>
        <div class="flex gap-2 pt-2">
          <button type="submit" class="btn btn-primary flex-1">Save Period</button>
          <button type="button" @click="close" class="btn btn-secondary">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  @else
  <div class="card text-center py-12 text-slate-400">Select a class and section to view timetable.</div>
  @endif
</div>

@push('scripts')
<script>
function timetableEditor() {
  return {
    open: false, day: '', period: 0, classId: '', sectionId: '',
    subjectId: '', teacherId: '', periodType: 'class', startTime: '', endTime: '',
    openEditor(day, period, classId, sectionId, subjectId, teacherId, periodType, startTime, endTime) {
      this.day = day; this.period = period; this.classId = classId; this.sectionId = sectionId;
      this.subjectId = subjectId || ''; this.teacherId = teacherId || '';
      this.periodType = periodType || 'class'; this.startTime = startTime; this.endTime = endTime;
      this.open = true;
    },
    close() { this.open = false; },
  };
}
</script>
@endpush
@endsection
