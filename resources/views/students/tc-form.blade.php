@extends('layouts.app')

@section('title', 'Generate Leaving Certificate — ' . $student->full_name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-16">

  {{-- Back --}}
  <a href="{{ route('students.show', $student->id) }}" class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 font-semibold transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to Student Profile
  </a>

  {{-- Header Card --}}
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
    <div class="flex items-center gap-4 mb-6">
      <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center shrink-0">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      </div>
      <div>
        <h1 class="text-xl font-extrabold text-slate-900">Generate Leaving Certificate (TC)</h1>
        <p class="text-sm text-slate-500 mt-1">Fill in the details below before generating the certificate for <strong class="text-slate-700">{{ $student->full_name }}</strong></p>
      </div>
    </div>

    {{-- Student Quick Info --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
      <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Student Name</span>
        <span class="text-sm font-bold text-slate-900 block mt-0.5">{{ $student->full_name }}</span>
      </div>
      <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Admission No</span>
        <span class="text-sm font-bold text-slate-900 block mt-0.5">{{ $student->admission_no ?? '—' }}</span>
      </div>
      <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Class</span>
        <span class="text-sm font-bold text-slate-900 block mt-0.5">{{ $enrollment?->class?->name ?? '—' }} @if($enrollment?->section)- {{ $enrollment->section->name }}@endif</span>
      </div>
      <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Father's Name</span>
        <span class="text-sm font-bold text-slate-900 block mt-0.5">{{ $student->father_name ?? '—' }}</span>
      </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('students.tc', $student->id) }}" method="POST" target="_blank">
      @csrf

      <div class="space-y-5">

        {{-- Date of Leaving --}}
        <div>
          <label for="leaving_date" class="block text-sm font-bold text-slate-700 mb-1.5">Date of Leaving School <span class="text-red-500">*</span></label>
          <input type="date" name="leaving_date" id="leaving_date" value="{{ now()->format('Y-m-d') }}"
                 class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
        </div>

        {{-- Reason for Leaving --}}
        <div>
          <label for="reason" class="block text-sm font-bold text-slate-700 mb-1.5">Reason for Leaving School <span class="text-red-500">*</span></label>
          <select name="reason" id="reason" onchange="toggleCustomReason(this.value)"
                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
            <option value="">— Select Reason —</option>
            <option value="Parent's Request">Parent's Request</option>
            <option value="Higher Studies">Higher Studies</option>
            <option value="Transfer to another school">Transfer to another school</option>
            <option value="Family relocation">Family relocation</option>
            <option value="Completion of studies">Completion of studies</option>
            <option value="Health reasons">Health reasons</option>
            <option value="Financial reasons">Financial reasons</option>
            <option value="Other">Other</option>
          </select>
        </div>

        {{-- Custom Reason Input (Shown when "Other" is selected) --}}
        <div id="custom_reason_wrapper" class="hidden space-y-1">
          <label for="custom_reason" class="block text-sm font-bold text-indigo-700 mb-1.5">Specify Custom Reason <span class="text-red-500">*</span></label>
          <input type="text" name="custom_reason" id="custom_reason" placeholder="Type reason for leaving here..."
                 class="w-full rounded-xl border border-indigo-300 bg-indigo-50/30 px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
        </div>

        {{-- 2-col: Conduct & Progress --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="conduct" class="block text-sm font-bold text-slate-700 mb-1.5">Conduct</label>
            <select name="conduct" id="conduct"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
              <option value="Excellent">Excellent</option>
              <option value="Good" selected>Good</option>
              <option value="Satisfactory">Satisfactory</option>
              <option value="Needs Improvement">Needs Improvement</option>
            </select>
          </div>
          <div>
            <label for="progress" class="block text-sm font-bold text-slate-700 mb-1.5">Progress</label>
            <select name="progress" id="progress"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
              <option value="Excellent">Excellent</option>
              <option value="Good" selected>Good</option>
              <option value="Satisfactory">Satisfactory</option>
              <option value="Needs Improvement">Needs Improvement</option>
            </select>
          </div>
        </div>

        {{-- Remarks --}}
        <div>
          <label for="remarks" class="block text-sm font-bold text-slate-700 mb-1.5">Remarks</label>
          <textarea name="remarks" id="remarks" rows="3" placeholder="E.g. Student conducted well. Character and Conduct are Good."
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none">Student conducted well. Character and Conduct are Good.</textarea>
        </div>

      </div>

      {{-- Actions --}}
      <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-slate-200">
        <a href="{{ route('students.show', $student->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-600 hover:bg-slate-50 transition">Cancel</a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-bold shadow-md hover:shadow-lg hover:from-indigo-700 hover:to-purple-700 transition flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          Generate &amp; Download TC
        </button>
      </div>

    </form>
  </div>

</div>

<script>
function toggleCustomReason(val) {
  const wrapper = document.getElementById('custom_reason_wrapper');
  const input = document.getElementById('custom_reason');
  if (val === 'Other') {
    wrapper.classList.remove('hidden');
    input.focus();
  } else {
    wrapper.classList.add('hidden');
    input.value = '';
  }
}
</script>
@endsection
