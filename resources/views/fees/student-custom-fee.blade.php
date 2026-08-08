@extends('layouts.app')
@section('title', 'Individual Student Custom Fee')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Individual Student Custom Fee</h1>
  <p class="text-slate-500 text-sm">Override the standard fee amount for a specific student and fee head.</p>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Search + Set Form --}}
    <div class="card space-y-4">
      <h2 class="font-semibold text-slate-800">Set Custom Fee</h2>
      <form method="GET" class="space-y-3">
        <div>
          <label class="label">Student ID</label>
          <input type="number" name="student_id" value="{{ request('student_id') }}" class="input w-full" placeholder="Enter student ID to look up">
        </div>
        <button type="submit" class="btn btn-secondary btn-sm">Load Student</button>
      </form>

      @if($student)
      <div class="bg-slate-50 rounded-lg p-3">
        <div class="font-medium text-slate-800">{{ $student->full_name }}</div>
        <div class="text-xs text-slate-500">{{ $student->admission_number }} | {{ $student->currentEnrollment?->class?->name }}</div>
      </div>

      <form method="POST" action="{{ route('fees.student-custom-fee.save') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="student_id" value="{{ $student->id }}">
        <div>
          <label class="label">Fee Head *</label>
          <select name="fee_head_id" required class="select w-full">
            <option value="">Select Fee Head</option>
            @foreach($feeHeads as $h)
              <option value="{{ $h->id }}">{{ $h->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Custom Amount (₹) *</label>
          <input type="number" name="amount" min="0" step="0.01" required class="input w-full" placeholder="0.00">
        </div>
        <div>
          <label class="label">Reason</label>
          <input type="text" name="reason" class="input w-full" placeholder="Reason for custom fee...">
        </div>
        <button type="submit" class="btn btn-primary btn-sm w-full">Save Custom Fee</button>
      </form>
      @endif
    </div>

    {{-- Current custom fees --}}
    <div class="card">
      <h2 class="font-semibold text-slate-800 mb-4">
        @if($student) Custom Fees for {{ $student->full_name }} @else Custom Fees @endif
        @if($currentYear) <span class="text-xs text-slate-400 font-normal ml-1">({{ $currentYear->name }})</span>@endif
      </h2>
      @if($customFees->count())
      <div class="space-y-2">
        @foreach($customFees as $cf)
        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
          <div>
            <div class="font-medium text-sm text-slate-800">{{ $cf->feeHead?->name }}</div>
            @if($cf->reason)<div class="text-xs text-slate-500">{{ $cf->reason }}</div>@endif
          </div>
          <div class="text-right">
            <div class="font-bold text-slate-800">₹{{ number_format($cf->custom_amount, 2) }}</div>
            <div class="text-xs text-slate-400">Set {{ $cf->updated_at?->format('d M') }}</div>
          </div>
        </div>
        @endforeach
      </div>
      @elseif($student)
      <p class="text-slate-400 text-sm">No custom fees set for this student yet.</p>
      @else
      <p class="text-slate-400 text-sm">Search for a student above to view their custom fees.</p>
      @endif
    </div>
  </div>
</div>
@endsection
