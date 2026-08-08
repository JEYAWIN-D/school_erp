@extends('layouts.app')
@section('title', 'Issue Outpass')
@section('content')
<div class="max-w-xl space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Issue Student Outpass</h1>
    <a href="{{ route('gate.outpass') }}" class="btn-sm btn-secondary">← Outpass</a>
  </div>

  <form method="POST" action="{{ route('gate.outpass.store') }}" class="card space-y-4">
    @csrf
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">Pass Number</label>
        <input type="text" name="pass_number" value="{{ $passNumber }}" class="input" readonly>
      </div>
      <div>
        <label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select" required>
          <option value="">Select student</option>
          @foreach($students as $s)
          <option value="{{ $s->id }}">{{ $s->first_name }} {{ $s->last_name }} ({{ $s->admission_number }})</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Out Time <span class="text-red-500">*</span></label>
        <input type="datetime-local" name="out_time" value="{{ now()->format('Y-m-d\TH:i') }}" class="input" required>
      </div>
      <div>
        <label class="label">Expected Return</label>
        <input type="datetime-local" name="expected_return" class="input">
      </div>
      <div class="col-span-2">
        <label class="label">Reason <span class="text-red-500">*</span></label>
        <input type="text" name="reason" class="input" required>
      </div>
      <div class="col-span-2">
        <label class="label">Authorized By <span class="text-red-500">*</span></label>
        <input type="text" name="authorized_by" class="input" placeholder="Class teacher / HOD name" required>
      </div>
    </div>

    @if($errors->any()) <div class="alert-danger text-sm">{{ $errors->first() }}</div> @endif

    <div class="flex gap-2">
      <button type="submit" class="btn-primary">Issue Outpass</button>
      <a href="{{ route('gate.outpass') }}" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
