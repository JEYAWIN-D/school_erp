@extends('layouts.admin')
@section('title', 'Apply Leave')
@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('hr.leaves') }}" class="btn-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="page-title">Apply Leave</h1>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

    <div class="card" x-data="{ halfDay: false, isSick: false }">
        <form method="POST" action="{{ route('hr.leaves.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="label">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" class="select" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>
                                {{ $emp->full_name }} ({{ $emp->employee_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type_id" class="select" required
                        @change="isSick = $event.target.options[$event.target.selectedIndex].text.toLowerCase().includes('sick') || $event.target.options[$event.target.selectedIndex].text.toLowerCase().includes('medical')">
                        <option value="">Select Type</option>
                        @foreach($leaveTypes as $lt)
                            <option value="{{ $lt->id }}" @selected(old('leave_type_id') == $lt->id)>{{ $lt->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_half_day" value="1" id="halfDay"
                        class="w-4 h-4 text-indigo-600" x-model="halfDay" @checked(old('is_half_day'))>
                    <label for="halfDay" class="label cursor-pointer">Half Day Leave</label>
                </div>

                <div x-show="halfDay" x-transition>
                    <label class="label">Session</label>
                    <select name="half_day_session" class="select w-48">
                        <option value="morning">Morning</option>
                        <option value="afternoon">Afternoon</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">From Date <span class="text-red-500">*</span></label>
                        <input name="from_date" type="date" class="input" value="{{ old('from_date') }}" required>
                    </div>
                    <div x-show="!halfDay">
                        <label class="label">To Date</label>
                        <input name="to_date" type="date" class="input" value="{{ old('to_date') }}"
                            :required="!halfDay" x-bind:value="halfDay ? $el.closest('form').querySelector('[name=from_date]').value : ''">
                    </div>
                </div>

                <div>
                    <label class="label">Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" class="input" required>{{ old('reason') }}</textarea>
                </div>

                <div>
                    <label class="label">
                        Medical Certificate / Supporting Document
                        <span x-show="isSick" class="text-red-500">*</span>
                        <span x-show="!isSick" class="text-slate-400 text-xs font-normal">(optional)</span>
                    </label>
                    <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"
                        :required="isSick"
                        class="block w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200 rounded-lg p-1">
                    <p class="text-xs text-slate-400 mt-1">PDF, JPG or PNG; max 2 MB. Required for Sick/Medical Leave.</p>
                    @error('attachment')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
                </div>
                @endif

                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary flex-1">Submit Application</button>
                    <a href="{{ route('hr.leaves') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
