<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; padding: 50px; line-height: 1.7; }
  .header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 12px; margin-bottom: 24px; }
  .school-name { font-size: 18px; font-weight: bold; color: #1e3a5f; }
  .school-sub { font-size: 10px; color: #555; margin-top: 3px; }
  .title { text-align: center; font-size: 14px; font-weight: bold; letter-spacing: 1px; text-decoration: underline; margin-bottom: 24px; text-transform: uppercase; }
  .meta { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 10px; }
  p { margin-bottom: 14px; }
  .seal { margin-top: 40px; display: flex; justify-content: space-between; align-items: flex-end; }
  .sig-box { text-align: center; border-top: 1px solid #333; width: 180px; padding-top: 4px; font-size: 9px; color: #555; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school->school_name ?? $school->name ?? 'SCHOOL NAME' }}</div>
  <div class="school-sub">{{ $school->address ?? '' }}</div>
</div>

<div class="title">No Objection Certificate</div>
<div class="meta">
  <span><strong>Ref:</strong> {{ $refNumber }}</span>
  <span><strong>Date:</strong> {{ now()->format('d F Y') }}</span>
</div>

<p>To Whom It May Concern,</p>

<p>
  This is to certify that <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>,
  Employee Number <strong>{{ $employee->employee_number }}</strong>, is / was employed with
  <strong>{{ $school->school_name ?? $school->name ?? 'our institution' }}</strong> as
  <strong>{{ $employee->designation ?? 'Staff Member' }}</strong> in the
  <strong>{{ $employee->department ?? '' }}</strong> department.
</p>

<p>
  We hereby confirm that we have <strong>no objection</strong> to the above-named employee
  applying for any position / passport / visa / educational course or any other purpose
  for which this certificate has been requested.
</p>

<p>
  This certificate is issued at the request of the individual for the purpose stated by them.
  The management holds no responsibility for the end-use of this document.
</p>

<div class="seal">
  <div></div>
  <div class="sig-box">Principal / Director<br>{{ $school->school_name ?? 'School Name' }}<br><br>Official Seal</div>
</div>
</body>
</html>
