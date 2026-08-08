@extends('layouts.app')
@section('title', 'Fine / Late Fee Collection Report')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Fine Collection Report</h1>
            <p class="page-subtitle">Late fee / fine collected from students</p>
        </div>
        <button onclick="window.print()" class="btn btn-secondary btn-sm">Print</button>
    </div>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="label">Class</label>
                <select name="class_id" class="select">
                    <option value="">All Classes</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" @selected(request('class_id') == $cls->id)>{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">From Date</label>
                <input type="date" name="from_date" class="input" value="{{ request('from_date') }}">
            </div>
            <div>
                <label class="label">To Date</label>
                <input type="date" name="to_date" class="input" value="{{ request('to_date') }}">
            </div>
            <button type="submit" class="btn btn-primary">Generate</button>
        </form>
    </div>

    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-700">{{ $payments->count() }} transactions</h3>
            <span class="text-lg font-bold text-red-600">Total Fine: ₹{{ number_format($totalFine, 2) }}</span>
        </div>
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Date</th>
                    <th class="th">Receipt No.</th>
                    <th class="th">Student</th>
                    <th class="th">Class</th>
                    <th class="th">Fee Head</th>
                    <th class="th text-right">Amount Paid</th>
                    <th class="th text-right">Late Fee</th>
                    <th class="th">Mode</th>
                </tr></thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr class="tr">
                        <td class="td">{{ \Carbon\Carbon::parse($p->payment_date)->format('d M Y') }}</td>
                        <td class="td font-mono text-blue-600">{{ $p->receipt_number }}</td>
                        <td class="td">
                            <p class="font-medium">{{ $p->student?->first_name }} {{ $p->student?->last_name }}</p>
                            <p class="text-xs text-slate-400">{{ $p->student?->admission_number }}</p>
                        </td>
                        <td class="td text-slate-500">{{ $p->student?->currentEnrollment?->class?->name ?? '—' }}</td>
                        <td class="td">{{ $p->feeHead?->name ?? '—' }}</td>
                        <td class="td text-right">₹{{ number_format($p->amount, 2) }}</td>
                        <td class="td text-right font-semibold text-red-600">₹{{ number_format($p->late_fee, 2) }}</td>
                        <td class="td text-slate-500 capitalize">{{ $p->payment_mode }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="td text-center text-slate-400 py-8">No fine transactions found for the selected filters.</td></tr>
                    @endforelse
                </tbody>
                @if($payments->count() > 0)
                <tfoot>
                    <tr class="bg-slate-50">
                        <td colspan="6" class="td font-semibold text-right">Total Fine Collected:</td>
                        <td class="td font-bold text-red-600 text-right">₹{{ number_format($totalFine, 2) }}</td>
                        <td class="td"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
