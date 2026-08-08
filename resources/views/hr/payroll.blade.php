@extends('layouts.app')
@section('title', 'Payroll')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Payroll</h1>
    <div class="flex gap-2">
        <a href="{{ route('hr.payroll.department-summary', ['month' => $month]) }}" class="btn btn-secondary">Dept Summary</a>
        <a href="{{ route('hr.payroll.bulk-payslip', ['month' => $month]) }}" class="btn btn-secondary">Bulk Payslip PDF</a>
    </div>
  </div>
  <div class="card-flat py-4">
    <div class="flex flex-wrap gap-3">
      <form method="GET" class="flex gap-2">
        <input type="month" name="month" value="{{ $month }}" class="input w-44">
        <button type="submit" class="btn btn-secondary btn-sm">Load</button>
      </form>
      <form method="POST" action="{{ route('hr.payroll.process') }}">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <button type="submit" class="btn btn-primary btn-sm">Generate Payroll</button>
      </form>
      @if($records->where('status', 'draft')->count() > 0)
      <form method="POST" action="{{ route('hr.payroll.approve') }}">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approve and lock all payroll for {{ $month }}?')">
          Approve &amp; Lock Payroll
        </button>
      </form>
      @endif
    </div>
  </div>
  @if($records->where('is_locked', true)->count() > 0)
  <div class="alert-warning text-sm">
    {{ $records->where('is_locked', true)->count() }} record(s) are locked for {{ $month }}.
  </div>
  @endif
  <div class="table-wrap">
    <table class="w-full">
      <thead><tr><th class="th">Employee</th><th class="th">Working Days</th><th class="th">Present</th><th class="th">Gross</th><th class="th">Deductions</th><th class="th">Net Salary</th><th class="th">Status</th><th class="th">Actions</th></tr></thead>
      <tbody>
        @forelse($records as $r)
          <tr class="tr {{ $r->is_locked ? 'bg-slate-50' : '' }}">
            <td class="td font-medium">{{ $r->employee?->first_name }} {{ $r->employee?->last_name }}</td>
            <td class="td">{{ $r->working_days }}</td>
            <td class="td">{{ $r->present_days }}</td>
            <td class="td">₹{{ number_format($r->gross_salary, 2) }}</td>
            <td class="td">₹{{ number_format($r->deductions, 2) }}</td>
            <td class="td font-semibold text-green-700">₹{{ number_format($r->net_salary, 2) }}</td>
            <td class="td"><span class="{{ $r->status === 'paid' ? 'badge-green' : ($r->status === 'approved' ? 'badge-blue' : 'badge-slate') }}">{{ ucfirst($r->status) }}</span></td>
            <td class="td">
              <div class="flex flex-wrap gap-1">
                <a href="{{ route('hr.payroll.payslip', $r->id) }}" class="btn btn-ghost btn-xs" title="Download PDF">PDF</a>
                <a href="{{ route('hr.payroll.payslip-protected', $r->id) }}" class="btn btn-ghost btn-xs" title="Download password-protected ZIP">🔒 ZIP</a>
                <form method="POST" action="{{ route('hr.payroll.email-payslip', $r->id) }}" class="inline"
                  onsubmit="return confirm('Email payslip to {{ $r->employee?->official_email ?? $r->employee?->personal_email ?? 'employee' }}?')">
                  @csrf
                  <button class="btn btn-ghost btn-xs text-indigo-500" title="Email payslip">✉ Email</button>
                </form>
                @if($r->is_locked)
                <form method="POST" action="{{ route('hr.payroll.unlock', $r->id) }}" class="inline">
                  @csrf
                  <button class="btn btn-secondary btn-xs">Unlock</button>
                </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="td text-center py-10 text-slate-400">No payroll for {{ $month }}. Click "Generate Payroll".</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
