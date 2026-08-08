<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Leaving Certificate - {{ $student->full_name }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: 'DejaVu Sans', 'Helvetica', Arial, sans-serif;
    font-size: 11px;
    color: #222;
    background: #fff;
    margin: 0;
    padding: 6mm 8mm;
  }

  .page { width: 100%; border: 3px solid #222; padding: 3px; }
  .page-inner { border: 1.5px solid #222; padding: 12px 16px 10px 16px; }

  .hdr { text-align: center; padding-bottom: 10px; border-bottom: 2px solid #222; margin-bottom: 8px; }
  .hdr-trust { font-size: 9px; color: #555; letter-spacing: 0.5px; margin-bottom: 3px; }
  .hdr-school { font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; color: #111; margin: 4px 0; }
  .hdr-board { font-size: 11px; font-weight: 700; color: #333; margin: 2px 0; }
  .hdr-addr { font-size: 9px; color: #555; margin-top: 3px; }

  .meta { width: 100%; margin: 6px 0; font-size: 10.5px; }
  .meta td { padding: 2px 0; }
  .meta .lbl { color: #666; font-weight: 600; }
  .meta .val { font-weight: 700; color: #111; border-bottom: 1.5px solid #999; padding: 0 8px; }

  .title-bar {
    text-align: center; margin: 10px 0; padding: 6px 0;
    background: #1a1a2e; color: #fff;
    font-size: 16px; font-weight: 900; letter-spacing: 4px; text-transform: uppercase;
  }

  .dt { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
  .dt td { border: 1px solid #999; padding: 6px 10px; vertical-align: middle; font-size: 11px; line-height: 1.4; }
  .dt .n { width: 26px; text-align: center; font-weight: 700; color: #888; background: #f5f5f5; font-size: 9.5px; }
  .dt .lb { width: 40%; font-weight: 700; color: #222; background: #fafafa; }
  .dt .vl { color: #111; }

  .cert-text { font-size: 11px; font-style: italic; color: #333; margin: 10px 0 6px 0; }
  .dp { font-size: 11px; font-weight: 700; color: #333; line-height: 1.8; margin-bottom: 6px; }

  .seal-wrap { text-align: center; }
  .seal {
    width: 88px; height: 88px;
    border: 3px solid #8b0000; border-radius: 50%;
    margin: 0 auto; position: relative;
  }
  .seal-mid {
    width: 70px; height: 70px;
    border: 1.5px solid #8b0000; border-radius: 50%;
    position: absolute; top: 7px; left: 7px; text-align: center;
  }
  .seal-mid-inner {
    position: absolute; top: 50%; left: 50%;
    transform: translate(-50%, -50%); width: 60px;
  }
  .seal-star { font-size: 12px; color: #8b0000; }
  .seal-txt { font-size: 6px; font-weight: 900; color: #8b0000; text-transform: uppercase; line-height: 1.2; margin: 1px 0; word-wrap: break-word; }
  .seal-lbl { font-size: 5.5px; letter-spacing: 1px; color: #8b0000; font-weight: 700; text-transform: uppercase; }

  .ft { width: 100%; margin-top: 10px; }
  .ft td { text-align: center; vertical-align: bottom; font-size: 10px; font-weight: 700; color: #222; padding: 0 6px; }
  .ft .sl { border-top: 1.5px solid #222; padding-top: 4px; margin-top: 3px; font-size: 10px; }
  .ft .sig-img { max-height: 35px; margin-bottom: 3px; }
</style>
</head>
<body>

<div class="page">
<div class="page-inner">

  <div class="hdr">
    <div class="hdr-trust">Estd. {{ $school?->established_year ?? '1999' }} &bull; {{ $school?->trust_name ?? 'Education Foundation' }}</div>
    <div class="hdr-school">{{ $school?->school_name ?? 'School Name' }}</div>
    <div class="hdr-board">
      @if($school?->board)
        Affiliated to {{ strtoupper($school->board) }} Board
      @endif
      @if($school?->affiliation_number)
        (Affn. No: {{ $school->affiliation_number }})
      @endif
    </div>
    <div class="hdr-addr">
      @if($school?->udise_code)
        U-DISE: {{ $school->udise_code }} |
      @endif
      {{ $school?->address ?? '' }}
      @if($school?->city)
        , {{ $school->city }}
      @endif
      @if($school?->pincode)
        - {{ $school->pincode }}
      @endif
      @if($school?->phone)
        | Ph: {{ $school->phone }}
      @endif
    </div>
  </div>

  <table class="meta">
    <tr>
      <td style="text-align:left;width:50%;"><span class="lbl">TC No:</span> <span class="val">{{ $tcNumber }}</span></td>
      <td style="text-align:right;width:50%;"><span class="lbl">Admn No:</span> <span class="val">{{ $student->admission_no ?? '—' }}</span></td>
    </tr>
    <tr>
      <td><span class="lbl">Date:</span> <span class="val">{{ now()->format('d/m/Y') }}</span></td>
      <td style="text-align:right;"><span class="lbl">Aadhaar:</span> <span class="val">{{ $student->aadhaar_no ?? '—' }}</span></td>
    </tr>
  </table>

  <div class="title-bar">Leaving Certificate</div>

  <table class="dt">
    <tr><td class="n">1</td><td class="lb">Full Name</td><td class="vl"><strong>{{ strtoupper($student->full_name) }}</strong></td></tr>
    <tr><td class="n">2</td><td class="lb">Father's Name</td><td class="vl">{{ $student->father_name ?? '—' }}</td></tr>
    <tr><td class="n">3</td><td class="lb">Mother's Name</td><td class="vl">{{ $student->mother_name ?? '—' }}</td></tr>
    <tr>
      <td class="n">4</td>
      <td class="lb">Religion / Caste</td>
      <td class="vl">
        {{ $student->religion ?? '—' }}
        @if($student->caste)
          / {{ $student->caste }}
        @endif
        @if($student->category)
          ({{ strtoupper($student->category) }})
        @endif
      </td>
    </tr>
    <tr><td class="n">5</td><td class="lb">Nationality</td><td class="vl">{{ $student->nationality ?? 'Indian' }}</td></tr>
    <tr><td class="n">6</td><td class="lb">Place of Birth</td><td class="vl">{{ $student->place_of_birth ?? ($student->city ?? '—') }}</td></tr>
    <tr><td class="n">7</td><td class="lb">Date of Birth (in figures)</td><td class="vl"><strong>{{ $student->dob ? $student->dob->format('d / m / Y') : '—' }}</strong></td></tr>
    <tr><td class="n">8</td><td class="lb">Date of Birth (in words)</td><td class="vl"><em>{{ $tcData['dob_in_words'] ?? '—' }}</em></td></tr>
    <tr><td class="n">9</td><td class="lb">Last School Attended</td><td class="vl">{{ $student->previous_school_name ?? '—' }}</td></tr>
    <tr>
      <td class="n">10</td>
      <td class="lb">Date of Admission &amp; Class</td>
      <td class="vl">
        {{ $student->admission_date ? $student->admission_date->format('d/m/Y') : ($student->created_at ? $student->created_at->format('d/m/Y') : '—') }}
        ({{ $student->admitted_class ?? $enrollment?->class?->name ?? '—' }})
      </td>
    </tr>
    <tr><td class="n">11</td><td class="lb">Progress</td><td class="vl">{{ $tcData['progress'] ?? 'Good' }}</td></tr>
    <tr><td class="n">12</td><td class="lb">Conduct</td><td class="vl">{{ $tcData['conduct'] ?? 'Good' }}</td></tr>
    <tr><td class="n">13</td><td class="lb">Date of Leaving School</td><td class="vl"><strong>{{ $tcData['leaving_date'] ?? now()->format('d/m/Y') }}</strong></td></tr>
    <tr>
      <td class="n">14</td>
      <td class="lb">Studying &amp; since when</td>
      <td class="vl">
        {{ $enrollment?->class?->name ?? '—' }}
        @if($enrollment?->section)
          - Section {{ $enrollment->section->name }}
        @endif
        (Since {{ $student->admission_date ? $student->admission_date->format('M Y') : '—' }})
      </td>
    </tr>
    <tr><td class="n">15</td><td class="lb">Reason for Leaving</td><td class="vl">{{ $tcData['reason'] ?? 'Parent Request' }}</td></tr>
    <tr><td class="n">16</td><td class="lb">Remarks</td><td class="vl"><em>{{ $tcData['remarks'] ?? 'Good conduct. Character satisfactory.' }}</em></td></tr>
  </table>

  <div class="cert-text">"Certified that the above information is correct and in accordance with the school register."</div>

  <div class="dp">
    Date: {{ now()->format('d/m/Y') }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Place: {{ $school?->city ?? 'School' }}
  </div>

  <table class="ft">
    <tr>
      <td style="width:30%;">
        @if(!empty($signatureBase64))
          <img src="{{ $signatureBase64 }}" class="sig-img"><br>
        @else
          <div style="height:40px;"></div>
        @endif
        <div class="sl">Class Teacher</div>
      </td>
      <td style="width:40%;">
        <div class="seal-wrap">
          <div class="seal">
            <div class="seal-mid">
              <div class="seal-mid-inner">
                <div class="seal-star">&#9733;</div>
                <div class="seal-txt">{{ Str::limit($school?->school_name ?? 'School', 24) }}</div>
                <div class="seal-lbl">Official Seal</div>
              </div>
            </div>
          </div>
        </div>
      </td>
      <td style="width:30%;">
        @if(!empty($signatureBase64))
          <img src="{{ $signatureBase64 }}" class="sig-img"><br>
        @else
          <div style="height:40px;"></div>
        @endif
        <div class="sl">{{ $school?->principal_name ?? 'Head Mistress / Principal' }}</div>
      </td>
    </tr>
  </table>

</div>
</div>

</body>
</html>
