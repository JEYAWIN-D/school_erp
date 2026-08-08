<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purchase Requisition — {{ $pr->pr_number }}</title>
<style>
  body { font-family: Arial, sans-serif; font-size: 13px; color: #1e293b; margin: 0; padding: 24px; }
  .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #1e293b; padding-bottom: 12px; }
  .header h1 { font-size: 20px; margin: 0 0 4px; }
  .header p  { margin: 0; color: #64748b; font-size: 12px; }
  .meta { display: flex; justify-content: space-between; margin-bottom: 20px; }
  .meta-block p { margin: 3px 0; }
  .meta-block strong { min-width: 110px; display: inline-block; }
  table { width: 100%; border-collapse: collapse; margin-top: 16px; }
  th { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-size: 12px; }
  td { border: 1px solid #e2e8f0; padding: 7px 10px; font-size: 12px; }
  .total-row td { font-weight: bold; background: #f8fafc; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
  .badge-pending  { background: #fef3c7; color: #92400e; }
  .badge-approved { background: #d1fae5; color: #065f46; }
  .badge-rejected { background: #fee2e2; color: #991b1b; }
  .sigs { display: flex; gap: 48px; margin-top: 48px; }
  .sig { text-align: center; }
  .sig-line { border-top: 1px solid #94a3b8; width: 160px; margin: 0 auto 4px; padding-top: 4px; }
  @media print { button { display: none; } }
</style>
</head>
<body>
<div class="header">
  <h1>Purchase Requisition</h1>
  <p>{{ config('app.name', 'DASA EduERP') }}</p>
</div>

<div class="meta">
  <div class="meta-block">
    <p><strong>PR Number:</strong> {{ $pr->pr_number }}</p>
    <p><strong>Requested By:</strong> {{ $pr->requestedBy?->name ?? '—' }}</p>
    <p><strong>Date:</strong> {{ $pr->created_at->format('d M Y') }}</p>
  </div>
  <div class="meta-block">
    <p><strong>Required By:</strong> {{ \Carbon\Carbon::parse($pr->required_by)->format('d M Y') }}</p>
    <p><strong>Status:</strong> <span class="badge badge-{{ $pr->status }}">{{ ucfirst($pr->status) }}</span></p>
    @if($pr->purpose)
    <p><strong>Purpose:</strong> {{ $pr->purpose }}</p>
    @endif
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Item</th>
      <th>Category</th>
      <th style="text-align:center">Qty</th>
      <th style="text-align:right">Est. Price (₹)</th>
      <th style="text-align:right">Total (₹)</th>
      <th>Remark</th>
    </tr>
  </thead>
  <tbody>
    @php $grandTotal = 0; @endphp
    @foreach($pr->items as $i => $line)
    @php
      $lineTotal = ($line->estimated_price ?? 0) * $line->quantity;
      $grandTotal += $lineTotal;
    @endphp
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $line->inventoryItem?->name ?? '—' }}</td>
      <td>{{ $line->inventoryItem?->category ?? '—' }}</td>
      <td style="text-align:center">{{ $line->quantity }}</td>
      <td style="text-align:right">{{ $line->estimated_price ? number_format($line->estimated_price, 2) : '—' }}</td>
      <td style="text-align:right">{{ $lineTotal > 0 ? number_format($lineTotal, 2) : '—' }}</td>
      <td>{{ $line->remark ?? '—' }}</td>
    </tr>
    @endforeach
    <tr class="total-row">
      <td colspan="5" style="text-align:right">Estimated Total</td>
      <td style="text-align:right">₹{{ number_format($grandTotal, 2) }}</td>
      <td></td>
    </tr>
  </tbody>
</table>

<div class="sigs">
  <div class="sig">
    <div class="sig-line">Requested By</div>
    <p>{{ $pr->requestedBy?->name ?? '—' }}</p>
  </div>
  <div class="sig">
    <div class="sig-line">Approved By</div>
    <p>&nbsp;</p>
  </div>
  <div class="sig">
    <div class="sig-line">Principal / Director</div>
    <p>&nbsp;</p>
  </div>
</div>

<div style="margin-top:24px;text-align:right">
  <button onclick="window.print()" style="padding:8px 20px;background:#4f46e5;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;">
    Print / Save PDF
  </button>
  <a href="{{ route('inventory.requisitions') }}" style="margin-left:8px;padding:8px 20px;background:#f1f5f9;color:#1e293b;border:none;border-radius:6px;cursor:pointer;text-decoration:none;font-size:13px;">← Back</a>
</div>
</body>
</html>
