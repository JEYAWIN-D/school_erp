@extends('portal.layout')
@section('title', 'Fee Statement')
@section('content')

{{-- Summary header --}}
<div style="margin-bottom: 1.25rem;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem;">
    <h2 style="font-size: 1.1875rem; font-weight: 700; color: #1e293b; margin: 0;">Fee Statement</h2>
    <a href="{{ route('portal.student.fees.pdf') }}" target="_blank"
       style="display: inline-flex; align-items: center; gap: .375rem; padding: .5rem 1rem; background: #2563eb; color: #fff; text-decoration: none; border-radius: .5rem; font-size: .8125rem; font-weight: 600; transition: background .1s;"
       onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
      <svg style="width:.875rem;height:.875rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      Download PDF
    </a>
  </div>

  <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: .875rem; margin-bottom: 1.25rem;">
    <div class="portal-card" style="text-align: center; padding: 1.25rem;">
      <div style="font-size: 1.375rem; font-weight: 700; color: #1e293b;">₹{{ number_format($totalDue) }}</div>
      <div style="font-size: .72rem; color: #94a3b8; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; margin-top: .25rem;">Total Billed</div>
    </div>
    <div class="portal-card" style="text-align: center; padding: 1.25rem;">
      <div style="font-size: 1.375rem; font-weight: 700; color: #16a34a;">₹{{ number_format($totalPaid) }}</div>
      <div style="font-size: .72rem; color: #94a3b8; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; margin-top: .25rem;">Total Paid</div>
    </div>
    <div class="portal-card" style="text-align: center; padding: 1.25rem; border: 2px solid {{ $balance > 0 ? '#fecaca' : '#bbf7d0' }};">
      <div style="font-size: 1.375rem; font-weight: 700; color: {{ $balance > 0 ? '#dc2626' : '#16a34a' }};">₹{{ number_format($balance) }}</div>
      <div style="font-size: .72rem; color: #94a3b8; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; margin-top: .25rem;">{{ $balance > 0 ? 'Balance Due' : 'All Clear' }}</div>
    </div>
  </div>

  @if($balance > 0)
    <div style="padding: .875rem 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: .75rem; color: #dc2626; font-size: .875rem; font-weight: 500; display: flex; align-items: center; gap: .5rem; margin-bottom: 1.25rem;">
      <svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      You have ₹{{ number_format($balance) }} outstanding. Please contact the school fee office.
    </div>
  @endif
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">

  {{-- Fee invoices --}}
  <div class="portal-card">
    <div class="section-title">
      <svg style="width:1rem;height:1rem;color:#8b5cf6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      Fee Invoices
    </div>
    @forelse($invoices as $inv)
    <div class="divider-row" style="display: flex; align-items: center; justify-content: space-between; padding: .625rem 0; gap: .5rem;">
      <div style="flex: 1; min-width: 0;">
        <p style="font-size: .875rem; font-weight: 500; color: #1e293b;">{{ $inv->fee_head_name ?? 'Fee' }}</p>
        <p style="font-size: .75rem; color: #94a3b8;">Due: {{ \Carbon\Carbon::parse($inv->due_date)->format('d M Y') }}</p>
      </div>
      <div style="text-align: right; flex-shrink: 0;">
        <div style="font-size: .9375rem; font-weight: 700; color: #1e293b;">₹{{ number_format($inv->amount) }}</div>
        @if(($inv->status ?? '') === 'paid')
          <span style="display:inline-block;font-size:.7rem;font-weight:600;padding:.125rem .5rem;border-radius:9999px;background:#dcfce7;color:#166534;">Paid</span>
        @elseif(($inv->status ?? '') === 'partial')
          <span style="display:inline-block;font-size:.7rem;font-weight:600;padding:.125rem .5rem;border-radius:9999px;background:#fef3c7;color:#92400e;">Partial</span>
        @else
          <span style="display:inline-block;font-size:.7rem;font-weight:600;padding:.125rem .5rem;border-radius:9999px;background:#fee2e2;color:#991b1b;">Unpaid</span>
        @endif
      </div>
    </div>
    @empty
      <div style="text-align: center; padding: 2rem 0; color: #94a3b8; font-size: .875rem;">
        <svg style="width: 2.5rem; height: 2.5rem; margin: 0 auto .75rem; opacity: .35;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        No fee invoices
      </div>
    @endforelse
  </div>

  {{-- Payment history --}}
  <div class="portal-card">
    <div class="section-title">
      <svg style="width:1rem;height:1rem;color:#16a34a" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7l2 2 4-4"/></svg>
      Payment History
    </div>
    @forelse($payments as $pay)
    <div class="divider-row" style="display: flex; align-items: center; justify-content: space-between; padding: .625rem 0; gap: .5rem;">
      <div style="flex: 1;">
        <p style="font-size: .875rem; font-weight: 500; color: #1e293b;">Receipt #{{ $pay->receipt_number ?? $pay->id }}</p>
        <p style="font-size: .75rem; color: #94a3b8;">{{ \Carbon\Carbon::parse($pay->payment_date)->format('d M Y') }} &bull; {{ ucfirst($pay->payment_mode ?? 'Cash') }}</p>
      </div>
      <div style="text-align: right; flex-shrink: 0;">
        <div style="font-size: .9375rem; font-weight: 700; color: #16a34a;">₹{{ number_format($pay->total_paid ?? $pay->amount ?? 0) }}</div>
        <a href="{{ route('portal.student.fees.receipt', $pay->id) }}" target="_blank"
           style="display: inline-block; margin-top: .25rem; font-size: .7rem; padding: .15rem .5rem; background: #eff6ff; color: #2563eb; border-radius: .375rem; text-decoration: none; font-weight: 500;">
          ↓ Receipt
        </a>
      </div>
    </div>
    @empty
      <div style="text-align: center; padding: 2rem 0; color: #94a3b8; font-size: .875rem;">
        <svg style="width: 2.5rem; height: 2.5rem; margin: 0 auto .75rem; opacity: .35;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        No payments recorded
      </div>
    @endforelse
  </div>

</div>
@endsection
