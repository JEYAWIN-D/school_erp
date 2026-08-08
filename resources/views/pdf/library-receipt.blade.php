<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }
  .wrap { padding: 16px; max-width: 300px; }
  .center { text-align: center; }
  .school-name { font-size: 13px; font-weight: bold; color: #1e1b4b; }
  .school-sub { font-size: 9px; color: #64748b; margin-top: 2px; }
  .divider { border-top: 1px dashed #cbd5e1; margin: 10px 0; }
  .title { font-size: 12px; font-weight: bold; text-align: center; margin: 6px 0; text-transform: uppercase; letter-spacing: 1px; }
  .row { display: flex; justify-content: space-between; padding: 3px 0; }
  .label { color: #64748b; font-size: 10px; }
  .value { font-weight: bold; font-size: 10px; }
  .book-title { font-size: 12px; font-weight: bold; color: #1e1b4b; margin: 6px 0; text-align: center; }
  .status-issued { background: #dbeafe; color: #1d4ed8; padding: 3px 10px; border-radius: 10px; display: inline-block; font-size: 10px; font-weight: bold; }
  .status-returned { background: #dcfce7; color: #166534; padding: 3px 10px; border-radius: 10px; display: inline-block; font-size: 10px; font-weight: bold; }
  .fine-box { background: #fef9c3; border: 1px solid #fde68a; padding: 6px 10px; border-radius: 4px; margin: 8px 0; text-align: center; }
  .footer { font-size: 9px; color: #94a3b8; text-align: center; margin-top: 10px; }
</style>
</head>
<body>
<div class="wrap">
  <div class="center">
    <div class="school-name">{{ $school->school_name ?? config('app.name') }}</div>
    <div class="school-sub">Library Receipt</div>
  </div>
  <div class="divider"></div>

  @php $isReturn = $issue->return_date !== null; @endphp
  <div class="title">{{ $isReturn ? 'Book Return Receipt' : 'Book Issue Receipt' }}</div>

  <div class="book-title">{{ $issue->book->title }}</div>
  @if($issue->book->author)
    <div class="center" style="font-size:9px;color:#64748b;margin-bottom:6px;">by {{ $issue->book->author }}</div>
  @endif

  <div class="divider"></div>

  @php
    $member = $issue->student ?? $issue->employee;
    $memberName = $issue->student ? ($issue->student->first_name . ' ' . $issue->student->last_name) : ($issue->employee->name ?? 'Staff');
  @endphp

  <div class="row"><span class="label">Receipt No.</span><span class="value">#{{ $issue->id }}</span></div>
  <div class="row"><span class="label">Member</span><span class="value">{{ $memberName }}</span></div>
  @if($issue->student)
  <div class="row"><span class="label">Adm. No.</span><span class="value">{{ $issue->student->admission_number ?? '—' }}</span></div>
  @endif
  <div class="row"><span class="label">Issue Date</span><span class="value">{{ $issue->issue_date->format('d M Y') }}</span></div>
  <div class="row"><span class="label">Due Date</span><span class="value">{{ $issue->due_date->format('d M Y') }}</span></div>
  @if($isReturn)
  <div class="row"><span class="label">Return Date</span><span class="value">{{ $issue->return_date->format('d M Y') }}</span></div>
  @endif
  <div class="row"><span class="label">Status</span>
    <span class="{{ $isReturn ? 'status-returned' : 'status-issued' }}">{{ $isReturn ? 'Returned' : 'Issued' }}</span>
  </div>

  @if($issue->fine_amount > 0)
  <div class="fine-box">
    Fine: ₹{{ number_format($issue->fine_amount, 2) }}
    — {{ $issue->fine_paid ? 'PAID' : 'PENDING' }}
  </div>
  @endif

  <div class="divider"></div>

  <div class="footer">
    Generated: {{ now()->format('d M Y, h:i A') }}<br>
    {{ $school->school_name ?? config('app.name') }} Library
  </div>
</div>
</body>
</html>
