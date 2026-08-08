@php
  $hc       = $template?->id_card_header_color ?? '#1e3a5f';
  $tc       = $template?->id_card_text_color ?? '#1e3a5f';
  $bg       = $template?->id_card_bg_color ?? '#ffffff';
  $hdrText  = $template?->id_card_header_text ?: ($school?->school_name ?? 'DASA EduERP');
  $ftrText  = $template?->id_card_footer_text ?: (($school?->school_name ?? '') . ($school?->phone ? ' | ' . $school->phone : ''));
  $showPhoto = $template?->id_card_show_photo ?? true;
  $showBlood = $template?->id_card_show_blood_group ?? true;
  $showDob   = $template?->id_card_show_dob ?? true;
  $showQr    = $template?->id_card_show_qr ?? true;
  $showMob   = $template?->id_card_show_mobile ?? true;
  $showAddr  = $template?->id_card_show_address ?? false;
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; margin: 0; padding: 16px; background: #fff; }
.id-card { width: 85mm; border: 2px solid {{ $hc }}; border-radius: 8px; overflow: hidden; page-break-inside: avoid; margin: 6px; display: inline-block; vertical-align: top; background: {{ $bg }}; }
.card-header { background: {{ $hc }}; color: white; text-align: center; padding: 6px 5px; }
.school-name { font-size: 9px; font-weight: bold; letter-spacing: 0.3px; }
.card-type { font-size: 7px; margin-top: 1px; letter-spacing: 1px; opacity: 0.9; }
.card-body { padding: 7px 8px; background: {{ $bg }}; }
.photo-row { display: flex; gap: 7px; align-items: flex-start; }
.photo-box { width: 28mm; height: 33mm; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; background: #f5f5f5; flex-shrink: 0; font-size: 7px; color: #999; text-align: center; overflow: hidden; }
.photo-box img { width: 100%; height: 100%; object-fit: cover; }
.info-area { flex: 1; min-width: 0; }
.student-name { font-size: 11px; font-weight: bold; color: {{ $tc }}; word-break: break-word; }
.info-row { font-size: 8px; margin-top: 3px; color: #444; }
.lbl { color: #888; }
.qr-row { display: flex; justify-content: flex-end; margin-top: 5px; }
.qr-row img { width: 28px; height: 28px; }
.card-footer { background: {{ $hc }}; color: white; text-align: center; padding: 4px 5px; font-size: 7px; }
.validity { font-size: 6px; margin-top: 1px; opacity: 0.8; }
</style>
</head>
<body>
@foreach($students as $student)
@php $enrollment = $student->currentEnrollment; @endphp
<div class="id-card">
  <div class="card-header">
    <div class="school-name">{{ $hdrText }}</div>
    <div class="card-type">STUDENT IDENTITY CARD</div>
  </div>
  <div class="card-body">
    <div class="photo-row">
      @if($showPhoto)
      <div class="photo-box">
        @if($student->photo)
          <img src="{{ public_path('storage/' . $student->photo) }}" alt="Photo">
        @else
          Photo
        @endif
      </div>
      @endif
      <div class="info-area">
        <div class="student-name">{{ $student->full_name }}</div>
        <div class="info-row"><span class="lbl">Adm No: </span>{{ $student->admission_number }}</div>
        <div class="info-row"><span class="lbl">Class: </span>{{ $enrollment?->class?->name }} {{ $enrollment?->section?->name }}</div>
        @if($showDob)
        <div class="info-row"><span class="lbl">DOB: </span>{{ $student->dob?->format('d/m/Y') }}</div>
        @endif
        @if($showBlood)
        <div class="info-row"><span class="lbl">Blood: </span>{{ $student->blood_group ?? '—' }}</div>
        @endif
        @if($showMob)
        <div class="info-row"><span class="lbl">Mobile: </span>{{ $student->father_mobile ?? $student->mobile ?? '—' }}</div>
        @endif
        @if($showAddr && $student->residential_address)
        <div class="info-row"><span class="lbl">Addr: </span>{{ \Illuminate\Support\Str::limit($student->residential_address, 40) }}</div>
        @endif
      </div>
    </div>
    @if($showQr && !empty($qrCodes[$student->id]))
    <div class="qr-row">
      <img src="data:image/png;base64,{{ $qrCodes[$student->id] }}" alt="QR">
    </div>
    @endif
  </div>
  <div class="card-footer">
    <div>{{ $ftrText }}</div>
    <div class="validity">Valid: {{ $currentYear?->name ?? '' }}</div>
  </div>
</div>
@endforeach
</body>
</html>
