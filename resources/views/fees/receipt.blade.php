@extends('layouts.app')
@section('title','Receipt — ' . $payment->receipt_number)
@section('content')
<div class="max-w-xl mx-auto space-y-6">
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-3"><a href="{{ route('fees.index') }}" class="btn-icon"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a><h1 class="page-title">Fee Receipt</h1></div>
    <div class="flex gap-2">
      <a href="{{ route('fees.receipt.duplicate', $payment->id) }}" class="btn btn-secondary btn-sm" target="_blank">Duplicate PDF</a>
      <button onclick="window.print()" class="btn btn-primary btn-sm">Print</button>
    </div>
  </div>
  <div class="card p-8" id="receipt-print">
    <div class="text-center border-b-2 border-slate-800 pb-4 mb-5">
      <h2 class="text-xl font-extrabold text-slate-800">FEE RECEIPT</h2>
      <p class="text-xs text-slate-500">DASA EduERP</p>
    </div>
    <div class="flex justify-between text-sm mb-4">
      <span class="text-slate-500">Receipt No:</span><span class="font-mono font-semibold text-blue-600">{{ $payment->receipt_number }}</span>
    </div>
    <table class="w-full text-sm border-collapse">
      @php $rows=[['Student',$payment->student?->full_name],['Admission No.',$payment->student?->admission_number],['Class',$payment->student?->currentEnrollment?->class?->name],['Fee Head',$payment->feeHead?->name],['Payment Date',\Carbon\Carbon::parse($payment->payment_date)->format('d M Y')],['Payment Mode',ucfirst($payment->payment_mode)],['Amount','₹'.number_format($payment->amount,2)],['Late Fee','₹'.number_format($payment->late_fee,2)],['Discount','₹'.number_format($payment->discount,2)],['Total Paid','₹'.number_format($payment->total_paid,2)],['Collected By',$payment->collectedBy?->name]]; @endphp
      @foreach($rows as [$l,$v])<tr class="border-b border-slate-100"><td class="py-2 text-slate-500 pr-4 w-2/5">{{ $l }}</td><td class="py-2 font-medium text-slate-800">{{ $v ?? '—' }}</td></tr>@endforeach
    </table>
    <div class="mt-8 text-right"><div class="border-t border-slate-400 inline-block pt-2 text-xs text-slate-500">Signature / Seal</div></div>
  </div>
</div>
@endsection
