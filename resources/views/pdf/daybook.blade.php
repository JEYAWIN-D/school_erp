<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Arial, sans-serif; font-size: 10px; margin: 0; padding: 15px; }
    h2 { font-size: 15px; margin: 0 0 3px 0; }
    p { font-size: 9px; margin: 0 0 10px 0; color: #666; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
    th { background: #f1f5f9; font-size: 9px; text-transform: uppercase; font-weight: bold; color: #475569; }
    .amount { text-align: right; font-family: monospace; }
    tfoot td { font-weight: bold; background: #f8fafc; }
    .kpi-row { display: flex; gap: 20px; margin-bottom: 12px; }
    .kpi { border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 14px; flex: 1; text-align: center; }
    .kpi-val { font-size: 14px; font-weight: bold; color: #1e40af; }
    .kpi-lbl { font-size: 8px; color: #64748b; margin-top: 2px; }
  </style>
</head>
<body>
  <h2>Fee Day Book</h2>
  <p>Date: {{ request('date') ?? \Carbon\Carbon::today()->toFormattedDateString() }}</p>

  @php
    $cash   = $payments->where('payment_mode','cash')->sum('amount_paid');
    $online = $payments->whereIn('payment_mode',['online','upi','neft','rtgs'])->sum('amount_paid');
    $cheque = $payments->where('payment_mode','cheque')->sum('amount_paid');
    $total  = $payments->sum('amount_paid');
  @endphp

  <table style="width:auto; margin-bottom:14px;">
    <tr>
      <th>Cash</th><th>Online / UPI</th><th>Cheque</th><th>Total</th>
    </tr>
    <tr>
      <td class="amount">₹{{ number_format($cash,2) }}</td>
      <td class="amount">₹{{ number_format($online,2) }}</td>
      <td class="amount">₹{{ number_format($cheque,2) }}</td>
      <td class="amount" style="font-weight:bold;">₹{{ number_format($total,2) }}</td>
    </tr>
  </table>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Receipt No</th>
        <th>Student</th>
        <th>Class</th>
        <th>Fee Head</th>
        <th>Mode</th>
        <th>Ref No</th>
        <th class="amount">Amount (₹)</th>
      </tr>
    </thead>
    <tbody>
      @forelse($payments as $i => $p)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td style="font-family:monospace;">{{ $p->receipt_number }}</td>
        <td>{{ $p->enrollment?->student?->full_name ?? '—' }}</td>
        <td>{{ $p->enrollment?->class?->name ?? '—' }}</td>
        <td>{{ $p->feeHead?->name ?? 'General' }}</td>
        <td style="text-transform:capitalize;">{{ str_replace('_',' ',$p->payment_mode) }}</td>
        <td>{{ $p->transaction_reference ?? '—' }}</td>
        <td class="amount">{{ number_format($p->amount_paid, 2) }}</td>
      </tr>
      @empty
      <tr><td colspan="8" style="text-align:center; color:#94a3b8; padding:10px;">No transactions found.</td></tr>
      @endforelse
    </tbody>
    @if($payments->count())
    <tfoot>
      <tr>
        <td colspan="7" style="text-align:right;">Total</td>
        <td class="amount">₹{{ number_format($total, 2) }}</td>
      </tr>
    </tfoot>
    @endif
  </table>
</body>
</html>
