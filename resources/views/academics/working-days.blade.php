@extends('layouts.admin')
@section('title', 'Working Days Configuration')
@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <h1 class="page-title">Working Days Configuration</h1>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    <div class="card">
        <p class="text-sm text-slate-500 mb-4">Select which days of the week are school working days.</p>
        <form method="POST" action="{{ route('academics.working-days.save') }}">
            @csrf
            @php
                $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                $active = $config['days'] ?? ['Monday','Tuesday','Wednesday','Thursday','Friday'];
            @endphp
            <div class="space-y-2 mb-6">
                @foreach($days as $day)
                <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50">
                    <input type="checkbox" name="days[days][]" value="{{ $day }}" class="w-4 h-4 text-indigo-600"
                        @checked(in_array($day, $active))>
                    <span class="font-medium text-slate-700">{{ $day }}</span>
                </label>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary w-full">Save Working Days</button>
        </form>
    </div>

    <div class="card bg-amber-50 border border-amber-200">
        <p class="text-sm text-amber-700"><strong>Note:</strong> This setting is used for attendance calculations, leave counting, and working day reports. Changes apply from the current date.</p>
    </div>
</div>
@endsection
