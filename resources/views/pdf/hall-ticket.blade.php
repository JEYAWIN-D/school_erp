<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
.page { width: 100%; padding: 10px; }
.header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 10px; }
.school-name { font-size: 18px; font-weight: bold; }
.ticket-title { font-size: 14px; color: #555; margin-top: 4px; }
.student-info { display: flex; justify-content: space-between; margin: 10px 0; }
.info-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
.info-table td { padding: 5px 8px; border: 1px solid #ddd; }
.info-table td:first-child { font-weight: bold; background: #f5f5f5; width: 35%; }
.schedule-table { width: 100%; border-collapse: collapse; margin: 10px 0; }
.schedule-table th { background: #333; color: white; padding: 6px; text-align: left; font-size: 11px; }
.schedule-table td { padding: 5px 6px; border: 1px solid #ddd; font-size: 11px; }
.schedule-table tr:nth-child(even) td { background: #f9f9f9; }
.footer { margin-top: 30px; display: flex; justify-content: space-between; }
.sign-area { text-align: center; width: 30%; }
.sign-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-size: 10px; }
.watermark { color: #ccc; font-size: 48px; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%) rotate(-30deg); opacity: 0.1; z-index: -1; }
.badge { background: #e8f4fd; border: 1px solid #b0d4f1; padding: 4px 10px; border-radius: 4px; font-size: 11px; }
.qr-section { text-align: center; float: right; margin-left: 10px; }
.qr-section p { font-size: 9px; color: #888; margin-top: 2px; }
</style>
</head>
<body>
<div class="page">
  <div class="header">
    <div class="school-name">{{ $school->school_name ?? 'DASA EduERP' }}</div>
    <div class="ticket-title">HALL TICKET — {{ $exam->name }}</div>
    <div style="font-size:11px;color:#888">Academic Year: {{ $academicYear->name ?? '' }}</div>
  </div>
  @if(isset($qrCode))
  <div class="qr-section">
    <img src="data:image/png;base64,{{ $qrCode }}" alt="QR Code" width="90" height="90">
    <p>Scan to Verify</p>
  </div>
  @endif
  <table class="info-table">
    <tr><td>Candidate Name</td><td>{{ $student->full_name }}</td></tr>
    <tr><td>Admission Number</td><td>{{ $student->admission_number }}</td></tr>
    <tr><td>Class / Section</td><td>{{ $enrollment?->class?->name }} — {{ $enrollment?->section?->name }}</td></tr>
    <tr><td>Roll Number</td><td>{{ $enrollment?->roll_number }}</td></tr>
    <tr><td>Date of Birth</td><td>{{ $student->dob?->format('d/m/Y') }}</td></tr>
  </table>
  <h4 style="margin:8px 0 4px">Examination Schedule</h4>
  <table class="schedule-table">
    <thead><tr>
      <th>Date</th><th>Subject</th><th>Time</th><th>Duration</th><th>Max Marks</th><th>Venue</th>
    </tr></thead>
    <tbody>
      @foreach($schedules as $s)
      <tr>
        <td>{{ \Carbon\Carbon::parse($s->exam_date)->format('d M Y') }}</td>
        <td>{{ $s->subject?->name }}</td>
        <td>{{ $s->start_time }} – {{ $s->end_time }}</td>
        <td>{{ $s->duration_minutes ?? 180 }} min</td>
        <td>{{ $s->max_marks }}</td>
        <td>{{ $s->venue ?? 'Main Hall' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div style="background:#fffbeb;border:1px solid #f59e0b;padding:8px;font-size:11px;margin:10px 0;">
    <strong>Instructions:</strong> Carry this hall ticket to all examination sessions. Arrive 30 minutes before exam. No electronic devices allowed.
  </div>
  <div class="footer">
    <div class="sign-area"><div class="sign-line">Student Signature</div></div>
    <div class="sign-area"><div class="sign-line">Class Teacher</div></div>
    <div class="sign-area"><div class="sign-line">Principal</div></div>
  </div>
</div>
</body>
</html>
