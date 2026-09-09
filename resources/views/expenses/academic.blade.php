@extends('layouts.app')

@section('title', 'Academic Expenses — DASA EduERP')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 60%,#3b82f6 100%);border-radius:24px;padding:28px 32px;color:#fff;position:relative;overflow:hidden">
    <div style="position:absolute;top:-30px;right:-30px;width:180px;height:180px;background:rgba(255,255,255,0.05);border-radius:50%"></div>

    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;position:relative">
      <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
          <a href="{{ route('expenses.index') }}" style="font-size:12px;color:rgba(255,255,255,0.6);text-decoration:none">← Expenses Hub</a>
          <span style="color:rgba(255,255,255,0.3)">•</span>
          <span style="padding:3px 10px;border-radius:100px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;background:rgba(147,197,253,0.2);color:#bfdbfe;border:1px solid rgba(147,197,253,0.3)">ACADEMIC EXPENSES</span>
        </div>
        <h1 style="font-size:30px;font-weight:900;color:#fff;letter-spacing:-.02em;margin-bottom:6px">📚 Academic Expenses</h1>
        <p style="font-size:13px;color:rgba(255,255,255,0.65);max-width:540px">Books, Lab reagents, Exam stationery, Sports equipment, Student events, Faculty workshops &amp; Educational software. Approval: <strong style="color:#bfdbfe">Coordinator → Admin → Principal</strong></p>
      </div>
      <a href="{{ route('expenses.create') }}?category=academic" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.15);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.25);color:#fff;padding:11px 20px;border-radius:14px;font-size:13px;font-weight:700;text-decoration:none;flex-shrink:0">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Add Academic Expense
      </a>
    </div>

    {{-- Summary KPIs --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:20px;position:relative">
      <div style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);border-radius:14px;padding:16px">
        <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.6);text-transform:uppercase;margin-bottom:4px">Total Approved Academic</div>
        <div style="font-size:24px;font-weight:900;color:#fff;font-family:'JetBrains Mono',monospace">₹{{ number_format($approvedSum, 0) }}</div>
      </div>
      <div style="background:rgba(251,191,36,0.15);border:1px solid rgba(251,191,36,0.25);border-radius:14px;padding:16px">
        <div style="font-size:11px;font-weight:700;color:rgba(253,230,138,0.8);text-transform:uppercase;margin-bottom:4px">Awaiting Approval</div>
        <div style="font-size:24px;font-weight:900;color:#fde68a;font-family:'JetBrains Mono',monospace">₹{{ number_format($pendingSum, 0) }}</div>
      </div>
    </div>
  </div>

  {{-- Subcategory Tabs --}}
  <div style="background:#fff;border-radius:16px;padding:16px 20px;border:1px solid #e2e8f0;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
    <a href="{{ route('expenses.academic') }}" style="padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;{{ !request('subcategory') ? 'background:#3b82f6;color:#fff' : 'background:#f1f5f9;color:#374151' }}">All</a>
    @foreach($subcategories as $key => $label)
    <a href="{{ route('expenses.academic', ['subcategory'=>$key]) }}" style="padding:6px 14px;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;{{ request('subcategory')===$key ? 'background:#3b82f6;color:#fff' : 'background:#f1f5f9;color:#374151' }}">{{ $label }}</a>
    @endforeach
  </div>

  {{-- Filters + Table --}}
  <div style="background:#fff;border-radius:20px;border:1px solid #e2e8f0;overflow:hidden">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <span style="font-size:14px;font-weight:700;color:#1e293b">Expense Vouchers <span style="color:#64748b;font-weight:500">({{ $expenses->total() }})</span></span>
      <form method="GET" style="display:flex;align-items:center;gap:8px">
        @if(request('subcategory'))<input type="hidden" name="subcategory" value="{{ request('subcategory') }}">@endif
        <select name="status" onchange="this.form.submit()" style="padding:7px 12px;border:1px solid #e2e8f0;border-radius:10px;font-size:12px;color:#374151;outline:none">
          <option value="">All Status</option>
          <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
          <option value="verified" {{ request('status')==='verified'?'selected':'' }}>Verified</option>
          <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Approved</option>
          <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
        </select>
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search…" style="padding:7px 12px;border:1px solid #e2e8f0;border-radius:10px;font-size:12px;color:#374151;outline:none;min-width:160px">
        <button type="submit" style="padding:7px 14px;background:#3b82f6;color:#fff;border:none;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer">Filter</button>
      </form>
    </div>

    {{-- Approval Chain Flow --}}
    <div style="padding:14px 24px;background:#eff6ff;border-bottom:1px solid #dbeafe;display:flex;align-items:center;gap:8px;font-size:12px">
      <span style="font-weight:600;color:#1e3a8a">Approval Chain:</span>
      @foreach(['Academic Coordinator (Prepares)','Admin (Verifies)','Principal (Approves)'] as $i => $step)
        @if($i > 0)<span style="color:#93c5fd">→</span>@endif
        <span style="padding:3px 10px;background:#dbeafe;color:#1e40af;border-radius:8px;font-weight:700">{{ $step }}</span>
      @endforeach
    </div>

    <div style="overflow-x:auto">
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
          <tr style="background:#f8fafc">
            <th style="text-align:left;padding:12px 20px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Voucher No.</th>
            <th style="text-align:left;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Category</th>
            <th style="text-align:left;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Title &amp; Vendor</th>
            <th style="text-align:right;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Amount</th>
            <th style="text-align:left;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Date</th>
            <th style="text-align:center;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Status</th>
            <th style="padding:12px 20px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($expenses as $expense)
          <tr style="border-top:1px solid #f1f5f9" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
            <td style="padding:14px 20px">
              <span style="font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:700;color:#1d4ed8;background:#eff6ff;padding:3px 8px;border-radius:6px">{{ $expense->expense_number }}</span>
            </td>
            <td style="padding:14px 16px">
              <span style="font-size:11px;font-weight:600;color:#374151">{{ str_replace('_',' ',ucwords($expense->subcategory,'_')) }}</span>
            </td>
            <td style="padding:14px 16px;max-width:220px">
              <div style="font-weight:600;color:#1e293b;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $expense->title }}">{{ Str::limit($expense->title, 50) }}</div>
              @if($expense->vendor_name)<div style="font-size:11px;color:#94a3b8;margin-top:2px">{{ Str::limit($expense->vendor_name, 35) }}</div>@endif
            </td>
            <td style="padding:14px 16px;text-align:right;font-weight:800;color:#1e293b;font-family:'JetBrains Mono',monospace;font-size:14px">₹{{ number_format($expense->amount, 0) }}</td>
            <td style="padding:14px 16px;font-size:12px;color:#64748b">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
            <td style="padding:14px 16px;text-align:center">
              @php
                $sc = match($expense->approval_status) {
                  'approved' => ['bg'=>'#dcfce7','color'=>'#15803d','dot'=>'#4ade80'],
                  'verified' => ['bg'=>'#dbeafe','color'=>'#1e40af','dot'=>'#60a5fa'],
                  'rejected' => ['bg'=>'#fee2e2','color'=>'#b91c1c','dot'=>'#f87171'],
                  default    => ['bg'=>'#fef3c7','color'=>'#92400e','dot'=>'#fbbf24'],
                };
              @endphp
              <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:100px;font-size:11px;font-weight:700;background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">
                <span style="width:5px;height:5px;border-radius:50%;background:{{ $sc['dot'] }};display:inline-block"></span>
                {{ ucfirst($expense->approval_status) }}
              </span>
            </td>
            <td style="padding:14px 20px">
              <div style="display:flex;align-items:center;gap:8px">
                <a href="{{ route('expenses.show', $expense->id) }}" style="font-size:12px;font-weight:700;color:#3b82f6;text-decoration:none;padding:5px 10px;border:1px solid #dbeafe;border-radius:8px">View</a>
                @if($expense->approval_status === 'pending')
                  <form action="{{ route('expenses.verify', $expense->id) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" style="font-size:11px;font-weight:700;color:#1e40af;background:#dbeafe;border:none;border-radius:8px;padding:5px 10px;cursor:pointer">Verify</button>
                  </form>
                @elseif($expense->approval_status === 'verified')
                  <form action="{{ route('expenses.approve', $expense->id) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" style="font-size:11px;font-weight:700;color:#15803d;background:#dcfce7;border:none;border-radius:8px;padding:5px 10px;cursor:pointer">Approve</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" style="padding:48px;text-align:center;color:#94a3b8">
            <div style="font-size:40px;margin-bottom:8px">📚</div>
            <div style="font-weight:600">No academic expenses found</div>
            <a href="{{ route('expenses.create') }}?category=academic" style="color:#3b82f6;font-size:13px;font-weight:600;text-decoration:none">+ Add first academic expense</a>
          </td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($expenses->hasPages())
    <div style="padding:16px 24px;border-top:1px solid #f1f5f9">{{ $expenses->withQueryString()->links() }}</div>
    @endif
  </div>

</div>
@endsection
