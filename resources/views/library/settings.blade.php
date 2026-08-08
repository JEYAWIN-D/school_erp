@extends('layouts.app')
@section('title', 'Library Settings')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <h1 class="page-title">Library Settings</h1>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    <form method="POST" action="{{ route('library.settings.save') }}">
        @csrf
        @foreach(['student' => $student, 'staff' => $staff] as $type => $setting)
        <div class="card mb-4">
            <h2 class="font-semibold text-slate-700 mb-4 capitalize">{{ $type }} Settings</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Fine Per Day (₹)</label>
                    <input type="number" name="types[{{ $type }}][fine_per_day]" step="0.01" min="0"
                        value="{{ $setting->fine_per_day }}" class="input">
                </div>
                <div>
                    <label class="label">Loan Period (days)</label>
                    <input type="number" name="types[{{ $type }}][loan_days]" min="1"
                        value="{{ $setting->loan_days }}" class="input">
                </div>
                <div>
                    <label class="label">Max Books at a Time</label>
                    <input type="number" name="types[{{ $type }}][max_books]" min="1"
                        value="{{ $setting->max_books }}" class="input">
                </div>
                <div>
                    <label class="label">Max Renewals</label>
                    <input type="number" name="types[{{ $type }}][max_renewals]" min="0"
                        value="{{ $setting->max_renewals }}" class="input">
                </div>
            </div>
        </div>
        @endforeach
        <button type="submit" class="btn btn-primary w-full">Save Library Settings</button>
    </form>
</div>
@endsection
