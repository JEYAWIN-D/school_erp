@extends('layouts.app')
@section('title', 'Salary Advances')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Salary Advances</h1>
            <p class="page-subtitle">Record, approve, and track advance salary recovery</p>
        </div>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif

    {{-- New Advance Form --}}
    <div class="card">
        <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">New Advance Request</h3>
        <form method="POST" action="{{ route('hr.salary-advances.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="label">Employee <span class="text-red-500">*</span></label>
                <select name="employee_id" class="select" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_number }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Amount (₹) <span class="text-red-500">*</span></label>
                <input type="number" name="amount" class="input" min="1" step="0.01" required placeholder="0.00">
            </div>
            <div>
                <label class="label">Advance Date <span class="text-red-500">*</span></label>
                <input type="date" name="advance_date" class="input" value="{{ date('Y-m-d') }}" required>
            </div>
            <div>
                <label class="label">Recovery Months <span class="text-red-500">*</span></label>
                <input type="number" name="recovery_months" class="input" min="1" max="24" value="3" required>
            </div>
            <div>
                <label class="label">Reason</label>
                <input type="text" name="reason" class="input" placeholder="Medical / Personal / Other">
            </div>
            <div>
                <label class="label">Remarks</label>
                <input type="text" name="remarks" class="input">
            </div>
            <div class="md:col-span-3">
                <button type="submit" class="btn btn-primary">Submit Advance Request</button>
            </div>
        </form>
    </div>

    {{-- Filter --}}
    <div class="card">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="label">Employee</label>
                <select name="employee_id" class="select">
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" @selected(request('employee_id') == $emp->id)>
                            {{ $emp->first_name }} {{ $emp->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Status</label>
                <select name="status" class="select">
                    <option value="">All</option>
                    <option value="pending"   @selected(request('status')=='pending')>Pending</option>
                    <option value="approved"  @selected(request('status')=='approved')>Approved</option>
                    <option value="rejected"  @selected(request('status')=='rejected')>Rejected</option>
                    <option value="recovered" @selected(request('status')=='recovered')>Fully Recovered</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>

    {{-- Advances Table --}}
    <div class="card">
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Employee</th>
                    <th class="th">Amount</th>
                    <th class="th">Date</th>
                    <th class="th">Months</th>
                    <th class="th">Monthly Deduction</th>
                    <th class="th">Recovered</th>
                    <th class="th">Remaining</th>
                    <th class="th">Status</th>
                    <th class="th">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($advances as $adv)
                    <tr class="tr">
                        <td class="td font-medium">{{ $adv->employee?->first_name }} {{ $adv->employee?->last_name }}</td>
                        <td class="td">₹{{ number_format($adv->amount, 2) }}</td>
                        <td class="td text-slate-500">{{ $adv->advance_date->format('d M Y') }}</td>
                        <td class="td text-center">{{ $adv->recovery_months }}</td>
                        <td class="td">₹{{ number_format($adv->monthly_deduction, 2) }}</td>
                        <td class="td text-green-600">₹{{ number_format($adv->recovered_amount, 2) }}</td>
                        <td class="td {{ $adv->remaining > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ₹{{ number_format($adv->remaining, 2) }}
                        </td>
                        <td class="td">
                            @php $badgeMap = ['pending'=>'badge-warning','approved'=>'badge-info','rejected'=>'badge-danger','recovered'=>'badge-success']; @endphp
                            <span class="{{ $badgeMap[$adv->status] ?? 'badge' }}">{{ ucfirst($adv->status) }}</span>
                        </td>
                        <td class="td">
                            <div class="flex gap-1 flex-wrap">
                                @if($adv->status === 'pending')
                                <form method="POST" action="{{ route('hr.salary-advances.approve', $adv->id) }}" class="inline">
                                    @csrf
                                    <button class="btn btn-primary btn-xs">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('hr.salary-advances.reject', $adv->id) }}" class="inline">
                                    @csrf
                                    <button class="btn btn-secondary btn-xs">Reject</button>
                                </form>
                                @elseif($adv->status === 'approved' && $adv->remaining > 0)
                                <form method="POST" action="{{ route('hr.salary-advances.recovery', $adv->id) }}" class="inline flex gap-1 items-center">
                                    @csrf
                                    <input type="number" name="recovery_amount" class="input w-24 text-sm py-1"
                                           placeholder="Amount" min="0.01" max="{{ $adv->remaining }}" step="0.01">
                                    <button class="btn btn-success btn-xs">Record</button>
                                </form>
                                @else
                                <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="td text-center text-slate-400 py-8">No salary advance records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($advances->hasPages())
        <div class="px-4 pb-4">{{ $advances->links() }}</div>
        @endif
    </div>
</div>
@endsection
