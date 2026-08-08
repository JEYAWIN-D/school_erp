<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 11px; color: #1e293b; padding: 20px; }
  .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 14px; }
  .school-name { font-size: 14px; font-weight: bold; color: #1e40af; }
  .doc-title { font-size: 12px; font-weight: bold; background: #1e40af; color: white; padding: 4px 16px; display: inline-block; border-radius: 4px; margin-top: 6px; letter-spacing: 1px; }
  .pass-id { font-size: 10px; color: #64748b; margin-top: 4px; }
  .content { margin-top: 14px; }
  .row { display: flex; margin-bottom: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px; }
  .row:last-child { border-bottom: none; }
  .lbl { width: 120px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: bold; flex-shrink: 0; padding-top: 1px; }
  .val { flex: 1; font-weight: 500; }
  .highlight { background: #eff6ff; border-left: 3px solid #1e40af; padding: 8px 12px; margin: 14px 0; border-radius: 2px; }
  .highlight .val { font-size: 13px; font-weight: bold; color: #1e40af; }
  .qr-section { text-align: center; margin-top: 16px; padding-top: 14px; border-top: 1px dashed #cbd5e1; }
  .qr-section img { width: 120px; height: 120px; }
  .qr-caption { font-size: 8px; color: #94a3b8; margin-top: 4px; }
  .status-box { display: inline-block; background: #dcfce7; color: #15803d; font-weight: bold; font-size: 9px; padding: 2px 10px; border-radius: 12px; letter-spacing: 0.5px; margin-left: 4px; }
  .signature-row { display: flex; justify-content: space-between; margin-top: 20px; }
  .sign-box { text-align: center; width: 45%; }
  .sign-line { border-top: 1px solid #334155; margin-bottom: 4px; }
  .sign-label { font-size: 8px; color: #64748b; text-transform: uppercase; }
  footer { text-align: center; margin-top: 20px; font-size: 7px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 6px; }
</style>
</head>
<body>

<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'School' }}</div>
  <div class="doc-title">HOSTEL GATE PASS</div>
  <div class="pass-id">Pass #{{ str_pad($outpass->id, 6, '0', STR_PAD_LEFT) }} &nbsp;·&nbsp;
    <span class="status-box">APPROVED</span>
  </div>
</div>

<div class="content">
  <div class="row">
    <div class="lbl">Student</div>
    <div class="val">{{ $outpass->student?->full_name }}</div>
  </div>
  <div class="row">
    <div class="lbl">Room</div>
    <div class="val">{{ $outpass->allotment?->room?->room_number ?? '—' }}</div>
  </div>
  <div class="highlight">
    <div class="row" style="border:none;margin:0;padding:0;">
      <div class="lbl" style="color:#1e40af;">From</div>
      <div class="val">{{ \Carbon\Carbon::parse($outpass->from_datetime)->format('d M Y, h:i A') }}</div>
    </div>
    <div class="row" style="border:none;margin-top:4px;padding:0;">
      <div class="lbl" style="color:#1e40af;">To</div>
      <div class="val">{{ \Carbon\Carbon::parse($outpass->to_datetime)->format('d M Y, h:i A') }}</div>
    </div>
  </div>
  @if($outpass->destination)
  <div class="row">
    <div class="lbl">Destination</div>
    <div class="val">{{ $outpass->destination }}</div>
  </div>
  @endif
  @if($outpass->reason)
  <div class="row">
    <div class="lbl">Purpose</div>
    <div class="val">{{ $outpass->reason }}</div>
  </div>
  @endif
  @if($outpass->parent_contact)
  <div class="row">
    <div class="lbl">Parent Contact</div>
    <div class="val">{{ $outpass->parent_contact }}</div>
  </div>
  @endif
  <div class="row">
    <div class="lbl">Issued On</div>
    <div class="val">{{ \Carbon\Carbon::parse($outpass->approved_at ?? $outpass->updated_at)->format('d M Y, h:i A') }}</div>
  </div>
</div>

@if($qrCode)
<div class="qr-section">
  <img src="data:image/png;base64,{{ $qrCode }}" alt="Gate Pass QR">
  <div class="qr-caption">Scan to verify gate pass authenticity</div>
</div>
@endif

<div class="signature-row">
  <div class="sign-box">
    <div class="sign-line" style="margin-bottom:4px;">&nbsp;</div>
    <div class="sign-label">Warden Signature</div>
  </div>
  <div class="sign-box">
    <div class="sign-line" style="margin-bottom:4px;">&nbsp;</div>
    <div class="sign-label">Gate / Security</div>
  </div>
</div>

<footer>
  {{ $school?->school_name ?? '' }} — This gate pass is valid only for the period mentioned above.
  Report to the hostel warden immediately upon return.
</footer>
</body>
</html>
