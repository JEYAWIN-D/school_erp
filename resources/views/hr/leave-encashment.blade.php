@extends('layouts.app')
@section('title', 'Leave Encashment (EL)')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Leave Encashment</h1>
            <p class="page-subtitle">Process Earned Leave (EL) encashment for employees</p>
        </div>
        <a href="{{ route('hr.leaves') }}" class="btn btn-secondary">Back to Leaves</a>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Calculator --}}
        <div class="card space-y-4">
            <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Calculate Encashment</h3>
            <form method="GET" action="{{ route('hr.leave-encashment') }}" class="space-y-4">
                <div>
                    <label class="label">Employee <span class="text-red-500">*</span></label>
                    <select name="employee_id" class="select" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>
                                {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Leave Type <span class="text-red-500">*</span></label>
                    <select name="leave_type_id" class="select" required>
                        <option value="">Select Type</option>
                        @foreach($elTypes as $lt)
                            <option value="{{ $lt->id }}" @selected(request('leave_type_id') == $lt->id)>{{ $lt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Year</label>
                        <input type="number" name="year" class="input" value="{{ request('year', date('Y')) }}" min="2000" max="2099">
                    </div>
                    <div>
                        <label class="label">Days to Encash</label>
                        <input type="number" name="days" class="input" min="1" value="{{ request('days', 1) }}" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-secondary w-full">Calculate</button>
            </form>

            @if($calculation)
            <div class="mt-4 p-4 bg-indigo-50 border border-indigo-200 rounded-lg space-y-2">
                <p class="text-sm font-semibold text-indigo-800">Calculation Result</p>
                <div class="text-sm text-slate-700 space-y-1">
                    <div class="flex justify-between"><span>Employee:</span><span class="font-medium">{{ $calculation['emp']->full_name }}</span></div>
                    <div class="flex justify-between"><span>Basic Salary / 26:</span><span class="font-medium">₹{{ number_format($calculation['basicPerDay'], 2) }}</span></div>
                    <div class="flex justify-between"><span>EL Balance ({{ $calculation['year'] }}):</span><span class="font-medium">{{ $calculation['balance'] }} days</span></div>
                    <div class="flex justify-between"><span>Days Encashed:</span><span class="font-medium">{{ $calculation['days'] }} days</span></div>
                    <div class="flex justify-between border-t border-indigo-200 pt-2 mt-2">
                        <span class="font-semibold">Encashment Amount:</span>
                        <span class="font-bold text-indigo-700 text-base">₹{{ number_format($calculation['amount'], 2) }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('hr.leave-encashment.process') }}" class="mt-3 space-y-3 border-t border-indigo-200 pt-3">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $calculation['emp']->id }}">
                    <input type="hidden" name="leave_type_id" value="{{ $calculation['lt']->id }}">
                    <input type="hidden" name="days_encashed" value="{{ $calculation['days'] }}">
                    <input type="hidden" name="year" value="{{ $calculation['year'] }}">
                    <div>
                        <label class="label">Encashment Date</label>
                        <input type="date" name="encashment_date" class="input" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label class="label">Remarks (Optional)</label>
                        <input type="text" name="remarks" class="input" placeholder="e.g. Annual EL encashment">
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Process Encashment</button>
                </form>
            </div>
            @endif
        </div>

        {{-- History --}}
        <div class="card">
            <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100 mb-4">Encashment History</h3>
            @forelse($history as $enc)
            <div class="flex items-center justify-between py-2.5 border-b border-slate-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ $enc->employee?->full_name ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $enc->leaveType?->name }} · {{ $enc->days_encashed }} days · {{ $enc->year }}</p>
                    <p class="text-xs text-slate-400">{{ $enc->encashment_date?->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-green-700 text-sm">₹{{ number_format($enc->amount, 2) }}</p>
                    @if($enc->remarks)
                        <p class="text-xs text-slate-400 max-w-[150px] text-right truncate">{{ $enc->remarks }}</p>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-slate-400 text-sm text-center py-6">No encashments recorded yet.</p>
            @endforelse
            {{ $history->links() }}
        </div>
    </div>
</div>
@endsection
