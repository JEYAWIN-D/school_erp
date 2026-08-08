@extends('layouts.admin')
@section('title', 'Class-wise Fee Collection')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="page-title">Class-wise Fee Collection</h1>
    </div>

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

    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Class</th>
                        <th class="th text-center">Students</th>
                        <th class="th text-right">Expected (₹)</th>
                        <th class="th text-right">Collected (₹)</th>
                        <th class="th text-right">Outstanding (₹)</th>
                        <th class="th text-center">Collection %</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalExpected = 0; $totalCollected = 0; $totalOutstanding = 0; @endphp
                    @foreach($data as $row)
                    @php
                        $totalExpected += $row['expected'];
                        $totalCollected += $row['collected'];
                        $totalOutstanding += $row['outstanding'];
                        $pct = $row['expected'] > 0 ? round(($row['collected'] / $row['expected']) * 100, 1) : 0;
                    @endphp
                    <tr class="tr">
                        <td class="td font-medium">{{ $row['class']->name }}</td>
                        <td class="td text-center">{{ $row['student_count'] }}</td>
                        <td class="td text-right">{{ number_format($row['expected']) }}</td>
                        <td class="td text-right text-green-700 font-semibold">{{ number_format($row['collected']) }}</td>
                        <td class="td text-right text-red-600">{{ number_format($row['outstanding']) }}</td>
                        <td class="td text-center">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ min(100, $pct) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold w-10">{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-bold">
                        <td class="td" colspan="2">Grand Total</td>
                        <td class="td text-right">{{ number_format($totalExpected) }}</td>
                        <td class="td text-right text-green-700">{{ number_format($totalCollected) }}</td>
                        <td class="td text-right text-red-600">{{ number_format($totalOutstanding) }}</td>
                        <td class="td text-center">
                            @if($totalExpected > 0)
                                {{ round(($totalCollected / $totalExpected) * 100, 1) }}%
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
