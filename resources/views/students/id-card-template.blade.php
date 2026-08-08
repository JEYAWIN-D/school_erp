@extends('layouts.app')
@section('title', 'ID Card Template Designer')
@section('content')
<div class="space-y-6" x-data="{
  headerColor: '{{ $school?->id_card_header_color ?? '#1e3a5f' }}',
  textColor: '{{ $school?->id_card_text_color ?? '#1e3a5f' }}',
  bgColor: '{{ $school?->id_card_bg_color ?? '#ffffff' }}',
  headerText: '{{ addslashes($school?->id_card_header_text ?? '') }}',
  footerText: '{{ addslashes($school?->id_card_footer_text ?? '') }}',
  showBlood: {{ $school?->id_card_show_blood_group ? 'true' : 'true' }},
  showDob: {{ $school?->id_card_show_dob ? 'true' : 'true' }},
  showQr: {{ $school?->id_card_show_qr ? 'true' : 'true' }},
  showMobile: {{ $school?->id_card_show_mobile ? 'true' : 'true' }},
  showAddress: {{ $school?->id_card_show_address ? 'true' : 'false' }},
  showPhoto: {{ $school?->id_card_show_photo !== false ? 'true' : 'false' }},
}">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">ID Card Template Designer</h1>
      <p class="page-subtitle">Configure logo, colours, and visible fields for student ID cards</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('students.id-cards') }}" class="btn btn-secondary btn-sm">← ID Cards</a>
    </div>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Designer Form --}}
    <form method="POST" action="{{ route('students.id-card-template.save') }}" class="space-y-5">
      @csrf

      <div class="card space-y-4">
        <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Colours & Style</h2>
        <div class="grid grid-cols-3 gap-4">
          <div>
            <label class="label text-xs">Header Background</label>
            <div class="flex items-center gap-2">
              <input type="color" name="id_card_header_color" x-model="headerColor"
                     class="h-9 w-14 rounded border border-slate-200 cursor-pointer">
              <span class="text-xs text-slate-400 font-mono" x-text="headerColor"></span>
            </div>
          </div>
          <div>
            <label class="label text-xs">Name / Label Color</label>
            <div class="flex items-center gap-2">
              <input type="color" name="id_card_text_color" x-model="textColor"
                     class="h-9 w-14 rounded border border-slate-200 cursor-pointer">
              <span class="text-xs text-slate-400 font-mono" x-text="textColor"></span>
            </div>
          </div>
          <div>
            <label class="label text-xs">Card Background</label>
            <div class="flex items-center gap-2">
              <input type="color" name="id_card_bg_color" x-model="bgColor"
                     class="h-9 w-14 rounded border border-slate-200 cursor-pointer">
              <span class="text-xs text-slate-400 font-mono" x-text="bgColor"></span>
            </div>
          </div>
        </div>
        <div>
          <label class="label text-xs">Custom Header Text <span class="text-slate-400">(leave blank to use school name)</span></label>
          <input type="text" name="id_card_header_text" x-model="headerText" class="input text-sm"
                 placeholder="{{ $school?->school_name ?? 'School Name' }}">
        </div>
        <div>
          <label class="label text-xs">Footer Text</label>
          <input type="text" name="id_card_footer_text" x-model="footerText" class="input text-sm"
                 placeholder="Address or emergency contact">
        </div>
      </div>

      <div class="card space-y-3">
        <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Visible Fields</h2>
        <p class="text-xs text-slate-500">Choose which information to display on the ID card.</p>
        <div class="grid grid-cols-2 gap-3">
          @php
            $toggles = [
              ['id_card_show_photo',       'showPhoto',   'Student Photo'],
              ['id_card_show_blood_group', 'showBlood',   'Blood Group'],
              ['id_card_show_dob',         'showDob',     'Date of Birth'],
              ['id_card_show_mobile',      'showMobile',  'Parent Mobile'],
              ['id_card_show_qr',          'showQr',      'QR Code'],
              ['id_card_show_address',     'showAddress', 'Residential Address'],
            ];
          @endphp
          @foreach($toggles as [$name, $model, $label])
          <label class="flex items-center gap-3 cursor-pointer p-2 rounded-lg hover:bg-slate-50 border border-slate-100">
            <input type="hidden" name="{{ $name }}" value="0">
            <input type="checkbox" name="{{ $name }}" value="1" x-model="{{ $model }}"
                   class="w-4 h-4 rounded border-slate-300 text-indigo-600">
            <span class="text-sm text-slate-700">{{ $label }}</span>
          </label>
          @endforeach
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-full">Save Template</button>
    </form>

    {{-- Live Preview --}}
    <div>
      <h2 class="font-semibold text-slate-700 mb-3">Live Preview</h2>
      <p class="text-xs text-slate-400 mb-4">Approximate preview — actual PDF rendering may vary slightly.</p>

      {{-- ID card preview --}}
      <div class="inline-block rounded-xl overflow-hidden border-2 shadow-lg"
           :style="`border-color: ${headerColor}; background: ${bgColor}; width: 260px;`">
        {{-- Header --}}
        <div class="text-center py-2 px-3" :style="`background: ${headerColor};`">
          <p class="text-white font-bold text-xs tracking-wide" x-text="headerText || '{{ $school?->school_name ?? 'School Name' }}'"></p>
          <p class="text-white text-xs opacity-80 mt-0.5">STUDENT IDENTITY CARD</p>
        </div>
        {{-- Body --}}
        <div class="p-3" :style="`background: ${bgColor};`">
          <div class="flex gap-3">
            {{-- Photo --}}
            <div x-show="showPhoto" class="w-16 h-20 rounded border border-slate-200 bg-slate-100 flex items-center justify-center text-slate-400 text-xs shrink-0">Photo</div>
            {{-- Info --}}
            <div class="flex-1 min-w-0">
              <p class="font-bold text-xs truncate" :style="`color: ${textColor};`">Student Full Name</p>
              <p class="text-xs text-slate-500 mt-1">Adm: SC-2025-001</p>
              <p class="text-xs text-slate-500">Class: X — A</p>
              <p x-show="showDob" class="text-xs text-slate-500">DOB: 12/06/2010</p>
              <p x-show="showBlood" class="text-xs text-slate-500">Blood: B+</p>
              <p x-show="showMobile" class="text-xs text-slate-500">Ph: 9876543210</p>
              <p x-show="showAddress" class="text-xs text-slate-500 truncate">123, Main Street</p>
            </div>
            {{-- QR --}}
            <div x-show="showQr" class="w-8 h-8 border border-slate-200 bg-slate-100 flex items-center justify-center shrink-0 mt-auto text-slate-300 text-xs">QR</div>
          </div>
        </div>
        {{-- Footer --}}
        <div class="py-1 px-3 text-center" :style="`background: ${headerColor};`">
          <p class="text-white text-xs opacity-90" x-text="footerText || '{{ $school?->phone ?? 'School Phone' }}'"></p>
          <p class="text-white text-xs opacity-60">Valid: {{ \App\Models\AcademicYear::current()?->name ?? '2025-2026' }}</p>
        </div>
      </div>

      <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
        <strong>Note:</strong> Colors and layout are applied to all generated ID cards.
        Go to <a href="{{ route('students.id-cards') }}" class="underline">ID Cards</a> to download with the new template.
      </div>
    </div>
  </div>
</div>
@endsection
