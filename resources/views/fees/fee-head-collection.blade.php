@extends('layouts.admin')
@section('title', 'Fee Head-wise Collection')
@section('content')
<div class="space-y-6">
    <h1 class="page-title">Fee Head-wise Collection Report</h1>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="label">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="input">
            </div>
            <div>
                <label class="label">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="input">
            </div>
            <button class="btn-primary">Filter</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($data as $row)
        @if($row['collected'] > 0)
        <div class="card">
            <div class="flex justify-between items-start">
                <div>
                    <div class="font-semibold text-gray-800">{{ $row['head']->name }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ ucfirst($row['head']->type ?? 'fee') }}</div>
                </div>
                @if($grandTotal > 0)
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                    {{ round(($row['collected'] / $grandTotal) * 100, 1) }}%
                </span>
                @endif
            </div>
            <div class="mt-3 text-2xl font-bold text-green-700">₹{{ number_format($row['collected']) }}</div>
            @if($grandTotal > 0)
            <div class="mt-2 bg-gray-200 rounded-full h-1.5">
                <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ min(100, round(($row['collected'] / $grandTotal) * 100, 1)) }}%"></div>
            </div>
            @endif
        </div>
        @endif
        @endforeach
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">#</th>
                        <th class="th">Fee Head</th>
                        <th class="th">Type</th>
                        <th class="th text-right">Collected (₹)</th>
                        <th class="th text-center">Share</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $i => $row)
                    <tr class="tr">
                        <td class="td text-gray-400">{{ $i + 1 }}</td>
                        <td class="td font-medium">{{ $row['head']->name }}</td>
                        <td class="td">{{ ucfirst($row['head']->type ?? '—') }}</td>
                        <td class="td text-right font-semibold">{{ number_format($row['collected']) }}</td>
                        <td class="td text-center text-gray-500 text-sm">
                            {{ $grandTotal > 0 ? round(($row['collected'] / $grandTotal) * 100, 1) . '%' : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-bold">
                        <td class="td" colspan="3">Grand Total</td>
                        <td class="td text-right text-green-700">₹{{ number_format($grandTotal) }}</td>
                        <td class="td text-center">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
