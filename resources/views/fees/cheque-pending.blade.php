@extends('layouts.app')
@section('title', 'Pending Cheque Clearances')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Pending Cheques</h1>
            <p class="page-subtitle">Mark cheques as cleared or bounced</p>
        </div>
        <a href="{{ route('fees.bank-reconciliation') }}" class="btn btn-secondary btn-sm">Bank Reconciliation →</a>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-danger">{{ session('error') }}</div>@endif

    <div class="card">
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Receipt No.</th>
                    <th class="th">Student</th>
                    <th class="th">Fee Head</th>
                    <th class="th">Amount</th>
                    <th class="th">Cheque No.</th>
                    <th class="th">Bank / Branch</th>
                    <th class="th">Cheque Date</th>
                    <th class="th">Action</th>
                </tr></thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr class="tr" x-data="{bounceOpen:false}">
                        <td class="td font-mono text-blue-600">{{ $p->receipt_number }}</td>
                        <td class="td">
                            <p class="font-medium">{{ $p->student?->first_name }} {{ $p->student?->last_name }}</p>
                            <p class="text-xs text-slate-400">{{ $p->student?->admission_number }}</p>
                        </td>
                        <td class="td">{{ $p->feeHead?->name }}</td>
                        <td class="td">₹{{ number_format($p->amount, 2) }}</td>
                        <td class="td font-mono">{{ $p->cheque_number }}</td>
                        <td class="td text-slate-500">{{ $p->cheque_bank }} / {{ $p->cheque_branch }}</td>
                        <td class="td">{{ $p->cheque_date?->format('d M Y') }}</td>
                        <td class="td">
                            <div class="flex gap-2 flex-wrap">
                                <form method="POST" action="{{ route('fees.cheque.clear', $p->id) }}" class="inline">
                                    @csrf
                                    <button class="btn btn-success btn-xs">Cleared</button>
                                </form>
                                <button @click="bounceOpen=!bounceOpen" class="btn btn-danger btn-xs">Bounced</button>
                            </div>
                            <div x-show="bounceOpen" x-transition class="mt-2 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <form method="POST" action="{{ route('fees.cheque.bounce', $p->id) }}" class="space-y-2">
                                    @csrf
                                    <input type="text" name="bounce_reason" class="input text-sm" placeholder="Bounce reason (e.g. Insufficient funds)">
                                    <input type="number" name="bounce_charge" class="input text-sm w-32" placeholder="Bounce charge ₹" min="0" step="0.01">
                                    <button type="submit" class="btn btn-danger btn-xs">Confirm Bounce</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="td text-center text-slate-400 py-8">No pending cheques.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
