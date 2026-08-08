<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
* { box-sizing: border-box; }
body { font-family: 'Times New Roman', serif; font-size: 12px; color: #111; margin: 0; padding: 30px 40px; background: #fff; }
.header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1e3a8a; padding-bottom: 16px; }
.school-name { font-size: 22px; font-weight: bold; color: #1e3a8a; }
.school-info  { font-size: 11px; color: #555; margin-top: 3px; }
.doc-title    { font-size: 17px; font-weight: bold; text-align: center; margin: 18px 0 6px; letter-spacing: 1px; text-decoration: underline; color: #1e3a8a; }
.ref-row      { display: flex; justify-content: space-between; font-size: 11px; color: #555; margin-bottom: 14px; }
.salutation   { margin-bottom: 12px; font-size: 13px; }
.body-text    { line-height: 1.8; font-size: 12.5px; text-align: justify; }
.highlight    { font-weight: bold; }
table.details { width: 100%; border-collapse: collapse; margin: 16px 0; background: #f8fafc; border: 1px solid #cbd5e1; }
table.details td { padding: 7px 12px; font-size: 12px; border-bottom: 1px solid #e2e8f0; }
table.details td:first-child { font-weight: bold; width: 40%; color: #334155; }
.footer       { margin-top: 40px; display: flex; justify-content: space-between; align-items: flex-end; }
.sign-block   { text-align: center; width: 38%; }
.sign-line    { border-top: 1px solid #000; margin-top: 60px; padding-top: 5px; font-size: 11px; }
.seal-box     { width: 100px; height: 100px; border: 1px dashed #aaa; text-align: center; padding-top: 35px; color: #aaa; font-size: 10px; }
.notice       { margin-top: 24px; background: #eff6ff; border: 1px solid #bfdbfe; padding: 10px 12px; font-size: 11px; border-radius: 4px; }
.notice strong { color: #1e40af; }
</style>
</head>
<body>

<div class="header">
  <div class="school-name">{{ $school->school_name ?? config('app.name') }}</div>
  <div class="school-info">{{ $school->address ?? '' }}</div>
  <div class="school-info">Ph: {{ $school->phone ?? '' }} | Email: {{ $school->email ?? '' }}</div>
  @if($school->affiliation_number ?? null)
  <div class="school-info">Affiliation No: {{ $school->affiliation_number }}</div>
  @endif
</div>

<div class="doc-title">ADMISSION CONFIRMATION LETTER</div>

<div class="ref-row">
  <span>Ref: {{ $enquiry->enquiry_number }}</span>
  <span>Date: {{ now()->format('d F Y') }}</span>
</div>

<div class="salutation">
  To,<br>
  <strong>{{ $enquiry->parent_name }}</strong><br>
  @if($enquiry->address){{ $enquiry->address }}<br>@endif
</div>

<div class="body-text">
  <p>Dear {{ $enquiry->parent_name }},</p>

  <p>We are pleased to inform you that your ward <span class="highlight">{{ $enquiry->student_name }}</span> has been provisionally admitted to <span class="highlight">{{ $school->school_name ?? config('app.name') }}</span> for the academic year <span class="highlight">{{ $enquiry->academicYear?->name ?? date('Y').'-'.(date('Y')+1) }}</span>.</p>

  <p>Please find the admission details below:</p>
</div>

<table class="details">
  <tr><td>Student Name</td><td>{{ $enquiry->student_name }}</td></tr>
  <tr><td>Class Admitted</td><td>{{ $enquiry->class?->name ?? '—' }}</td></tr>
  <tr><td>Academic Year</td><td>{{ $enquiry->academicYear?->name ?? date('Y').'-'.(date('Y')+1) }}</td></tr>
  <tr><td>Enquiry Reference</td><td>{{ $enquiry->enquiry_number }}</td></tr>
  <tr><td>Parent / Guardian</td><td>{{ $enquiry->parent_name }}</td></tr>
  <tr><td>Contact Number</td><td>{{ $enquiry->parent_mobile }}</td></tr>
  <tr><td>Date of Confirmation</td><td>{{ now()->format('d F Y') }}</td></tr>
</table>

<div class="body-text">
  <p>You are requested to report to the school office with the following documents for completing the admission formalities:</p>
  <ol style="margin-left:18px; line-height:2;">
    <li>Original Transfer Certificate (TC) from previous school</li>
    <li>Birth Certificate (original + copy)</li>
    <li>Aadhaar Card of student and parents</li>
    <li>Passport-size photographs (4 copies)</li>
    <li>Previous year mark sheet / report card</li>
    <li>Caste/Category certificate (if applicable)</li>
  </ol>
  <p>Kindly complete the formalities within <strong>7 working days</strong> of receiving this letter to secure your ward's seat.</p>
  <p>For any queries, please contact the school office. We look forward to welcoming {{ $enquiry->student_name }} into our school family.</p>
</div>

<div class="footer">
  <div class="sign-block">
    <div class="seal-box">School<br>Seal</div>
  </div>
  <div class="sign-block">
    <div class="sign-line">Principal's Signature</div>
  </div>
  <div class="sign-block">
    <div class="sign-line">Admissions In-Charge</div>
  </div>
</div>

<div class="notice">
  <strong>Note:</strong> This is a provisional confirmation letter. Admission will be confirmed only after verification of all original documents and payment of the admission fee.
</div>

</body>
</html>
