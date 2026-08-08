@extends('layouts.admin')
@section('title', 'Bulk Fee Assignment')
@section('content')
<div class="space-y-6">
    <h1 class="page-title">Bulk Fee Assignment</h1>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="card">
                <h2 class="font-semibold text-slate-700 mb-4">Assign Fee Structure</h2>
                <form method="POST" action="{{ route('fees.bulk-assign.process') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="label">Class</label>
                            <select name="class_id" class="select" required>
                                <option value="">Select Class</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-3 text-sm text-blue-700">
                            This will link the current academic year's fee structure to all active students in the selected class. Already-assigned fees are not duplicated.
                        </div>
                        <button type="submit" class="btn btn-primary w-full">Assign Fee Structure</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="card p-0 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-100">
                    <p class="font-semibold text-slate-700">Fee Structures — {{ $currentYear?->name }}</p>
                </div>
                @if($structures->count())
                <div class="table-wrap">
                    <table>
                        <thead><tr>
                            <th class="th">Class</th>
                            <th class="th">Fee Heads</th>
                            <th class="th text-right">Total</th>
                        </tr></thead>
                        <tbody>
                            @foreach($classes as $cls)
                                @if(isset($structures[$cls->id]))
                                <tr class="tr">
                                    <td class="td font-semibold">{{ $cls->name }}</td>
                                    <td class="td text-sm text-slate-500">
                                        {{ $structures[$cls->id]->pluck('head.name')->implode(', ') }}
                                    </td>
                                    <td class="td text-right font-semibold">
                                        ₹{{ number_format($structures[$cls->id]->sum('amount'), 2) }}
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="p-8 text-center text-slate-400">No fee structures defined for current year.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
