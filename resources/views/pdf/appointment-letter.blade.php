<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Appointment Letter — {{ $employee->full_name }}</title>
<style>
  @page { margin: 25mm 20mm; }
  body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #1e293b; line-height: 1.7; background: #fff; }
  .letterhead { text-align: center; border-bottom: 3px double #1e3a8a; padding-bottom: 12px; margin-bottom: 25px; }
  .school-title { font-size: 22px; font-weight: 800; color: #1e3a8a; text-transform: uppercase; letter-spacing: 1px; }
  .school-sub { font-size: 11px; color: #475569; margin-top: 3px; font-weight: 600; }
  .meta-bar { display: table; width: 100%; margin-bottom: 20px; font-size: 11px; color: #475569; }
  .meta-left { display: table-cell; text-align: left; }
  .meta-right { display: table-cell; text-align: right; }
  .recipient-box { margin-bottom: 20px; background: #f8fafc; padding: 12px 15px; border-left: 4px solid #1e3a8a; border-radius: 4px; }
  .recipient-name { font-size: 14px; font-weight: 700; color: #0f172a; }
  .doc-title { font-size: 16px; font-weight: 800; color: #1e3a8a; text-align: center; text-decoration: underline; letter-spacing: 1px; margin: 25px 0 15px 0; text-transform: uppercase; }
  .body-text { margin-bottom: 15px; text-align: justify; }
  .terms-table { width: 100%; border-collapse: collapse; margin: 20px 0; border: 1px solid #cbd5e1; }
  .terms-table th, .terms-table td { border: 1px solid #e2e8f0; padding: 8px 12px; font-size: 11px; }
  .terms-table th { background: #f1f5f9; color: #1e3a8a; font-weight: 700; text-align: left; width: 35%; }
  .terms-table td { color: #0f172a; font-weight: 600; }
  .signature-section { margin-top: 50px; display: table; width: 100%; }
  .sig-block { display: table-cell; width: 50%; vertical-align: bottom; }
  .sig-left { text-align: left; }
  .sig-right { text-align: right; }
  .sig-line { display: inline-block; border-top: 1.5px solid #334155; width: 180px; padding-top: 5px; font-size: 11px; font-weight: 700; color: #0f172a; }
</style>
</head>
<body>

  {{-- Letterhead --}}
  <div class="letterhead">
    <div class="school-title">{{ $school->school_name ?? config('app.name', 'DASA EduERP') }}</div>
    <div class="school-sub">{{ $school->address ?? 'Main Campus, Educational Zone' }} &bull; Ph: {{ $school->phone ?? '+91 9876543210' }}</div>
  </div>

  {{-- Meta Ref & Date --}}
  <div class="meta-bar">
    <div class="meta-left"><strong>Ref No:</strong> {{ $refNumber ?? 'APT-'.date('Y').'-'.str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</div>
    <div class="meta-right"><strong>Date:</strong> {{ now()->format('F d, Y') }}</div>
  </div>

  {{-- Recipient Info --}}
  <div class="recipient-box">
    <div class="recipient-name">To, {{ $employee->full_name }}</div>
    <div>Emp Code: <strong>{{ $employee->employee_code }}</strong></div>
    <div>{{ $employee->residential_address ?? $employee->address ?? 'Residential Address' }}</div>
    <div>Mobile: +91 {{ $employee->mobile }} &bull; Email: {{ $employee->official_email ?? $employee->email }}</div>
  </div>

  {{-- Document Title --}}
  <div class="doc-title">LETTER OF APPOINTMENT</div>

  {{-- Body Paragraph --}}
  <div class="body-text">
    Dear <strong>{{ $employee->full_name }}</strong>,
  </div>
  <div class="body-text">
    With reference to your application and subsequent interview, management is pleased to appoint you as <strong>{{ is_object($employee->designation) ? $employee->designation->name : ($employee->designation ?? 'Staff Member') }}</strong> in the <strong>{{ is_object($employee->department) ? $employee->department->name : ($employee->department ?? 'General') }}</strong> Department at <strong>{{ $school->school_name ?? 'DASA EduERP' }}</strong> under the following terms and conditions:
  </div>

  {{-- Terms Table --}}
  <table class="terms-table">
    <tr>
      <th>Employee Name</th>
      <td>{{ $employee->full_name }}</td>
    </tr>
    <tr>
      <th>Employee Code</th>
      <td>{{ $employee->employee_code }}</td>
    </tr>
    <tr>
      <th>Designation</th>
      <td>{{ is_object($employee->designation) ? $employee->designation->name : ($employee->designation ?? 'Staff Member') }}</td>
    </tr>
    <tr>
      <th>Category</th>
      <td>{{ $employee->category_label }}</td>
    </tr>
    <tr>
      <th>Date of Joining</th>
      <td>{{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('F d, Y') : ($employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('F d, Y') : now()->format('F d, Y')) }}</td>
    </tr>
    <tr>
      <th>Basic Monthly Salary</th>
      <td>₹{{ number_format($employee->basic_salary > 0 ? $employee->basic_salary : 25000, 2) }} / month</td>
    </tr>
  </table>

  <div class="body-text">
    You will be governed by the standard service rules, code of conduct, and regulations of the institution as amended from time to time. Please sign and return the duplicate copy of this letter as acceptance of this offer.
  </div>
  <div class="body-text">
    We welcome you to <strong>{{ $school->school_name ?? 'DASA EduERP' }}</strong> and wish you a successful professional career with us.
  </div>

  {{-- Signatures --}}
  <div class="signature-section">
    <div class="sig-block sig-left">
      <div class="sig-line">Employee Signature</div>
      <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Accepted Terms & Conditions</div>
    </div>
    <div class="sig-block sig-right">
      <div class="sig-line">Authorized Signatory</div>
      <div style="font-size: 10px; color: #64748b; margin-top: 2px;">{{ $school->school_name ?? 'DASA EduERP' }} Management</div>
    </div>
  </div>

</body>
</html>
