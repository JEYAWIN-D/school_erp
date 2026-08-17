<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff ID Cards</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color: #1e293b; background: #fff; }

  .page { padding: 8mm 6mm; }
  .cards-grid { width: 100%; margin-bottom: 5mm; }

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
    position: relative;
  }
  .card-header table { width: 100%; border-collapse: collapse; }
  .school-name { font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px; color: #ffffff; }
  .card-type { font-size: 6px; font-weight: bold; background: rgba(255,255,255,0.25); padding: 1px 4px; border-radius: 3px; color: #ffffff; text-align: right; text-transform: uppercase; }

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
  .photo-initials { font-size: 11px; font-weight: bold; color: #64748b; line-height: 22mm; }

  .info-col { padding-left: 2.5mm; vertical-align: top; }
  .emp-name { font-size: 8.5px; font-weight: bold; color: #0f172a; text-transform: uppercase; margin-bottom: 1px; }
  .emp-desig { font-size: 7px; font-weight: bold; margin-bottom: 2px; }
  
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

  /* Back card specific */
  .back-box {
    background: #f8fafc;
    border: 0.5px solid #e2e8f0;
    border-radius: 3px;
    padding: 1.5mm 2mm;
    margin-bottom: 1.5mm;
    font-size: 6px;
  }
  .back-title { font-size: 6px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; margin-bottom: 1px; }
  .back-desc { font-size: 5.8px; color: #334155; line-height: 1.2; }
  .helpline { font-size: 6px; font-weight: bold; color: #0f172a; margin-top: 1mm; }
</style>
</head>
<body>
<div class="page">
  <table class="card-table">
    @foreach($employees->chunk(2) as $row)
    <tr>
      @foreach($row as $emp)
      @php
        $theme = $emp->theme_meta ?? [
          'category_key' => 'teacher',
          'theme_label'  => 'TEACHER',
          'header_color' => '#1d4ed8',
          'accent_color' => '#2563eb',
        ];
        $headerBg = $theme['header_color'] ?? '#1e1b4b';
        $accentColor = $theme['accent_color'] ?? '#4338ca';
      @endphp
      <td class="id-card-cell">
        {{-- FRONT SIDE CARD --}}
        <div class="id-card">
          <div class="card-header" style="background: {{ $headerBg }};">
            <table>
              <tr>
                <td>
                  <div class="school-name">{{ $school->school_name ?? config('app.name', 'DEMO SCHOOL') }}</div>
                </td>
                <td style="text-align: right;">
                  <span class="card-type">{{ $theme['theme_label'] ?? 'STAFF' }} ID</span>
                </td>
              </tr>
            </table>
          </div>

          <div class="card-body">
            <table class="body-table">
              <tr>
                <td style="width: 18mm; vertical-align: top;">
                  <div class="photo-box">
                    @if($emp->photo && file_exists(public_path('storage/' . $emp->photo)))
                      <img src="{{ public_path('storage/' . $emp->photo) }}" alt="">
                    @else
                      <div class="photo-initials" style="color: {{ $accentColor }};">
                        {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) }}
                      </div>
                    @endif
                  </div>
                </td>
                <td class="info-col">
                  <div class="emp-name">{{ $emp->full_name }}</div>
                  <div class="emp-desig" style="color: {{ $accentColor }};">{{ $emp->designation ?? $emp->category_label }}</div>

                  <div class="details-box">
                    <table class="details-table">
                      <tr>
                        <td class="lbl">ID No:</td>
                        <td class="val">{{ $emp->employee_code ?? 'EMP-' . $emp->id }}</td>
                      </tr>
                      <tr>
                        <td class="lbl">DOB:</td>
                        <td class="val">{{ $emp->dob ? $emp->dob->format('d/m/Y') : '15/08/1990' }}</td>
                      </tr>
                      <tr>
                        <td class="lbl">Phone:</td>
                        <td class="val">+91 {{ $emp->mobile ?? '9876543210' }}</td>
                      </tr>
                      @if($emp->blood_group)
                      <tr>
                        <td class="lbl">Blood:</td>
                        <td class="val" style="color: #dc2626;">{{ $emp->blood_group }}</td>
                      </tr>
                      @endif
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
                <td class="side-tag">FRONT SIDE</td>
              </tr>
            </table>
          </div>
        </div>
      </td>
      @endforeach
      @if($row->count() == 1)
        <td class="id-card-cell"></td>
      @endif
    </tr>

    {{-- Corresponding Back Sides for the chunk --}}
    <tr>
      @foreach($row as $emp)
      @php
        $theme = $emp->theme_meta ?? [
          'category_key' => 'teacher',
          'theme_label'  => 'TEACHER',
          'header_color' => '#1d4ed8',
          'accent_color' => '#2563eb',
        ];
        $headerBg = $theme['header_color'] ?? '#1e1b4b';
      @endphp
      <td class="id-card-cell">
        {{-- BACK SIDE CARD --}}
        <div class="id-card">
          <div class="card-header" style="background: {{ $headerBg }};">
            <table>
              <tr>
                <td>
                  <div class="school-name">{{ $school->school_name ?? config('app.name', 'DEMO SCHOOL') }}</div>
                </td>
                <td style="text-align: right;">
                  <span class="card-type">AUTHORIZATION</span>
                </td>
              </tr>
            </table>
          </div>

          <div class="card-body">
            <div class="back-box">
              <div class="back-title">INSTITUTION ADDRESS</div>
              <div class="back-desc">
                {{ $school->school_name ?? 'Demo School Main Campus' }}, {{ $school->address ?? '123, Main Street, Chennai - 600001' }}
              </div>
            </div>

            <div class="back-box">
              <div class="back-title">RESIDENTIAL ADDRESS</div>
              <div class="back-desc">
                {{ $emp->residential_address ?? $emp->address ?? 'Staff Quarters Road, Campus Block B - 600001' }}
              </div>
            </div>

            <div class="helpline">
              Helpline: +91 {{ $school->phone ?? $emp->emergency_contact_mobile ?? '9876543210' }}
            </div>
          </div>

          <div class="card-footer">
            <table>
              <tr>
                <td class="website">{{ $school->website ?? 'www.dasaeduerp.com' }}</td>
                <td class="side-tag">BACK SIDE</td>
              </tr>
            </table>
          </div>
        </div>
      </td>
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
