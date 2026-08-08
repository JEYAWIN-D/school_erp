@extends('layouts.app')
@section('title', 'Notification Templates')
@section('content')
<div class="space-y-6" x-data="{ activeEvent: '', activeChannel: 'email' }">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Notification Templates</h1>
    <a href="{{ route('settings.index') }}" class="btn-sm btn-secondary">← Settings</a>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  <div class="card bg-slate-50 border border-slate-200 text-sm text-slate-600">
    <p class="font-semibold mb-1">Available Placeholders</p>
    <p class="text-xs font-mono text-slate-500">
      {{'{{'}}parent_name{{'}}'}}, {{'{{'}}student_name{{'}}'}}, {{'{{'}}class{{'}}'}}, {{'{{'}}amount{{'}}'}}, {{'{{'}}balance{{'}}'}}, {{'{{'}}due_date{{'}}'}}, {{'{{'}}date{{'}}'}}, {{'{{'}}exam_name{{'}}'}}, {{'{{'}}percentage{{'}}'}}, {{'{{'}}grade{{'}}'}}, {{'{{'}}result{{'}}'}}, {{'{{'}}action_type{{'}}'}}, {{'{{'}}total_fine{{'}}'}}, {{'{{'}}school_name{{'}}'}}
    </p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- Event selector --}}
    <div class="card space-y-2">
      <h2 class="text-sm font-semibold text-slate-700 mb-3">Event Types</h2>
      @foreach($eventTypes as $key => $label)
      <button type="button" @click="activeEvent='{{ $key }}'"
        :class="activeEvent==='{{ $key }}' ? 'bg-indigo-50 border-indigo-300 text-indigo-700' : 'border-slate-200 text-slate-600'"
        class="w-full text-left px-3 py-2 rounded-lg border text-sm hover:bg-indigo-50 transition-colors">
        {{ $label }}
        @if($templates->has($key.'_email'))
          <span class="float-right badge-green text-xs">✓</span>
        @endif
      </button>
      @endforeach
    </div>

    {{-- Template editor --}}
    <div class="md:col-span-2">
      @foreach($eventTypes as $key => $label)
      <div x-show="activeEvent==='{{ $key }}'" x-cloak class="card space-y-4">
        <h2 class="text-base font-semibold text-slate-700">{{ $label }}</h2>

        <div class="flex gap-2 mb-2">
          <button type="button" @click="activeChannel='email'"
            :class="activeChannel==='email' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600'"
            class="btn-xs rounded-full px-3">Email</button>
          <button type="button" @click="activeChannel='sms'"
            :class="activeChannel==='sms' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600'"
            class="btn-xs rounded-full px-3">SMS</button>
          <button type="button" @click="activeChannel='whatsapp'"
            :class="activeChannel==='whatsapp' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600'"
            class="btn-xs rounded-full px-3">WhatsApp</button>
        </div>

        @foreach(['email', 'sms', 'whatsapp'] as $ch)
        @php $tpl = $templates->get($key.'_'.$ch); @endphp
        <div x-show="activeChannel==='{{ $ch }}'">
          <form method="POST" action="{{ route('settings.notification-templates.save') }}" class="space-y-3">
            @csrf
            <input type="hidden" name="event_type" value="{{ $key }}">
            <input type="hidden" name="channel" value="{{ $ch }}">

            @if($ch === 'email')
            <div>
              <label class="label">Subject</label>
              <input type="text" name="subject" class="input"
                value="{{ old('subject', $tpl?->subject) }}"
                placeholder="{{ $label }} — {{ config('app.name') }}">
            </div>
            @endif

            <div>
              <label class="label">Message Body</label>
              <textarea name="body" rows="8" class="input font-mono text-sm">{{ old('body', $tpl?->body ?? ($defaultBodies[$key] ?? '')) }}</textarea>
            </div>

            @if($ch === 'email')
            <div>
              <label class="label">Trigger Time <span class="text-slate-400 text-xs">(for scheduled sends, e.g. 09:30)</span></label>
              <input type="time" name="trigger_time" class="input w-32"
                value="{{ old('trigger_time', $tpl?->trigger_time) }}">
            </div>
            @endif

            <div class="flex items-center gap-3">
              <input type="checkbox" name="is_active" id="active_{{ $key }}_{{ $ch }}" class="w-4 h-4"
                {{ ($tpl?->is_active ?? true) ? 'checked' : '' }}>
              <label for="active_{{ $key }}_{{ $ch }}" class="label mb-0">Active</label>
            </div>

            <div class="flex justify-end">
              <button type="submit" class="btn-primary btn-sm">Save Template</button>
            </div>
          </form>
        </div>
        @endforeach
      </div>
      @endforeach

      <div x-show="!activeEvent" class="card text-center py-12 text-slate-400">
        <p>Select an event type on the left to configure its template.</p>
      </div>
    </div>
  </div>
</div>
@endsection
