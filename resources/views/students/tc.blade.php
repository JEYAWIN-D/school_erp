@extends('layouts.app')

@section('title', 'Transfer Certificate — ' . $student->full_name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

  <div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
      <a href="{{ route('students.show', $student->id) }}" class="btn-icon">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <h1 class="page-title">Transfer Certificate</h1>
    </div>
    <button onclick="window.print()" class="btn btn-primary btn-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
      Print TC
    </button>
  </div>

  <div id="tc-print" class="card p-8 print:shadow-none print:border-none">
    <div class="text-center border-b-2 border-slate-800 pb-4 mb-6">
      <h2 class="text-2xl font-extrabold text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">TRANSFER CERTIFICATE</h2>
      <p class="text-sm text-slate-500 mt-1">DASA EduERP</p>
    </div>

    <table class="w-full text-sm border-collapse">
      @php
        $rows = [
          ['Sr. No.', 'TC-' . date('Y') . '-' . str_pad($student->id, 4, '0', STR_PAD_LEFT)],
          ['Admission No.', $student->admission_number],
          ['Student\'s Name', strtoupper($student->full_name)],
          ['Father\'s Name', strtoupper($student->father_name)],
          ['Mother\'s Name', strtoupper($student->mother_name)],
          ['Date of Birth', $student->dob?->format('d M Y')],
          ['Class Last Studied', $student->currentEnrollment?->class?->name ?? '—'],
          ['Date of Admission', $student->admission_date?->format('d M Y')],
          ['Date of Leaving', now()->format('d M Y')],
          ['Reason for Leaving', 'Parent\'s Request'],
          ['Conduct', 'Good'],
          ['Fee Dues', 'NIL'],
        ];
      @endphp
      @foreach($rows as [$label, $value])
        <tr class="border-b border-slate-200">
          <td class="py-2.5 pr-4 font-semibold text-slate-600 w-2/5">{{ $label }}</td>
          <td class="py-2.5 text-slate-800">{{ $value }}</td>
        </tr>
      @endforeach
    </table>

    <div class="mt-10 grid grid-cols-3 text-center text-sm text-slate-600">
      <div><div class="border-t border-slate-400 pt-2 mt-8">Class Teacher</div></div>
      <div><div class="border-t border-slate-400 pt-2 mt-8">Accountant</div></div>
      <div><div class="border-t border-slate-400 pt-2 mt-8">Principal</div></div>
    </div>
  </div>

</div>
@endsection
