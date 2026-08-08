@extends('layouts.app')
@section('title','Collect Fee')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
  <div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-4">
      <a href="{{ route('fees.index') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
      <h1 class="page-title">Collect Fee</h1>
    </div>
    <a href="{{ route('fees.advance-payments') }}" class="btn btn-secondary btn-sm">Advance Payment</a>
  </div>
  <form method="GET" class="card py-4">
    <div class="flex gap-3">
      <input type="text" name="student_id" value="{{ request('student_id') }}"
             placeholder="Name, Admission No, or ID…" class="input flex-1"
             list="student-suggestions" autocomplete="off">
      <datalist id="student-suggestions">
        @foreach($studentSuggestions as $s)
          <option value="{{ $s->admission_no }}">{{ $s->first_name }} {{ $s->last_name }} ({{ $s->admission_no }})</option>
        @endforeach
      </datalist>
      <button type="submit" class="btn btn-secondary">Search</button>
    </div>
  </form>
  @if($student)
  <div class="card">
    <div class="flex items-center gap-4 mb-4 pb-3 border-b border-slate-100">
      <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center"><span class="text-white font-bold">{{ strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1)) }}</span></div>
      <div><p class="font-semibold text-slate-800">{{ $student->full_name }}</p><p class="text-sm text-slate-500">{{ $student->admission_number }} &bull; {{ $student->currentEnrollment?->class?->name }}</p></div>
    </div>
    @if($dues->count())
    @php
      $duesMap = $dues->pluck('balance', 'fee_head_id')->toArray();
    @endphp
    <form method="POST" action="{{ route('fees.collect.save') }}" class="space-y-4" x-data="feeCollect({{ json_encode($duesMap) }})">
      @csrf
      <input type="hidden" name="student_id" value="{{ $student->id }}">
      <div><label class="label">Fee Head <span class="text-red-500">*</span></label>
        <select name="fee_head_id" class="select" @change="fillBalance($event.target.value)">
          @foreach($dues as $d)<option value="{{ $d->fee_head_id }}">{{ $d->feeHead?->name }} — ₹{{ number_format($d->balance,2) }} due</option>@endforeach
        </select>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="label">Amount <span class="text-red-500">*</span></label><input type="number" name="amount" class="input" min="1" step="0.01" x-ref="amount"></div>
        <div><label class="label">Late Fee</label><input type="number" name="late_fee" value="0" class="input" min="0" step="0.01"></div>
        <div><label class="label">Discount</label><input type="number" name="discount" value="0" class="input" min="0" step="0.01"></div>
        <div><label class="label">Payment Date <span class="text-red-500">*</span></label><input type="date" name="payment_date" value="{{ today()->toDateString() }}" class="input"></div>
      </div>
      <div x-data="{mode:'cash'}">
        <label class="label">Payment Mode <span class="text-red-500">*</span></label>
        <select name="payment_mode" class="select" x-model="mode">
          @foreach(['cash'=>'Cash','cheque'=>'Cheque','dd'=>'DD','online'=>'Online','upi'=>'UPI'] as $v=>$l)<option value="{{ $v }}">{{ $l }}</option>@endforeach
        </select>
        {{-- Cheque fields --}}
        <div x-show="mode==='cheque' || mode==='dd'" x-transition class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="label">Cheque / DD Number <span class="text-red-500">*</span></label>
            <input type="text" name="cheque_number" class="input" placeholder="Cheque number">
          </div>
          <div>
            <label class="label">Cheque Date</label>
            <input type="date" name="cheque_date" class="input">
          </div>
          <div>
            <label class="label">Bank</label>
            <input type="text" name="cheque_bank" class="input" placeholder="Bank name">
          </div>
          <div>
            <label class="label">Branch</label>
            <input type="text" name="cheque_branch" class="input" placeholder="Branch name">
          </div>
        </div>
        {{-- Online/UPI transaction ID --}}
        <div x-show="mode==='online' || mode==='upi' || mode==='dd'" x-transition class="mt-3">
          <label class="label">Transaction ID</label>
          <input type="text" name="transaction_id" class="input" placeholder="UTR / Transaction reference">
        </div>
      </div>
      <div><label class="label">Remarks</label><textarea name="remarks" class="input resize-none" rows="2"></textarea></div>
      <div class="flex justify-end gap-3"><a href="{{ route('fees.index') }}" class="btn btn-secondary">Cancel</a><button type="submit" class="btn btn-primary">Record Payment</button></div>
    </form>
    @else
    <p class="text-slate-500 text-sm">No fee dues for this student.</p>
    @endif
  </div>
  @endif
</div>
@push('scripts')
<script>
function feeCollect(dues) {
  return {
    dues,
    fillBalance(feeHeadId) {
      const bal = this.dues[feeHeadId];
      if (bal !== undefined) {
        this.$refs.amount.value = parseFloat(bal).toFixed(2);
      }
    },
    init() {
      // Auto-fill on page load with first option
      const sel = this.$el.querySelector('select[name="fee_head_id"]');
      if (sel) this.fillBalance(sel.value);
    }
  };
}
</script>
@endpush
@endsection
