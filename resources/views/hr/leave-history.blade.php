@extends('layouts.admin')
@section('title', 'Leave History')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('hr.employees.show', $employee->id) }}" class="text-blue-600 hover:underline">← {{ $employee->full_name }}</a>
        <span class="text-gray-400">/</span>
        <h1 class="page-title mb-0">Leave History</h1>
    </div>

    <div class="card bg-blue-50 border-blue-200">
        <div class="font-semibold text-blue-900">{{ $employee->full_name }}</div>
        <div class="text-sm text-blue-700">{{ $employee->designation?->name }} — {{ $employee->department?->name }}</div>
    </div>

    @if($leaves->count())
    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Leave Type</th>
                        <th class="th">From</th>
                        <th class="th">To</th>
                        <th class="th text-center">Days</th>
                        <th class="th">Status</th>
                        <th class="th">Reason</th>
                        <th class="th">Applied On</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr class="tr">
                        <td class="td font-medium">{{ $leave->leaveType?->name ?? '—' }}</td>
                        <td class="td">{{ $leave->from_date?->format('d M Y') }}</td>
                        <td class="td">{{ $leave->to_date?->format('d M Y') }}</td>
                        <td class="td text-center">{{ $leave->days }}</td>
                        <td class="td">
                            <span class="badge-{{
                                $leave->status === 'approved' ? 'success' :
                                ($leave->status === 'rejected' ? 'danger' :
                                ($leave->status === 'cancelled' ? 'secondary' : 'warning'))
                            }}">{{ ucfirst($leave->status) }}</span>
                        </td>
                        <td class="td text-gray-600 text-sm max-w-xs truncate">{{ $leave->reason }}</td>
                        <td class="td text-sm text-gray-500">{{ $leave->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{ $leaves->links() }}
    @else
    <div class="alert-info">No leave records found for this employee.</div>
    @endif
</div>
@endsection
