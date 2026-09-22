<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Daily Account Day Book — {{ date('d-m-Y', strtotime($date)) }}</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      font-size: 11px;
      color: #1e293b;
      margin: 0;
      padding: 15mm;
      background: #fff;
    }
    .header {
      border-bottom: 2px solid #0f172a;
      padding-bottom: 12px;
      margin-bottom: 16px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }
    .school-title {
      font-size: 20px;
      font-weight: 900;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .sheet-title {
      font-size: 13px;
      font-weight: 800;
      color: #334155;
      margin-top: 2px;
      text-transform: uppercase;
    }
    .meta {
      font-size: 10px;
      color: #64748b;
      text-align: right;
    }
    .summary-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      margin-bottom: 16px;
    }
    .summary-card {
      padding: 10px;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      background: #f8fafc;
    }
    .card-label {
      font-size: 9px;
      font-weight: 800;
      text-transform: uppercase;
      color: #64748b;
    }
    .card-val {
      font-size: 14px;
      font-weight: 900;
      font-family: monospace;
      margin-top: 4px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 10.5px;
      margin-top: 8px;
    }
    th {
      background: #f1f5f9;
      border: 1px solid #cbd5e1;
      padding: 6px 8px;
      text-align: left;
      font-weight: 800;
      font-size: 9.5px;
      text-transform: uppercase;
      color: #334155;
    }
    td {
      border: 1px solid #e2e8f0;
      padding: 6px 8px;
    }
    tr:nth-child(even) {
      background: #f8fafc;
    }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .font-bold { font-weight: bold; }
    .font-mono { font-family: monospace; }
    .badge {
      font-size: 9px;
      font-weight: 800;
      padding: 2px 6px;
      border-radius: 4px;
      display: inline-block;
      text-transform: uppercase;
    }
    .badge-inflow { background: #dcfce7; color: #166534; }
    .badge-outflow { background: #ffe4e6; color: #9f1239; }
    .badge-trf { background: #e0e7ff; color: #3730a3; }
    .sign-section {
      margin-top: 30px;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      text-align: center;
      font-size: 10px;
      font-weight: bold;
      color: #475569;
    }
    .sign-line {
      border-bottom: 1px dashed #94a3b8;
      height: 35px;
      margin-bottom: 6px;
    }
    @media print {
      body { padding: 0; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>

  <div class="no-print" style="margin-bottom: 15px; text-align: right;">
    <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; font-weight: bold; border: none; border-radius: 6px; cursor: pointer;">
      🖨️ Print Day Book
    </button>
  </div>

  <div class="header">
    <div>
      <div class="school-title">{{ $school->school_name ?? 'DASA EDUGROUP' }}</div>
      <div class="sheet-title">Daily Cash &amp; Account Day Book (Reconciliation Sheet)</div>
    </div>
    <div class="meta">
      <strong>Closing Date:</strong> {{ date('d M Y', strtotime($date)) }}<br>
      <strong>Generated:</strong> {{ date('d-m-Y h:i A') }}
    </div>
  </div>

  {{-- Top KPIs --}}
  <div class="summary-grid">
    <div class="summary-card">
      <div class="card-label">Day Total Inflow</div>
      <div class="card-val" style="color: #15803d;">₹{{ number_format($totalInflow, 2) }}</div>
    </div>
    <div class="summary-card">
      <div class="card-label">Day Total Outflow</div>
      <div class="card-val" style="color: #be123c;">₹{{ number_format($totalOutflow, 2) }}</div>
    </div>
    <div class="summary-card">
      <div class="card-label">Net Day Surplus / Deficit</div>
      <div class="card-val" style="color: {{ $netAmount >= 0 ? '#15803d' : '#be123c' }};">
        {{ $netAmount >= 0 ? '+' : '' }}₹{{ number_format($netAmount, 2) }}
      </div>
    </div>
    <div class="summary-card">
      <div class="card-label">Total Transactions</div>
      <div class="card-val" style="color: #0f172a;">{{ $ledger->count() }} Entries</div>
    </div>
  </div>

  {{-- Account Breakdown --}}
  <div class="summary-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="summary-card" style="border-left: 3px solid #9333ea;">
      <div class="card-label">UPI Account Day Movement</div>
      <div style="font-size: 11px; margin-top: 4px;">
        In: <strong class="font-mono text-right">+₹{{ number_format($upiInflow, 2) }}</strong> &bull;
        Out: <strong class="font-mono text-right">-₹{{ number_format($upiOutflow, 2) }}</strong>
      </div>
    </div>
    <div class="summary-card" style="border-left: 3px solid #16a34a;">
      <div class="card-label">Cash Box 1 (Front Desk) Movement</div>
      <div style="font-size: 11px; margin-top: 4px;">
        In: <strong class="font-mono text-right">+₹{{ number_format($box1Inflow, 2) }}</strong> &bull;
        Out: <strong class="font-mono text-right">-₹{{ number_format($box1Outflow, 2) }}</strong>
      </div>
    </div>
    <div class="summary-card" style="border-left: 3px solid #2563eb;">
      <div class="card-label">Cash Box 2 (Accounts Dept) Movement</div>
      <div style="font-size: 11px; margin-top: 4px;">
        In: <strong class="font-mono text-right">+₹{{ number_format($box2Inflow, 2) }}</strong> &bull;
        Out: <strong class="font-mono text-right">-₹{{ number_format($box2Outflow, 2) }}</strong>
      </div>
    </div>
  </div>

  {{-- Ledger Table --}}
  <table>
    <thead>
      <tr>
        <th style="width: 40px;" class="text-center">#</th>
        <th style="width: 70px;">Time</th>
        <th style="width: 120px;">Voucher / Receipt</th>
        <th style="width: 60px;">Type</th>
        <th>Party / Student / Payee</th>
        <th>Purpose / Description</th>
        <th style="width: 90px;">Account / Box</th>
        <th style="width: 90px;" class="text-right">Inflow (₹)</th>
        <th style="width: 90px;" class="text-right">Outflow (₹)</th>
        <th style="width: 70px;" class="text-center">Staff</th>
      </tr>
    </thead>
    <tbody>
      @forelse($ledger as $idx => $row)
        <tr>
          <td class="text-center font-mono">{{ $idx + 1 }}</td>
          <td class="font-mono">{{ $row->time ?: '—' }}</td>
          <td class="font-mono font-bold">{{ $row->voucher_no }}</td>
          <td>
            @if($row->type === 'inflow')
              <span class="badge badge-inflow">Inflow</span>
            @elseif($row->type === 'outflow')
              <span class="badge badge-outflow">Outflow</span>
            @else
              <span class="badge badge-trf">Transfer</span>
            @endif
          </td>
          <td>
            <strong>{{ $row->party_name }}</strong>
            @if($row->party_subtitle)
              <div style="font-size: 9px; color: #64748b;">{{ $row->party_subtitle }}</div>
            @endif
          </td>
          <td>{{ $row->category_label }} — {{ $row->description }}</td>
          <td><strong>{{ $row->account_label }}</strong></td>
          <td class="text-right font-mono" style="color: #15803d;">
            {{ $row->type === 'inflow' ? number_format($row->amount, 2) : '—' }}
          </td>
          <td class="text-right font-mono" style="color: #be123c;">
            {{ $row->type === 'outflow' ? number_format($row->amount, 2) : '—' }}
          </td>
          <td class="text-center" style="font-size: 9.5px;">{{ $row->staff_name }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
            No transactions recorded on this date.
          </td>
        </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr style="background: #f1f5f9; font-weight: 800;">
        <td colspan="7" class="text-right" style="text-transform: uppercase;">Totals For The Day:</td>
        <td class="text-right font-mono" style="color: #15803d; font-size: 11px;">₹{{ number_format($totalInflow, 2) }}</td>
        <td class="text-right font-mono" style="color: #be123c; font-size: 11px;">₹{{ number_format($totalOutflow, 2) }}</td>
        <td></td>
      </tr>
    </tfoot>
  </table>

  {{-- Signatures --}}
  <div class="sign-section">
    <div>
      <div class="sign-line"></div>
      Cashier / Front Desk Officer
    </div>
    <div>
      <div class="sign-line"></div>
      Accounts Manager / Accountant
    </div>
    <div>
      <div class="sign-line"></div>
      Principal / Correspondent
    </div>
  </div>

</body>
</html>
