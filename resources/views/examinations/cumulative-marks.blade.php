@extends('layouts.app')
@section('title', 'Cumulative Marks')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Cumulative Marks — Term-wise</h1>
  <p class="text-slate-500 text-sm">Combines marks from all term exams using configured weightages to compute a final cumulative score.</p>

  @if($termExams->isEmpty())
  <div class="alert-amber">
    No exams are marked as "cumulative components" for the current year.
    To enable: when creating/editing an exam, check "Include in Cumulative" and set a weightage %.
  </div>
  @endif

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label">Class *</label>
        <select name="class_id" required class="select w-40">
          <option value="">Select Class</option>
          @foreach($classes as $c)
            <option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Calculate</button>
    </div>
  </form>

  @if($termExams->count())
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex flex-wrap gap-4">
      @foreach($termExams as $e)
      <div class="text-xs">
        <span class="font-medium text-slate-700">{{ $e->name }}</span>
        <span class="text-slate-400 ml-1">({{ $e->weightage_percent }}% weight)</span>
      </div>
      @endforeach
    </div>
    @if($result->count())
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        <th class="th">Rank</th>
        <th class="th">Student</th>
        @foreach($termExams as $e)
        <th class="th text-center">{{ $e->term_label ?: $e->name }}<div class="text-xs text-slate-400 font-normal">{{ $e->weightage_percent }}%</div></th>
        @endforeach
        <th class="th text-center">Cumulative %</th>
        <th class="th">Grade</th>
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($result as $i => $row)
        @php
          $cum = $row['cumulative'];
          $grade = match(true) {
            $cum >= 91 => 'A1', $cum >= 81 => 'A2', $cum >= 71 => 'B1',
            $cum >= 61 => 'B2', $cum >= 51 => 'C1', $cum >= 41 => 'C2',
            $cum >= 33 => 'D', default => 'E'
          };
        @endphp
        <tr class="hover:bg-slate-50">
          <td class="td text-center font-bold text-slate-400">{{ $i + 1 }}</td>
          <td class="td font-medium text-slate-800">{{ $row['student']?->full_name }}
            <div class="text-xs text-slate-400">{{ $row['student']?->admission_number }}</div>
          </td>
          @foreach($termExams as $e)
          @php $ts = $row['term_scores'][$e->id] ?? ['pct'=>0,'weighted'=>0]; @endphp
          <td class="td text-center">
            <div class="font-medium text-slate-700">{{ $ts['pct'] }}%</div>
            <div class="text-xs text-slate-400">+{{ $ts['weighted'] }}</div>
          </td>
          @endforeach
          <td class="td text-center">
            <span class="font-bold text-lg {{ $cum >= 60 ? 'text-green-600' : ($cum >= 33 ? 'text-amber-600' : 'text-red-500') }}">{{ $cum }}%</span>
          </td>
          <td class="td"><span class="badge-{{ $cum >= 60 ? 'green' : ($cum >= 33 ? 'amber' : 'red') }}">{{ $grade }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @elseif(request('class_id'))
    <div class="px-4 py-8 text-center text-slate-400">No marks data found for this class.</div>
    @endif
  </div>
  @endif
</div>
@endsection
