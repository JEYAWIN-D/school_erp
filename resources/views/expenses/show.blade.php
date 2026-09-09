@extends('layouts.app')

@section('title', 'Expense Voucher #' . $expense->expense_number . ' — DASA EduERP')

@section('content')
<div class="space-y-6">

  {{-- Top Actions --}}
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div style="display:flex;align-items:center;gap:8px">
      <a href="{{ $expense->category === 'academic' ? route('expenses.academic') : route('expenses.maintenance') }}"
         style="display:inline-flex;align-items:center;gap:4px;padding:7px 14px;border:1px solid #e2e8f0;border-radius:10px;font-size:12px;font-weight:700;color:#374151;text-decoration:none">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back
      </a>
    </div>
    <div style="display:flex;align-items:center;gap:8px">
      <button onclick="window.print()" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;font-size:12px;font-weight:700;color:#374151;cursor:pointer">
        🖨️ Print Voucher
      </button>
    </div>
  </div>

  {{-- Printable Voucher Slip --}}
  <div id="voucher-print" style="background:#fff;border-radius:24px;border:1px solid #e2e8f0;overflow:hidden;max-width:900px;margin:0 auto">

    {{-- Header Band --}}
    @if($expense->category === 'academic')
    <div style="background:linear-gradient(135deg,#1e3a8a,#3b82f6);padding:28px 32px;color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
    @else
    <div style="background:linear-gradient(135deg,#7c2d12,#f97316);padding:28px 32px;color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
    @endif
      <div style="display:flex;align-items:center;gap:16px">
        <div style="width:52px;height:52px;border-radius:16px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);display:flex;align-items:center;justify-content:center;font-size:26px">
          {{ $expense->category === 'academic' ? '📚' : '🔧' }}
        </div>
        <div>
          <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:.05em">DASA EduGroup — EXPENSE VOUCHER</div>
          <div style="font-size:22px;font-weight:900;color:#fff;margin-top:2px">{{ ucfirst($expense->category) }} Expense</div>
          <div style="font-size:13px;color:rgba(255,255,255,0.7);margin-top:2px">{{ $expense->academicYear?->name ?? '2025–2026' }}</div>
        </div>
      </div>
      <div style="text-align:right">
        <div style="font-size:11px;color:rgba(255,255,255,0.6);font-weight:700;text-transform:uppercase">Voucher No.</div>
        <div style="font-family:'JetBrains Mono',monospace;font-size:18px;font-weight:800;color:#fff;margin-top:2px">{{ $expense->expense_number }}</div>
        @php
          $statusColor = match($expense->approval_status) {
            'approved' => '#4ade80',
            'verified' => '#60a5fa',
            'rejected' => '#f87171',
            default    => '#fbbf24',
          };
        @endphp
        <span style="display:inline-block;margin-top:8px;padding:4px 14px;border-radius:100px;font-size:11px;font-weight:800;background:rgba(255,255,255,0.15);color:{{ $statusColor }};border:1px solid {{ $statusColor }}">
          {{ strtoupper($expense->approval_status) }}
        </span>
      </div>
    </div>

    {{-- Main Info Grid --}}
    <div style="padding:28px 32px">
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:24px">
        <div style="background:#f8fafc;border-radius:14px;padding:16px">
          <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Category</div>
          <div style="font-size:14px;font-weight:700;color:#1e293b">{{ ucfirst($expense->category) }}</div>
          <div style="font-size:12px;color:#64748b;margin-top:2px">{{ str_replace('_',' ',ucwords($expense->subcategory,'_')) }}</div>
        </div>
        <div style="background:#f8fafc;border-radius:14px;padding:16px">
          <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Amount</div>
          <div style="font-size:22px;font-weight:900;color:#1e293b;font-family:'JetBrains Mono',monospace">₹{{ number_format($expense->amount, 2) }}</div>
        </div>
        <div style="background:#f8fafc;border-radius:14px;padding:16px">
          <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Expense Date</div>
          <div style="font-size:14px;font-weight:700;color:#1e293b">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</div>
          <div style="font-size:11px;color:#64748b;margin-top:2px">{{ $expense->payment_method }}</div>
        </div>
      </div>

      {{-- Title & Description --}}
      <div style="background:#f8fafc;border-radius:14px;padding:18px;margin-bottom:20px">
        <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">Expense Title / Purpose</div>
        <div style="font-size:16px;font-weight:700;color:#1e293b;margin-bottom:8px">{{ $expense->title }}</div>
        @if($expense->description)
        <div style="font-size:13px;color:#64748b;line-height:1.6;border-top:1px solid #e2e8f0;padding-top:10px;margin-top:6px">{{ $expense->description }}</div>
        @endif
      </div>

      {{-- Vendor & Invoice --}}
      @if($expense->vendor_name || $expense->vendor_invoice_no)
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
        @if($expense->vendor_name)
        <div style="background:#f8fafc;border-radius:14px;padding:16px">
          <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Vendor / Supplier</div>
          <div style="font-size:14px;font-weight:700;color:#1e293b">{{ $expense->vendor_name }}</div>
        </div>
        @endif
        @if($expense->vendor_invoice_no)
        <div style="background:#f8fafc;border-radius:14px;padding:16px">
          <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Invoice / Receipt No.</div>
          <div style="font-size:14px;font-weight:700;color:#1e293b;font-family:'JetBrains Mono',monospace">{{ $expense->vendor_invoice_no }}</div>
        </div>
        @endif
      </div>
      @endif

      {{-- Approval Timeline --}}
      <div style="border:1px solid #e2e8f0;border-radius:16px;padding:20px;margin-bottom:20px">
        <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:16px;display:flex;align-items:center;gap:6px"><span>📋</span> Approval Timeline</div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0;position:relative">
          @php
            $chain = $expense->category === 'academic'
              ? [['step'=>'Prepared','role'=>'Academic Coordinator','field'=>'created_by','time'=>'created_at'],
                 ['step'=>'Verified','role'=>'Admin Staff','field'=>'verified_by','time'=>'verified_at'],
                 ['step'=>'Approved','role'=>'Principal','field'=>'approved_by','time'=>'approved_at']]
              : [['step'=>'Prepared','role'=>'Admin Staff','field'=>'created_by','time'=>'created_at'],
                 ['step'=>'Verified','role'=>'Principal','field'=>'verified_by','time'=>'verified_at'],
                 ['step'=>'Approved','role'=>'Correspondent','field'=>'approved_by','time'=>'approved_at']];
          @endphp
          @foreach($chain as $i => $step)
          @php
            $timeField = $step['time'];
            $userField = $step['field'];
            $person = match($step['field']) {
              'created_by' => $expense->creator,
              'verified_by' => $expense->verifier,
              'approved_by' => $expense->approver,
              default => null,
            };
            $time = $expense->$timeField;
            $done = !is_null($time) || $step['field'] === 'created_by';
          @endphp
          <div style="text-align:center;position:relative">
            @if($i < count($chain)-1)
            <div style="position:absolute;top:18px;left:50%;right:-50%;height:2px;background:{{ $done ? '#4ade80' : '#e2e8f0' }};z-index:0"></div>
            @endif
            <div style="width:36px;height:36px;border-radius:50%;background:{{ $done ? '#dcfce7' : '#f1f5f9' }};border:2px solid {{ $done ? '#4ade80' : '#e2e8f0' }};display:flex;align-items:center;justify-content:center;margin:0 auto 8px;position:relative;z-index:1">
              @if($done)<span style="font-size:16px">✓</span>@else<span style="font-size:14px;color:#94a3b8">{{ $i+1 }}</span>@endif
            </div>
            <div style="font-size:12px;font-weight:700;color:{{ $done ? '#15803d' : '#94a3b8' }}">{{ $step['step'] }}</div>
            <div style="font-size:10px;color:#94a3b8;margin-top:2px">{{ $step['role'] }}</div>
            @if($person)<div style="font-size:10px;color:#1e293b;font-weight:600;margin-top:3px">{{ $person->name }}</div>@endif
            @if($time)<div style="font-size:9px;color:#94a3b8;margin-top:1px">{{ \Carbon\Carbon::parse($time)->format('d M Y') }}</div>@endif
          </div>
          @endforeach
        </div>
      </div>

      {{-- Rejection Note --}}
      @if($expense->approval_status === 'rejected' && $expense->rejection_reason)
      <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:14px;padding:16px;margin-bottom:20px">
        <div style="font-size:12px;font-weight:700;color:#b91c1c;margin-bottom:6px;display:flex;align-items:center;gap:6px">❌ Rejection Reason</div>
        <div style="font-size:13px;color:#7f1d1d">{{ $expense->rejection_reason }}</div>
      </div>
      @endif

      {{-- Signature Footer --}}
      <div style="border-top:2px dashed #e2e8f0;padding-top:24px;display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
        @foreach($expense->category === 'academic'
          ? ['Prepared by (Academic Coordinator)','Verified by (Admin)','Approved by (Principal)']
          : ['Prepared by (Admin)','Verified by (Principal)','Approved by (Correspondent)']
          as $sig)
        <div style="text-align:center">
          <div style="height:48px;border-bottom:2px solid #e2e8f0;margin-bottom:8px"></div>
          <div style="font-size:11px;color:#94a3b8;font-weight:600">{{ $sig }}</div>
        </div>
        @endforeach
      </div>

      {{-- Quick Actions --}}
      @if($expense->approval_status !== 'approved' && $expense->approval_status !== 'rejected')
      <div style="display:flex;align-items:center;gap:10px;margin-top:20px;padding-top:20px;border-top:1px solid #f1f5f9">
        @if($expense->approval_status === 'pending')
          <form action="{{ route('expenses.verify', $expense->id) }}" method="POST">
            @csrf
            <button type="submit" style="padding:10px 22px;background:#3b82f6;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer">✓ Mark Verified</button>
          </form>
        @elseif($expense->approval_status === 'verified')
          <form action="{{ route('expenses.approve', $expense->id) }}" method="POST">
            @csrf
            <button type="submit" style="padding:10px 22px;background:#22c55e;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer">✓ Mark Approved</button>
          </form>
        @endif

        <button onclick="document.getElementById('rejectModal').style.display='flex'" style="padding:10px 18px;background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer">✕ Reject</button>
      </div>

      {{-- Reject Modal --}}
      <div id="rejectModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:16px">
        <div style="background:#fff;border-radius:20px;padding:28px;max-width:480px;width:100%">
          <h3 style="font-size:16px;font-weight:800;color:#1e293b;margin-bottom:6px">Reject Expense Voucher</h3>
          <p style="font-size:13px;color:#64748b;margin-bottom:16px">Please provide the reason for rejection so the submitter can resubmit.</p>
          <form action="{{ route('expenses.reject', $expense->id) }}" method="POST">
            @csrf
            <textarea name="rejection_reason" rows="4" required placeholder="State clear reason for rejection…" style="width:100%;padding:12px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'Inter',sans-serif;outline:none;resize:vertical;margin-bottom:14px"></textarea>
            <div style="display:flex;gap:10px">
              <button type="submit" style="flex:1;padding:11px;background:#ef4444;color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer">Confirm Reject</button>
              <button type="button" onclick="document.getElementById('rejectModal').style.display='none'" style="flex:1;padding:11px;background:#f1f5f9;color:#374151;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer">Cancel</button>
            </div>
          </form>
        </div>
      </div>
      @endif

    </div>
  </div>

</div>

<style>
@media print {
  .no-print, header, nav, aside { display: none !important; }
  #voucher-print { border: 1px solid #e5e7eb; max-width: 100%; margin: 0; }
}
</style>
@endsection
