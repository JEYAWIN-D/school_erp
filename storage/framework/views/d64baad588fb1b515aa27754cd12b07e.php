<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purchase Order — <?php echo e($po->po_number); ?></title>
<style>
  body { font-family: Arial, sans-serif; font-size: 13px; color: #1e293b; margin: 0; padding: 24px; }
  .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #1e293b; padding-bottom: 12px; }
  .header h1 { font-size: 20px; margin: 0 0 4px; }
  .header p  { margin: 0; color: #64748b; font-size: 12px; }
  .meta { display: flex; justify-content: space-between; margin-bottom: 20px; }
  .meta-block p { margin: 3px 0; }
  .meta-block strong { min-width: 110px; display: inline-block; }
  .vendor-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 16px; }
  .vendor-box h3 { margin: 0 0 6px; font-size: 13px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
  table { width: 100%; border-collapse: collapse; margin-top: 16px; }
  th { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-size: 12px; }
  td { border: 1px solid #e2e8f0; padding: 7px 10px; font-size: 12px; }
  .total-row td { font-weight: bold; background: #f8fafc; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
  .badge-draft    { background: #f1f5f9; color: #475569; }
  .badge-sent     { background: #dbeafe; color: #1e40af; }
  .badge-partial  { background: #fef3c7; color: #92400e; }
  .badge-received { background: #d1fae5; color: #065f46; }
  .sigs { display: flex; gap: 48px; margin-top: 48px; }
  .sig { text-align: center; }
  .sig-line { border-top: 1px solid #94a3b8; width: 160px; margin: 0 auto 4px; padding-top: 4px; }
  .terms { margin-top: 24px; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 12px; }
  @media print { button, a.back-btn { display: none; } }
</style>
</head>
<body>
<div class="header">
  <h1>Purchase Order</h1>
  <p><?php echo e(config('app.name', 'DASA EduERP')); ?></p>
</div>

<div class="meta">
  <div class="meta-block">
    <p><strong>PO Number:</strong> <?php echo e($po->po_number); ?></p>
    <p><strong>Order Date:</strong> <?php echo e(\Carbon\Carbon::parse($po->order_date)->format('d M Y')); ?></p>
    <?php if($po->delivery_date): ?>
    <p><strong>Delivery By:</strong> <?php echo e(\Carbon\Carbon::parse($po->delivery_date)->format('d M Y')); ?></p>
    <?php endif; ?>
    <p><strong>Status:</strong> <span class="badge badge-<?php echo e($po->status); ?>"><?php echo e(ucfirst($po->status)); ?></span></p>
  </div>
  <div class="meta-block">
    <?php if($po->payment_terms): ?>
    <p><strong>Payment Terms:</strong> <?php echo e($po->payment_terms); ?></p>
    <?php endif; ?>
    <?php if($po->shipping_address): ?>
    <p><strong>Ship To:</strong> <?php echo e($po->shipping_address); ?></p>
    <?php endif; ?>
  </div>
</div>

<div class="vendor-box">
  <h3>Vendor Details</h3>
  <p><strong>Name:</strong> <?php echo e($po->vendor?->name ?? '—'); ?></p>
  <?php if($po->vendor?->email): ?><p><strong>Email:</strong> <?php echo e($po->vendor->email); ?></p><?php endif; ?>
  <?php if($po->vendor?->phone): ?><p><strong>Phone:</strong> <?php echo e($po->vendor->phone); ?></p><?php endif; ?>
  <?php if($po->vendor?->address): ?><p><strong>Address:</strong> <?php echo e($po->vendor->address); ?></p><?php endif; ?>
  <?php if($po->vendor?->gstin): ?><p><strong>GSTIN:</strong> <?php echo e($po->vendor->gstin); ?></p><?php endif; ?>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Item Description</th>
      <th style="text-align:center">Qty</th>
      <th style="text-align:right">Unit Price (₹)</th>
      <th style="text-align:right">Total (₹)</th>
      <th>Received</th>
    </tr>
  </thead>
  <tbody>
    <?php $grandTotal = 0; ?>
    <?php $__currentLoopData = $po->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
      $lineTotal = ($line->unit_price ?? 0) * $line->quantity;
      $grandTotal += $lineTotal;
    ?>
    <tr>
      <td><?php echo e($i + 1); ?></td>
      <td><?php echo e($line->inventoryItem?->name ?? $line->description ?? '—'); ?></td>
      <td style="text-align:center"><?php echo e($line->quantity); ?></td>
      <td style="text-align:right"><?php echo e($line->unit_price ? number_format($line->unit_price, 2) : '—'); ?></td>
      <td style="text-align:right"><?php echo e($lineTotal > 0 ? number_format($lineTotal, 2) : '—'); ?></td>
      <td style="text-align:center"><?php echo e($line->received_qty ?? 0); ?></td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <tr class="total-row">
      <td colspan="4" style="text-align:right">Grand Total</td>
      <td style="text-align:right">₹<?php echo e(number_format($grandTotal, 2)); ?></td>
      <td></td>
    </tr>
  </tbody>
</table>

<?php if($po->notes): ?>
<div style="margin-top:16px;font-size:12px;color:#475569;">
  <strong>Notes:</strong> <?php echo e($po->notes); ?>

</div>
<?php endif; ?>

<div class="sigs">
  <div class="sig">
    <div class="sig-line">Prepared By</div>
  </div>
  <div class="sig">
    <div class="sig-line">Authorized By</div>
  </div>
  <div class="sig">
    <div class="sig-line">Vendor Acknowledgement</div>
  </div>
</div>

<div class="terms">
  This Purchase Order is subject to the terms and conditions of the institution. Please confirm receipt and acceptance of this order.
</div>

<div style="margin-top:24px;text-align:right">
  <button onclick="window.print()" style="padding:8px 20px;background:#4f46e5;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;">
    Print / Save PDF
  </button>
  <a href="<?php echo e(route('inventory.purchase-orders')); ?>" class="back-btn" style="margin-left:8px;padding:8px 20px;background:#f1f5f9;color:#1e293b;border:none;border-radius:6px;cursor:pointer;text-decoration:none;font-size:13px;">← Back</a>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\inventory\po-print.blade.php ENDPATH**/ ?>