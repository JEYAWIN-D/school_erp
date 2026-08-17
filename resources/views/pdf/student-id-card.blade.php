<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student ID Cards</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #1e293b; background: #fff; }
  .page { padding: 8mm 6mm; }

  .card-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 4mm 4mm;
  }

  .id-card-cell {
    width: 50%;
    vertical-align: top;
  }

  .id-card {
    width: 85mm;
    height: 54mm;
    border-radius: 5px;
    overflow: hidden;
    background: #fff;
    border: 1px solid #cbd5e1;
    page-break-inside: avoid;
    position: relative;
  }

  .card-header {
    height: 12mm;
    color: #fff;
    padding: 2mm 3mm;
  }
  .card-header table { width: 100%; border-collapse: collapse; }
  .school-name { font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; color: #ffffff; }
  .wing-tag { font-size: 6px; font-weight: bold; background: rgba(255,255,255,0.25); padding: 1px 4px; border-radius: 3px; color: #ffffff; text-align: right; text-transform: uppercase; }

  .card-body { padding: 2mm 3mm; height: 35mm; }
  .body-table { width: 100%; border-collapse: collapse; }

  .photo-box {
    width: 18mm;
    height: 22mm;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    background: #f8fafc;
    text-align: center;
    vertical-align: middle;
    overflow: hidden;
  }
  .photo-box img { width: 18mm; height: 22mm; object-fit: cover; }
  .photo-initials { font-size: 11px; font-weight: bold; line-height: 22mm; }

  .info-col { padding-left: 2.5mm; vertical-align: top; }
  .std-name { font-size: 8.5px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 1px; }
  .std-class { font-size: 7px; font-weight: bold; margin-bottom: 2px; }

  .details-box {
    background: #f8fafc;
    border: 0.5px solid #e2e8f0;
    border-radius: 3px;
    padding: 1.5px 3px;
    font-size: 6.5px;
    line-height: 1.25;
  }
  .details-table { width: 100%; border-collapse: collapse; font-size: 6.5px; }
  .lbl { color: #64748b; font-weight: bold; width: 11mm; }
  .val { color: #0f172a; font-weight: bold; }

  .card-footer {
    height: 7mm;
    background: #f8fafc;
    border-top: 0.5px solid #e2e8f0;
    padding: 1mm 3mm;
    font-size: 6px;
    color: #64748b;
  }
  .card-footer table { width: 100%; border-collapse: collapse; }
  .website { font-size: 6px; font-weight: bold; color: #64748b; }
  .side-tag { font-size: 5.5px; font-weight: bold; color: #94a3b8; text-align: right; text-transform: uppercase; }
</style>
</head>
<body>
<div class="page">
  <table class="card-table">
    @foreach($enrollments->chunk(2) as $row)
    <tr>
      @foreach($row as $enrollment)
      @php
        $student = $enrollment->student;
        $cls = $enrollment->class;
        $sec = $enrollment->section;
        $wing = $enrollment->wing_meta ?? [
          'key'           => 'primary',
          'tag'           => 'PRIMARY WING',
          'primary_color' => '#059669',
          'header_color'  => '#065f46',
        ];
        $headerBg = $wing['primary_color'] ?? '#1e3a5f';
      @endphp
      @if($student)
      <td class="id-card-cell">
        <div class="id-card">
          <div class="card-header" style="background: {{ $headerBg }};">
            <table>
              <tr>
                <td>
                  <div class="school-name">{{ $school->school_name ?? config('app.name', 'DEMO SCHOOL') }}</div>
                </td>
                <td style="text-align: right;">
                  <span class="wing-tag">{{ $wing['tag'] ?? 'STUDENT' }}</span>
                </td>
              </tr>
            </table>
          </div>

          <div class="card-body">
            <table class="body-table">
              <tr>
                <td style="width: 18mm; vertical-align: top;">
                  <div class="photo-box">
                    @if($student->photo && file_exists(public_path('storage/' . $student->photo)))
                      <img src="{{ public_path('storage/' . $student->photo) }}" alt="">
                    @else
                      <div class="photo-initials" style="color: {{ $headerBg }};">
                        {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                      </div>
                    @endif
                  </div>
                </td>
                <td class="info-col">
                  <div class="std-name">{{ $student->full_name }}</div>
                  <div class="std-class" style="color: {{ $headerBg }};">
                    Class {{ $cls?->name ?? '—' }} {{ $sec?->name ? '(' . $sec->name . ')' : '' }}
                  </div>

                  <div class="details-box">
                    <table class="details-table">
                      <tr>
                        <td class="lbl">Adm No:</td>
                        <td class="val">{{ $student->admission_number }}</td>
                      </tr>
                      <tr>
                        <td class="lbl">DOB:</td>
                        <td class="val">{{ $student->dob ? $student->dob->format('d/m/Y') : '—' }}</td>
                      </tr>
                      @if($student->blood_group)
                      <tr>
                        <td class="lbl">Blood:</td>
                        <td class="val" style="color: #dc2626;">{{ $student->blood_group }}</td>
                      </tr>
                      @endif
                      <tr>
                        <td class="lbl">Mobile:</td>
                        <td class="val">+91 {{ $student->father_mobile ?? $student->mobile ?? '9876543210' }}</td>
                      </tr>
                    </table>
                  </div>
                </td>
              </tr>
            </table>
          </div>

          <div class="card-footer">
            <table>
              <tr>
                <td class="website">{{ $school->website ?? 'www.dasaeduerp.com' }}</td>
                <td class="side-tag">VALID: {{ $currentYear?->name ?? '2025-2026' }}</td>
              </tr>
            </table>
          </div>
        </div>
      </td>
      @endif
      @endforeach
      @if($row->count() == 1)
        <td class="id-card-cell"></td>
      @endif
    </tr>
    @endforeach
  </table>
</div>
</body>
</html>
