<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 0; }
  .page { padding: 30px; }
  .header { text-align: center; border-bottom: 3px double #1e40af; padding-bottom: 12px; margin-bottom: 18px; }
  .school-name { font-size: 20px; font-weight: bold; color: #1e3a8a; }
  .doc-title { font-size: 15px; font-weight: bold; color: #1e40af; margin-top: 4px; letter-spacing: 1px; }
  .info-grid { display: table; width: 100%; margin-bottom: 18px; }
  .info-row { display: table-row; }
  .info-cell { display: table-cell; padding: 4px 8px; width: 50%; }
  .info-label { font-size: 10px; color: #64748b; }
  .info-value { font-size: 12px; font-weight: bold; }
  .section-title { background: #1e3a8a; color: white; padding: 6px 10px; font-weight: bold; font-size: 12px; margin: 14px 0 6px; }
  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  th { background: #dbeafe; color: #1e3a8a; padding: 7px 8px; text-align: left; border: 1px solid #93c5fd; }
  td { padding: 6px 8px; border: 1px solid #cbd5e1; }
  tr:nth-child(even) td { background: #f8fafc; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: bold; }
  .badge-blue { background: #dbeafe; color: #1e40af; }
  .instructions { margin-top: 20px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; background: #f8fafc; }
  .instructions ul { margin: 6px 0 0 16px; padding: 0; }
  .instructions li { margin-bottom: 4px; color: #475569; font-size: 10px; }
  .signature-row { margin-top: 30px; display: table; width: 100%; }
  .sig-cell { display: table-cell; text-align: center; width: 50%; }
  .sig-line { border-top: 1px solid #94a3b8; margin: 0 20px; padding-top: 6px; font-size: 10px; color: #64748b; }
  .footer { text-align: center; font-size: 9px; color: #94a3b8; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 8px; }
</style>
</head>
<body>
<div class="page">

  <div class="header">
    <div class="school-name">{{ $school?->name ?? 'School Name' }}</div>
    @if($school?->address)<div style="font-size:10px;color:#64748b;margin-top:2px;">{{ $school->address }}</div>@endif
    <div class="doc-title">HALL TICKET / ADMIT CARD</div>
  </div>

  <div class="info-grid">
    <div class="info-row">
      <div class="info-cell">
        <div class="info-label">Student Name</div>
        <div class="info-value">{{ $student->full_name }}</div>
      </div>
      <div class="info-cell">
        <div class="info-label">Admission No.</div>
        <div class="info-value">{{ $student->admission_no }}</div>
      </div>
    </div>
    <div class="info-row">
      <div class="info-cell">
        <div class="info-label">Class &amp; Section</div>
        <div class="info-value">{{ $enrollment?->class_name ?? '—' }}</div>
      </div>
      <div class="info-cell">
        <div class="info-label">Roll No.</div>
        <div class="info-value">{{ $student->roll_number ?? '—' }}</div>
      </div>
    </div>
  </div>

  @forelse($upcoming as $examName => $schedules)
  <div class="section-title">{{ $examName }}</div>
  <table>
    <thead>
      <tr>
        <th>Subject</th>
        <th>Date</th>
        <th>Time</th>
        <th>Max Marks</th>
        <th>Pass Marks</th>
      </tr>
    </thead>
    <tbody>
      @foreach($schedules as $s)
      <tr>
        <td>{{ $s->subject_name }}</td>
        <td>{{ \Carbon\Carbon::parse($s->exam_date)->format('d M Y, l') }}</td>
        <td>
          {{ $s->start_time ? \Carbon\Carbon::parse($s->start_time)->format('h:i A') : '—' }}
          @if($s->end_time) – {{ \Carbon\Carbon::parse($s->end_time)->format('h:i A') }} @endif
        </td>
        <td style="text-align:center;">{{ $s->total_marks ?? '—' }}</td>
        <td style="text-align:center;">{{ $s->passing_marks ?? '—' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @empty
  <div style="text-align:center;padding:30px;color:#94a3b8;">No upcoming exam schedules found.</div>
  @endforelse

  <div class="instructions">
    <strong style="font-size:11px;color:#1e3a8a;">Instructions to Candidates:</strong>
    <ul>
      <li>Bring this Hall Ticket and a valid ID to the examination hall.</li>
      <li>Report 15 minutes before the exam start time.</li>
      <li>Mobile phones and electronic devices are strictly prohibited.</li>
      <li>Students found using unfair means will be disqualified.</li>
    </ul>
  </div>

  <div class="signature-row">
    <div class="sig-cell"><div class="sig-line">Student Signature</div></div>
    <div class="sig-cell"><div class="sig-line">Principal / Controller of Examinations</div></div>
  </div>

  <div class="footer">Generated on {{ now()->format('d M Y') }} | {{ $school?->name ?? '' }}</div>
</div>
</body>
</html>
