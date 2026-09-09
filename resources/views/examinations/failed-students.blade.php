@extends('layouts.app')
@section('title', 'Failed Students Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Failed Students Report</h1>
      <p class="page-subtitle">Students who failed in one or more subjects with subject-wise deficit tracking</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('examinations.class-result', request()->only('exam_id','class_id')) }}" class="btn btn-secondary btn-sm">Class Result Summary</a>
      <a href="{{ route('examinations.index') }}" class="btn btn-secondary btn-sm">Back to Exams</a>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card bg-slate-50 border border-slate-200">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label font-semibold text-xs text-slate-700">Exam *</label>
        <select name="exam_id" class="select text-sm py-1.5" required onchange="this.form.submit()">
          <option value="">— Select Exam —</option>
          @foreach($exams as $e)
          <option value="{{ $e->id }}" {{ request('exam_id') == $e->id ? 'selected' : '' }}>{{ $e->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label font-semibold text-xs text-slate-700">Class (Optional)</label>
        <select name="class_id" class="select text-sm py-1.5" onchange="this.form.submit()">
          <option value="">All Classes</option>
          @foreach($classes as $c)
          <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label font-semibold text-xs text-slate-700">Subject Filter</label>
        <select name="subject_id" class="select text-sm py-1.5 min-w-[200px]" onchange="this.form.submit()">
          <option value="">All Subjects (Any Failure)</option>
          @if(isset($subjects) && $subjects->isNotEmpty())
          @foreach($subjects as $sub)
          <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
          @endforeach
          @endif
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Filter</button>
      @if(request('exam_id'))
      <a href="{{ route('examinations.failed.pdf', request()->only('exam_id','class_id','subject_id')) }}"
         target="_blank" class="btn btn-secondary btn-sm">PDF Export</a>
      <a href="{{ route('examinations.failed.excel', request()->only('exam_id','class_id','subject_id')) }}"
         class="btn btn-secondary btn-sm">Excel Export</a>
      @endif
    </form>
  </div>

  {{-- Subject Failure Breakdown Badges / KPIs --}}
  @if(isset($subjectFailureCounts) && $subjectFailureCounts->isNotEmpty())
  <div class="card bg-red-50/50 border border-red-100">
    <div class="flex items-center justify-between mb-3 flex-wrap gap-2">
      <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
        <h3 class="font-bold text-slate-800 text-sm">Subject-wise Failure Analysis</h3>
      </div>
      <span class="text-xs text-slate-500">Click a subject below to filter failed students</span>
    </div>
    <div class="flex flex-wrap gap-2">
      @foreach($subjectFailureCounts as $subName => $count)
      <a href="{{ route('examinations.failed', array_merge(request()->only('exam_id', 'class_id'), ['subject_id' => $subjects->firstWhere('name', $subName)?->id])) }}"
         class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border transition text-xs font-semibold {{ (isset($selectedSubject) && $selectedSubject->name == $subName) ? 'bg-red-600 text-white border-red-700 shadow-sm' : 'bg-white text-slate-700 border-red-200 hover:bg-red-100' }}">
        <span>{{ $subName }}</span>
        <span class="px-1.5 py-0.2 rounded-full text-[11px] font-bold {{ (isset($selectedSubject) && $selectedSubject->name == $subName) ? 'bg-white text-red-600' : 'bg-red-100 text-red-700' }}">{{ $count }}</span>
      </a>
      @endforeach
    </div>
  </div>
  @endif

  @if($failed->isNotEmpty())
  @php $suppCount = $failed->where('supp_eligible', true)->count(); @endphp
  <div class="card p-0 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3 bg-slate-50/50">
      <div>
        <h2 class="font-bold text-slate-800 text-base">
          Failed Students Roster
          <span class="ml-2 px-2.5 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full">{{ $failed->count() }} Student{{ $failed->count() > 1 ? 's' : '' }}</span>
          @if(isset($selectedSubject))
          <span class="ml-1 text-xs font-semibold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded-full">Subject: {{ $selectedSubject->name }}</span>
          @endif
        </h2>
        <p class="text-xs text-slate-500 mt-0.5">
          Supplementary Eligible (&le;{{ $threshold }} subjects failed):
          <span class="font-bold text-amber-600">{{ $suppCount }}</span> &bull; Remedial Needed:
          <span class="font-bold text-red-600">{{ $failed->count() - $suppCount }}</span>
        </p>
      </div>
      @if(isset($selectedSubject))
      <a href="{{ route('examinations.failed', request()->only('exam_id', 'class_id')) }}" class="btn btn-secondary btn-sm">Show All Subjects</a>
      @endif
    </div>

    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th text-center" style="width: 60px;">#</th>
            <th class="th">Student Name</th>
            <th class="th">Adm No</th>
            <th class="th">Subject Details &amp; Mark Deficit</th>
            <th class="th text-center">Failed Count</th>
            <th class="th text-center">Supp. Eligible</th>
            <th class="th text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($failed as $i => $row)
          <tr class="tr hover:bg-slate-50 transition">
            <td class="td text-center text-slate-400 font-semibold">{{ $i + 1 }}</td>
            <td class="td font-medium">
              <a href="{{ route('examinations.student-result-history', ['student_id' => $row['student']->id]) }}" class="text-indigo-600 hover:underline font-semibold">
                {{ $row['student']->first_name }} {{ $row['student']->last_name }}
              </a>
            </td>
            <td class="td text-slate-500 font-mono text-xs">{{ $row['student']->admission_no ?? $row['student']->admission_number ?? '—' }}</td>
            <td class="td">
              <div class="flex flex-wrap gap-2">
                @if(isset($row['subject_details']) && $row['subject_details']->isNotEmpty())
                  @foreach($row['subject_details'] as $sd)
                  <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-50 border border-red-200 text-xs">
                    <strong class="text-red-900 font-semibold">{{ $sd['subject_name'] }}:</strong>
                    @if($sd['is_absent'])
                      <span class="text-amber-700 font-bold">Absent</span>
                    @else
                      <span class="text-red-700 font-bold">{{ $sd['marks_obtained'] }}/{{ $sd['max_marks'] }}</span>
                      <span class="text-slate-500 text-[11px]">(Pass: {{ $sd['pass_marks'] }})</span>
                      @if($sd['deficit'] > 0)
                      <span class="px-1.5 py-0.2 rounded bg-red-200 text-red-800 font-bold text-[10px]">-{{ $sd['deficit'] }}</span>
                      @endif
                    @endif
                  </div>
                  @endforeach
                @else
                  @foreach($row['subjects'] as $subject)
                    <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full">{{ $subject }}</span>
                  @endforeach
                @endif
              </div>
            </td>
            <td class="td text-center">
              <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-red-100 text-red-700 font-extrabold text-xs">
                {{ $row['subjects']->count() }}
              </span>
            </td>
            <td class="td text-center">
              @if($row['supp_eligible'])
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">
                  <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  Eligible
                </span>
              @else
                <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-500 text-xs rounded-full">Remedial</span>
              @endif
            </td>
            <td class="td text-right">
              <div class="flex items-center justify-end gap-2">
                <a href="{{ route('examinations.student-result-history', ['student_id' => $row['student']->id]) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                  Report Card &rarr;
                </a>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @elseif(request('exam_id'))
  <div class="card text-center py-12 text-slate-400 bg-green-50/50 border border-green-200">
    <svg class="w-12 h-12 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <p class="font-bold text-green-800 text-base">Congratulations! No failed students found.</p>
    <p class="text-xs text-green-600 mt-1">All students passed in the selected criteria.</p>
  </div>
  @else
  <div class="card text-center py-12 text-slate-400">
    <p class="font-medium text-slate-600">Please select an Exam to view the Failed Students report.</p>
  </div>
  @endif
</div>
@endsection
