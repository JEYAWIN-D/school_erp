@extends('layouts.admin')
@section('title', 'Department-wise Salary Summary')
@section('content')
<div class="space-y-6">
    <h1 class="page-title">Department-wise Salary Summary</h1>

    <div class="card">
        <form method="GET" class="flex gap-3 items-end">
            <div>
                <label class="label">Month</label>
                <input type="month" name="month" value="{{ $month }}" class="input">
            </div>
            <button class="btn-primary">View</button>
        </form>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card text-center">
            <div class="text-sm text-gray-500">Total Employees</div>
            <div class="text-2xl font-bold text-gray-800">{{ $grandTotal['count'] }}</div>
        </div>
        <div class="card text-center">
            <div class="text-sm text-gray-500">Total Gross</div>
            <div class="text-2xl font-bold text-blue-700">₹{{ number_format($grandTotal['gross']) }}</div>
        </div>
        <div class="card text-center">
            <div class="text-sm text-gray-500">Total Deductions</div>
            <div class="text-2xl font-bold text-red-600">₹{{ number_format($grandTotal['deductions']) }}</div>
        </div>
        <div class="card text-center">
            <div class="text-sm text-gray-500">Total Net Pay</div>
            <div class="text-2xl font-bold text-green-700">₹{{ number_format($grandTotal['net']) }}</div>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Department</th>
                        <th class="th text-center">Employees</th>
                        <th class="th text-right">Gross (₹)</th>
                        <th class="th text-right">Deductions (₹)</th>
                        <th class="th text-right">Net Pay (₹)</th>
                        <th class="th text-center">% of Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                    <tr class="tr">
                        <td class="td font-medium">{{ $row['department']->name }}</td>
                        <td class="td text-center">{{ $row['count'] }}</td>
                        <td class="td text-right">{{ number_format($row['gross']) }}</td>
                        <td class="td text-right text-red-600">{{ number_format($row['deductions']) }}</td>
                        <td class="td text-right font-semibold text-green-700">{{ number_format($row['net']) }}</td>
                        <td class="td text-center text-sm text-gray-500">
                            {{ $grandTotal['gross'] > 0 ? round(($row['gross'] / $grandTotal['gross']) * 100, 1) . '%' : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-bold">
                        <td class="td">Grand Total</td>
                        <td class="td text-center">{{ $grandTotal['count'] }}</td>
                        <td class="td text-right">{{ number_format($grandTotal['gross']) }}</td>
                        <td class="td text-right text-red-600">{{ number_format($grandTotal['deductions']) }}</td>
                        <td class="td text-right text-green-700">{{ number_format($grandTotal['net']) }}</td>
                        <td class="td text-center">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
