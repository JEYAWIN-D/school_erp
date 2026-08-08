<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: 'Times New Roman', serif; font-size: 12px; margin: 0; padding: 25px; }
.header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
.school-name { font-size: 18px; font-weight: bold; }
.school-info { font-size: 10px; color: #555; margin-top: 3px; }
.app-title { font-size: 14px; font-weight: bold; margin: 12px 0 4px; text-decoration: underline; }
.app-number { font-size: 11px; font-weight: bold; color: #333; text-align: right; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
td { padding: 5px 8px; vertical-align: top; border-bottom: 1px dotted #ccc; }
td:first-child { width: 40%; font-weight: bold; color: #444; }
.section-title { font-size: 11px; font-weight: bold; background: #f5f5f5; padding: 5px 8px; margin-top: 12px; border-left: 3px solid #333; }
.status-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
.footer { margin-top: 30px; display: flex; justify-content: space-between; }
.sign-line { border-top: 1px solid #000; width: 150px; text-align: center; padding-top: 4px; font-size: 10px; margin-top: 40px; }
</style>
</head>
<body>
<div class="header">
  <div class="school-name">{{ $school?->school_name ?? 'School' }}</div>
  @if($school?->address)<div class="school-info">{{ $school->address }}</div>@endif
  @if($school?->phone)<div class="school-info">Ph: {{ $school->phone }}@if($school?->email) | {{ $school->email }}@endif</div>@endif
  @if($school?->affiliation_number)<div class="school-info">Affiliation No: {{ $school->affiliation_number }}</div>@endif
</div>

<div class="app-number">Application No: {{ $app->application_number }}</div>
<div class="app-title">ADMISSION APPLICATION FORM</div>
<div style="font-size:10px;color:#666;margin-bottom:12px;">
  Form: {{ $app->formConfig?->title }}
  @if($app->formConfig?->class) | Class: {{ $app->formConfig->class->name }}@endif
  | Submitted: {{ $app->created_at->format('d M Y') }}
  | Status: <strong>{{ ucwords(str_replace('_',' ',$app->status)) }}</strong>
</div>

<div class="section-title">STUDENT DETAILS</div>
<table>
  @foreach($app->form_data ?? [] as $key => $value)
  @if(!in_array($key, ['parent_name','parent_mobile','parent_email','address']))
  <tr>
    <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
    <td>{{ $value }}</td>
  </tr>
  @endif
  @endforeach
</table>

<div class="section-title">PARENT / GUARDIAN DETAILS</div>
<table>
  <tr><td>Parent/Guardian Name</td><td>{{ $app->parent_name ?? ($app->form_data['parent_name'] ?? '—') }}</td></tr>
  <tr><td>Mobile</td><td>{{ $app->parent_mobile }}</td></tr>
  <tr><td>Email</td><td>{{ $app->parent_email ?? ($app->form_data['parent_email'] ?? '—') }}</td></tr>
  @if(!empty($app->form_data['address']))
  <tr><td>Address</td><td>{{ $app->form_data['address'] }}</td></tr>
  @endif
</table>

<div class="section-title">DOCUMENTS SUBMITTED</div>
<table>
  @forelse($app->documents ?? [] as $name => $path)
  <tr>
    <td>{{ ucwords(str_replace('_', ' ', $name)) }}</td>
    <td>Uploaded</td>
  </tr>
  @empty
  <tr><td colspan="2">No documents uploaded</td></tr>
  @endforelse
</table>

@if($app->admin_notes)
<div class="section-title">ADMIN NOTES</div>
<p style="padding:5px 8px;font-style:italic;color:#555;">{{ $app->admin_notes }}</p>
@endif

<div class="footer">
  <div class="sign-line">Applicant / Parent Signature</div>
  <div class="sign-line">Admin / Admissions Office</div>
</div>
</body>
</html>
