<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Curriculum Syllabus — {{ $class->display_name ?? $class->name }} — {{ config('app.name') }}</title>
  <style>
    @page { size: A4; margin: 15mm; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; background: #fff; margin: 0; padding: 20px; font-size: 13px; line-height: 1.5; }
    .header { text-align: center; border-bottom: 2px solid #6366f1; padding-bottom: 15px; margin-bottom: 20px; }
    .header h1 { margin: 0 0 4px; font-size: 22px; color: #1e1b4b; text-transform: uppercase; letter-spacing: 0.5px; }
    .header p { margin: 2px 0; color: #64748b; font-size: 12px; }
    .meta-badge { display: inline-block; background: #e0e7ff; color: #4338ca; padding: 4px 12px; border-radius: 9999px; font-weight: 700; font-size: 12px; margin-top: 6px; }
    .term-section { margin-bottom: 30px; page-break-inside: avoid; }
    .term-title { font-size: 16px; font-weight: 800; color: #4338ca; background: #f8fafc; border-left: 5px solid #6366f1; padding: 8px 12px; margin-bottom: 12px; border-radius: 4px; }
    .subject-title { font-size: 14px; font-weight: 700; color: #0f172a; margin: 16px 0 8px; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; display: flex; align-items: center; justify-content: space-between; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 12px; }
    th { background: #f1f5f9; color: #475569; text-align: left; padding: 8px 10px; font-weight: 600; border: 1px solid #e2e8f0; text-transform: uppercase; font-size: 11px; }
    td { padding: 8px 10px; border: 1px solid #e2e8f0; vertical-align: top; }
    tr:nth-child(even) { background: #f8fafc; }
    .status-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
    .status-completed { background: #dcfce7; color: #15803d; }
    .status-in_progress { background: #fef3c7; color: #b45309; }
    .status-pending { background: #f1f5f9; color: #64748b; }
    .footer { margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 12px; font-size: 11px; color: #94a3b8; text-align: center; }
    @media print {
      body { padding: 0; }
      .no-print { display: none; }
    }
  </style>
</head>
<body>

  <div class="no-print" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; background: #e0e7ff; padding: 12px 16px; border-radius: 8px;">
    <span style="font-weight: 600; color: #3730a3;">Official Curriculum Print View</span>
    <div>
      <button onclick="window.print()" style="background: #4f46e5; color: white; border: none; padding: 6px 16px; border-radius: 6px; font-weight: 600; cursor: pointer;">🖨️ Print / Save as PDF</button>
      <button onclick="window.close()" style="background: #fff; color: #475569; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; margin-left: 8px;">Close</button>
    </div>
  </div>

  <div class="header">
    <h1>{{ $school->name ?? config('app.name', 'DASA EduERP') }}</h1>
    <p>{{ $school->address ?? 'Academic Year ' . ($currentYear->name ?? '2025-2026') }}</p>
    <div class="meta-badge">Curriculum Plan: {{ $class->display_name ?? $class->name }}</div>
  </div>

  @forelse($syllabus as $termName => $subjects)
    <div class="term-section">
      <div class="term-title">📌 {{ $termName }}</div>
      @foreach($subjects as $subjectName => $chapters)
        <div class="subject-title">
          <span>📚 {{ $subjectName }}</span>
          <span style="font-size: 11px; font-weight: normal; color: #64748b;">{{ count($chapters) }} Chapters</span>
        </div>
        <table>
          <thead>
            <tr>
              <th style="width: 8%;">Ch #</th>
              <th style="width: 32%;">Chapter Title</th>
              <th style="width: 40%;">Key Topics & Objectives</th>
              <th style="width: 10%;">Planned</th>
              <th style="width: 10%;">Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($chapters as $ch)
              <tr>
                <td style="font-weight: 700; text-align: center;">{{ $ch->chapter_number }}</td>
                <td>
                  <strong>{{ $ch->chapter_title }}</strong>
                  @if($ch->description)
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">{{ $ch->description }}</div>
                  @endif
                </td>
                <td style="color: #475569;">{{ $ch->topics ?: '—' }}</td>
                <td style="font-size: 11px; color: #64748b;">{{ $ch->planned_date ? \Carbon\Carbon::parse($ch->planned_date)->format('d M Y') : '—' }}</td>
                <td>
                  <span class="status-badge status-{{ $ch->status }}">
                    {{ str_replace('_', ' ', $ch->status) }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endforeach
    </div>
  @empty
    <div style="text-align: center; padding: 40px; color: #94a3b8;">
      No syllabus entries found for this standard.
    </div>
  @endforelse

  <div class="footer">
    Generated from {{ config('app.name') }} on {{ now()->format('d M Y, h:i A') }} • Page 1 of 1
  </div>

</body>
</html>
