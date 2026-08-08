@extends('layouts.app')
@section('title','Bank Transfer Export')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Bank Transfer / NEFT Export</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Generate Transfer File</h3>
      <form method="GET" action="{{ route('hr.payroll.bank-transfer') }}" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div><label class="label">Month <span class="text-red-500">*</span></label>
            <select name="month" class="select" required>
              @for($m=1;$m<=12;$m++)
              <option value="{{ $m }}" @selected(($payrollMonth??now()->month)==$m)>{{ \Carbon\Carbon::create(null,$m)->format('F') }}</option>
              @endfor
            </select>
          </div>
          <div><label class="label">Year <span class="text-red-500">*</span></label>
            <select name="year" class="select" required>
              @for($y=now()->year-1;$y<=now()->year+1;$y++)
              <option value="{{ $y }}" @selected(($payrollYear??now()->year)==$y)>{{ $y }}</option>
              @endfor
            </select>
          </div>
        </div>
        <div><label class="label">Bank</label>
          <select name="bank" class="select">
            <option value="">All Banks</option>
            @foreach($banks??[] as $b)<option value="{{ $b }}">{{ $b }}</option>@endforeach
          </select>
        </div>
        <div><label class="label">Format</label>
          <select name="format" class="select">
            <option value="neft">NEFT/Generic CSV</option>
            <option value="sbi">SBI (Pipe-delimited)</option>
            <option value="hdfc">HDFC NetBanking</option>
            <option value="icici">ICICI Corporate</option>
            <option value="axis">Axis Bank</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Download Transfer File</button>
      </form>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Summary Preview</h3>
      @if(isset($payrollSummary))
      <div class="space-y-2">
        <div class="flex justify-between text-sm"><span class="text-slate-500">Total Employees</span><span class="font-semibold">{{ $payrollSummary->count() }}</span></div>
        <div class="flex justify-between text-sm"><span class="text-slate-500">Total Net Pay</span><span class="font-bold text-slate-800">₹{{ number_format($payrollSummary->sum('net_salary'),2) }}</span></div>
        <div class="border-t border-slate-100 pt-2 space-y-1">
          @foreach($payrollSummary->groupBy(fn($p)=>$p->employee?->bank_name) as $bank=>$rows)
          <div class="flex justify-between text-xs text-slate-500">
            <span>{{ $bank ?: 'Unknown Bank' }}</span>
            <span>{{ $rows->count() }} records | ₹{{ number_format($rows->sum('net_salary'),2) }}</span>
          </div>
          @endforeach
        </div>
      </div>
      @else
      <p class="text-slate-400 text-sm text-center py-8">Select month and year to preview.</p>
      @endif
    </div>
  </div>
</div>
@endsection
