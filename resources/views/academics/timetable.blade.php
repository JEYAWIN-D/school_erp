@extends('layouts.app')
@section('title', 'Timetable')
@section('content')
<div class="space-y-6" x-data="timetableEditor()" @keydown.escape.window="close()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Timetable</h1>
    <div class="flex gap-2">
      <a href="{{ route('academics.substitutions') }}" class="btn btn-secondary btn-sm">Substitutions</a>
      <span class="btn btn-secondary btn-sm text-slate-400 border-slate-200 cursor-not-allowed" title="Conflict checker coming soon">Check Conflicts</span>
      @if(request('class_id'))
        <a href="{{ route('academics.timetable.pdf', request()->only(['class_id','section_id'])) }}" target="_blank" class="btn btn-secondary btn-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
          PDF
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
  <p class="text-xs text-slate-400">Click any cell to edit that period.</p>
  @endif
  <div class="card overflow-x-auto">
    @php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']; @endphp
    <table class="min-w-full text-sm border-collapse">
      <thead><tr class="bg-slate-50">
        <th class="border border-slate-200 px-3 py-2 text-slate-500 font-medium w-24">Day</th>
        @php $periods = range(1, $timetable->flatten()->max('period_number') ?: 8); @endphp
        @foreach($periods as $p)<th class="border border-slate-200 px-3 py-2 text-slate-500 font-medium text-center">P{{ $p }}</th>@endforeach
      </tr></thead>
      <tbody>
        @foreach($days as $day)
        @php $daySlots = $timetable[$day] ?? collect(); @endphp
        <tr class="hover:bg-slate-50">
          <td class="border border-slate-200 px-3 py-2 font-semibold text-slate-600">{{ $day }}</td>
          @foreach($periods as $p)
          @php $slot = $daySlots->firstWhere('period_number', $p); $type = $slot?->period_type ?? 'class'; @endphp
          <td class="border border-slate-200 px-2 py-2 text-center cursor-pointer hover:bg-indigo-50 transition group
            {{ $type === 'break' ? 'bg-amber-50' : ($type === 'lunch' ? 'bg-green-50' : ($type === 'free' ? 'bg-slate-50' : '')) }}"
            @click="openEditor('{{ $day }}', {{ $p }}, '{{ request('class_id') }}', '{{ request('section_id') }}', {{ $slot ? "'{$slot->subject_id}'" : 'null' }}, {{ $slot ? "'{$slot->employee_id}'" : 'null' }}, '{{ $slot?->period_type ?? 'class' }}', '{{ $slot?->start_time ?? '' }}', '{{ $slot?->end_time ?? '' }}')">
            @if($slot)
              @if(in_array($type, ['break','lunch','free']))
                <span class="text-xs font-semibold {{ $type === 'break' ? 'text-amber-600' : ($type === 'lunch' ? 'text-green-700' : 'text-slate-400') }}">
                  {{ ucfirst($type) }}
                </span>
                @if($slot->start_time && $slot->end_time)
                <span class="block text-xs text-slate-400">{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i') }}–{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i') }}</span>
                @endif
              @else
                <span class="text-slate-800 font-medium block text-xs">{{ $slot->subject?->name }}</span>
                <span class="text-slate-400 text-xs">{{ $slot->teacher?->first_name }}</span>
              @endif
            @else
              <span class="text-slate-200 group-hover:text-indigo-300 text-xs">+ Add</span>
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
        <h3 class="font-semibold text-slate-700" x-text="'Edit ' + day + ' P' + period"></h3>
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
            <option value="class">Class</option>
            <option value="break">Break</option>
            <option value="lunch">Lunch</option>
            <option value="free">Free Period</option>
          </select>
        </div>
        <div x-show="periodType === 'class'">
          <label class="label">Subject</label>
          <select name="entries[0][subject_id]" class="select w-full" x-model="subjectId">
            <option value="">— Select Subject —</option>
            @foreach($subjects as $sub)
            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
            @endforeach
          </select>
        </div>
        <div x-show="periodType === 'class'">
          <label class="label">Teacher</label>
          <select name="entries[0][employee_id]" class="select w-full" x-model="teacherId">
            <option value="">— Select Teacher —</option>
            @foreach($teachers as $t)
            <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }}</option>
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
        <div class="flex gap-2 pt-1">
          <button type="submit" class="btn-primary flex-1">Save</button>
          <button type="button" @click="close" class="btn-secondary">Cancel</button>
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
