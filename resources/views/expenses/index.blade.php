@extends('layouts.app')

@section('title', 'Expenses Hub — DASA EduERP')

@section('content')
<div class="space-y-6">

  {{-- ── Header ── --}}
  <div style="background:linear-gradient(135deg,#1e1b4b 0%,#312e81 60%,#4c1d95 100%);border-radius:24px;padding:28px 32px;color:#fff;position:relative;overflow:hidden">
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:rgba(255,255,255,0.04);border-radius:50%"></div>
    <div style="position:absolute;bottom:-60px;right:80px;width:140px;height:140px;background:rgba(255,255,255,0.03);border-radius:50%"></div>

    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;position:relative">
      <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
          <span style="padding:3px 10px;border-radius:100px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;background:rgba(167,139,250,0.2);color:#c4b5fd;border:1px solid rgba(167,139,250,0.3)">FINANCE MODULE</span>
          <span style="font-size:12px;color:rgba(255,255,255,0.5)">{{ $currentYear?->name ?? '2025–2026' }}</span>
        </div>
        <h1 style="font-size:32px;font-weight:900;color:#fff;letter-spacing:-.02em;margin-bottom:6px">Expenses Hub</h1>
        <p style="font-size:13px;color:rgba(255,255,255,0.6);max-width:520px">Bifurcated expense management — Academic (Coordinator → Admin → Principal) &amp; Maintenance (Admin → Principal → Correspondent) approval workflows.</p>
      </div>
      <a href="{{ route('expenses.create') }}" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;padding:12px 22px;border-radius:14px;font-size:13px;font-weight:700;text-decoration:none;flex-shrink:0;box-shadow:0 4px 16px rgba(109,40,217,0.4)">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        New Expense Voucher
      </a>
    </div>

    {{-- KPI Cards row --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-top:24px">
      @foreach([
        ['label'=>'Total Approved','value'=>'₹'.number_format($stats['total_amount'],0),'icon'=>'💰','color'=>'rgba(167,139,250,0.2)'],
        ['label'=>'Academic Spend','value'=>'₹'.number_format($stats['academic_amount'],0),'icon'=>'📚','color'=>'rgba(96,165,250,0.2)'],
        ['label'=>'Maintenance Spend','value'=>'₹'.number_format($stats['maintenance_amount'],0),'icon'=>'🔧','color'=>'rgba(251,146,60,0.2)'],
        ['label'=>'Pending Approval','value'=>$stats['pending_count'].' vouchers','icon'=>'⏳','color'=>'rgba(248,113,113,0.2)'],
        ['label'=>'Total Vouchers','value'=>$stats['total_count'],'icon'=>'📄','color'=>'rgba(52,211,153,0.2)'],
      ] as $kpi)
      <div style="background:{{ $kpi['color'] }};border:1px solid rgba(255,255,255,0.1);border-radius:14px;padding:14px 16px">
        <div style="font-size:22px;margin-bottom:6px">{{ $kpi['icon'] }}</div>
        <div style="font-size:14px;font-weight:800;color:#fff;line-height:1.2">{{ $kpi['value'] }}</div>
        <div style="font-size:11px;color:rgba(255,255,255,0.55);margin-top:3px;font-weight:600">{{ $kpi['label'] }}</div>
      </div>
      @endforeach
    </div>
  </div>

  {{-- ── Two Branch Cards ── --}}
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

    {{-- Academic Branch --}}
    <a href="{{ route('expenses.academic') }}" style="text-decoration:none;display:block;background:#fff;border:1.5px solid #dbeafe;border-radius:20px;padding:24px;transition:box-shadow .2s;position:relative;overflow:hidden">
      <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:50%;opacity:.8"></div>
      <div style="width:44px;height:44px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:14px;position:relative">📚</div>
      <h3 style="font-size:18px;font-weight:800;color:#1e3a8a;margin-bottom:6px">Academic Expenses</h3>
      <p style="font-size:12px;color:#64748b;line-height:1.5;margin-bottom:16px">Books, Lab chemicals, Exam stationery, Sports equipment, Student events, Faculty training &amp; Educational software.</p>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:14px">
        @foreach(['Books','Lab','Exams','Sports','Events','Training'] as $tag)
        <span style="padding:3px 10px;background:#eff6ff;color:#3b82f6;border-radius:100px;font-size:10px;font-weight:700">{{ $tag }}</span>
        @endforeach
      </div>
      <div style="background:#eff6ff;border-radius:12px;padding:10px 14px;display:flex;align-items:center;justify-content:space-between">
        <div>
          <div style="font-size:10px;color:#60a5fa;font-weight:700;text-transform:uppercase">Approval Chain</div>
          <div style="font-size:11px;color:#1e3a8a;font-weight:600;margin-top:2px">Coordinator → Admin → Principal</div>
        </div>
        <svg width="18" height="18" fill="none" stroke="#3b82f6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </div>
    </a>

    {{-- Maintenance Branch --}}
    <a href="{{ route('expenses.maintenance') }}" style="text-decoration:none;display:block;background:#fff;border:1.5px solid #fed7aa;border-radius:20px;padding:24px;transition:box-shadow .2s;position:relative;overflow:hidden">
      <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:linear-gradient(135deg,#fff7ed,#fed7aa);border-radius:50%;opacity:.8"></div>
      <div style="width:44px;height:44px;background:linear-gradient(135deg,#f97316,#c2410c);border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:14px;position:relative">🔧</div>
      <h3 style="font-size:18px;font-weight:800;color:#7c2d12;margin-bottom:6px">Maintenance Expenses</h3>
      <p style="font-size:12px;color:#64748b;line-height:1.5;margin-bottom:16px">Civil works, Electrical systems, Plumbing &amp; RO water, School bus fleet, Campus housekeeping &amp; CCTV security.</p>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:14px">
        @foreach(['Civil','Electrical','Plumbing','Bus Fleet','Security','Sanitation'] as $tag)
        <span style="padding:3px 10px;background:#fff7ed;color:#f97316;border-radius:100px;font-size:10px;font-weight:700">{{ $tag }}</span>
        @endforeach
      </div>
      <div style="background:#fff7ed;border-radius:12px;padding:10px 14px;display:flex;align-items:center;justify-content:space-between">
        <div>
          <div style="font-size:10px;color:#fb923c;font-weight:700;text-transform:uppercase">Approval Chain</div>
          <div style="font-size:11px;color:#7c2d12;font-weight:600;margin-top:2px">Admin → Principal → Correspondent</div>
        </div>
        <svg width="18" height="18" fill="none" stroke="#f97316" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </div>
    </a>
  </div>

  {{-- ── Recent Vouchers Table ── --}}
  <div style="background:#fff;border-radius:20px;border:1px solid #e2e8f0;overflow:hidden">
    <div style="padding:20px 24px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
      <h2 style="font-size:15px;font-weight:800;color:#1e293b;display:flex;align-items:center;gap:8px">
        <span style="font-size:18px">📄</span> All Expense Vouchers
        <span style="font-size:12px;font-weight:600;color:#64748b">({{ $expenses->total() }} total)</span>
      </h2>

      <form method="GET" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <select name="category" onchange="this.form.submit()" style="padding:7px 12px;border:1px solid #e2e8f0;border-radius:10px;font-size:12px;color:#374151;outline:none">
          <option value="">All Categories</option>
          <option value="academic" {{ request('category')==='academic'?'selected':'' }}>📚 Academic</option>
          <option value="maintenance" {{ request('category')==='maintenance'?'selected':'' }}>🔧 Maintenance</option>
        </select>
        <select name="status" onchange="this.form.submit()" style="padding:7px 12px;border:1px solid #e2e8f0;border-radius:10px;font-size:12px;color:#374151;outline:none">
          <option value="">All Statuses</option>
          <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
          <option value="verified" {{ request('status')==='verified'?'selected':'' }}>Verified</option>
          <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Approved</option>
          <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Rejected</option>
        </select>
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search vouchers…" style="padding:7px 12px;border:1px solid #e2e8f0;border-radius:10px;font-size:12px;color:#374151;outline:none;min-width:180px">
        <button type="submit" style="padding:7px 14px;background:#4f46e5;color:#fff;border:none;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer">Go</button>
      </form>
    </div>

    <div style="overflow-x:auto">
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead>
          <tr style="background:#f8fafc">
            <th style="text-align:left;padding:12px 20px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Voucher</th>
            <th style="text-align:left;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Category</th>
            <th style="text-align:left;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Title</th>
            <th style="text-align:left;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Vendor</th>
            <th style="text-align:right;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Amount</th>
            <th style="text-align:left;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Date</th>
            <th style="text-align:center;padding:12px 16px;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Status</th>
            <th style="padding:12px 20px"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($expenses as $expense)
          <tr style="border-top:1px solid #f1f5f9;transition:background .15s" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
            <td style="padding:14px 20px">
              <span style="font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:700;color:#6d28d9;background:#f3f0ff;padding:3px 8px;border-radius:6px">{{ $expense->expense_number }}</span>
            </td>
            <td style="padding:14px 16px">
              @if($expense->category === 'academic')
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:#eff6ff;color:#3b82f6;border-radius:100px;font-size:11px;font-weight:700">📚 Academic</span>
              @else
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;background:#fff7ed;color:#f97316;border-radius:100px;font-size:11px;font-weight:700">🔧 Maintenance</span>
              @endif
            </td>
            <td style="padding:14px 16px;max-width:200px">
              <div style="font-weight:600;color:#1e293b;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $expense->title }}">{{ Str::limit($expense->title, 45) }}</div>
            </td>
            <td style="padding:14px 16px;font-size:12px;color:#64748b">{{ $expense->vendor_name ?: '—' }}</td>
            <td style="padding:14px 16px;text-align:right;font-weight:800;color:#1e293b;font-family:'JetBrains Mono',monospace;font-size:13px">₹{{ number_format($expense->amount, 0) }}</td>
            <td style="padding:14px 16px;font-size:12px;color:#64748b">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
            <td style="padding:14px 16px;text-align:center">
              @php
                $statusConfig = [
                  'pending'  => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Pending'],
                  'verified' => ['bg'=>'#dbeafe','color'=>'#1e40af','label'=>'Verified'],
                  'approved' => ['bg'=>'#dcfce7','color'=>'#15803d','label'=>'Approved'],
                  'rejected' => ['bg'=>'#fee2e2','color'=>'#b91c1c','label'=>'Rejected'],
                ];
                $sc = $statusConfig[$expense->approval_status] ?? $statusConfig['pending'];
              @endphp
              <span style="padding:4px 12px;border-radius:100px;font-size:11px;font-weight:700;background:{{ $sc['bg'] }};color:{{ $sc['color'] }}">{{ $sc['label'] }}</span>
            </td>
            <td style="padding:14px 20px">
              <a href="{{ route('expenses.show', $expense->id) }}" style="font-size:12px;font-weight:700;color:#4f46e5;text-decoration:none">View →</a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" style="padding:48px;text-align:center;color:#94a3b8">
              <div style="font-size:40px;margin-bottom:8px">📭</div>
              <div style="font-weight:600">No expense vouchers found</div>
            </td>
          </tr>
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
