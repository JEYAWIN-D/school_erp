@extends('layouts.admin')
@section('title', 'Vehicle Utilisation Report')
@section('content')
<div class="space-y-6">
    <h1 class="page-title">Vehicle Utilisation Report</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @php
            $totalCapacity = $data->sum('capacity');
            $totalAssigned = $data->sum('assigned');
            $overallPct    = $totalCapacity > 0 ? round(($totalAssigned / $totalCapacity) * 100, 1) : 0;
        @endphp
        <div class="card text-center">
            <div class="text-sm text-gray-500">Total Capacity</div>
            <div class="text-3xl font-bold text-gray-800">{{ $totalCapacity }}</div>
        </div>
        <div class="card text-center">
            <div class="text-sm text-gray-500">Students Assigned</div>
            <div class="text-3xl font-bold text-blue-700">{{ $totalAssigned }}</div>
        </div>
        <div class="card text-center">
            <div class="text-sm text-gray-500">Overall Utilisation</div>
            <div class="text-3xl font-bold {{ $overallPct >= 90 ? 'text-red-600' : ($overallPct >= 70 ? 'text-yellow-600' : 'text-green-600') }}">
                {{ $overallPct }}%
            </div>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Vehicle</th>
                        <th class="th">Type</th>
                        <th class="th">Route</th>
                        <th class="th text-center">Capacity</th>
                        <th class="th text-center">Assigned</th>
                        <th class="th text-center">Available</th>
                        <th class="th">Utilisation</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $row)
                    @php $color = $row['utilisation'] >= 90 ? 'red' : ($row['utilisation'] >= 70 ? 'yellow' : 'green'); @endphp
                    <tr class="tr">
                        <td class="td font-medium">{{ $row['vehicle']->vehicle_number }}</td>
                        <td class="td">{{ $row['vehicle']->vehicle_type ?? '—' }}</td>
                        <td class="td text-sm">{{ $row['vehicle']->route?->route_name ?? 'Unassigned' }}</td>
                        <td class="td text-center">{{ $row['capacity'] }}</td>
                        <td class="td text-center font-semibold">{{ $row['assigned'] }}</td>
                        <td class="td text-center {{ $row['available'] == 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $row['available'] }}
                        </td>
                        <td class="td w-48">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2.5">
                                    <div class="h-2.5 rounded-full bg-{{ $color }}-500"
                                         style="width: {{ min(100, $row['utilisation']) }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-{{ $color }}-700 w-12">
                                    {{ $row['utilisation'] }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
