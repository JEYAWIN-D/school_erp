@extends('layouts.admin')
@section('title', 'Maintenance Complaint Status Report')
@section('content')
<div class="space-y-6">
    <h1 class="page-title">Maintenance Complaint Status Report</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card text-center border-yellow-200 bg-yellow-50">
            <div class="text-sm text-yellow-700">Open</div>
            <div class="text-3xl font-bold text-yellow-800">{{ $statusCounts['open'] ?? 0 }}</div>
        </div>
        <div class="card text-center border-blue-200 bg-blue-50">
            <div class="text-sm text-blue-700">In Progress</div>
            <div class="text-3xl font-bold text-blue-800">{{ $statusCounts['in_progress'] ?? 0 }}</div>
        </div>
        <div class="card text-center border-green-200 bg-green-50">
            <div class="text-sm text-green-700">Resolved</div>
            <div class="text-3xl font-bold text-green-800">{{ $statusCounts['resolved'] ?? 0 }}</div>
        </div>
    </div>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="label">Hostel Block</label>
                <select name="hostel_id" class="select">
                    <option value="">All Hostels</option>
                    @foreach($hostels as $h)
                        <option value="{{ $h->id }}" @selected(request('hostel_id') == $h->id)>{{ $h->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Status</label>
                <select name="status" class="select">
                    <option value="">All Statuses</option>
                    <option value="open" @selected(request('status') == 'open')>Open</option>
                    <option value="in_progress" @selected(request('status') == 'in_progress')>In Progress</option>
                    <option value="resolved" @selected(request('status') == 'resolved')>Resolved</option>
                </select>
            </div>
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
                        <th class="th">Student</th>
                        <th class="th">Room</th>
                        <th class="th">Category</th>
                        <th class="th">Description</th>
                        <th class="th">Status</th>
                        <th class="th">Resolution</th>
                        <th class="th">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $c)
                    <tr class="tr">
                        <td class="td">
                            <div class="font-medium">{{ $c->student?->full_name ?? '—' }}</div>
                        </td>
                        <td class="td text-sm">{{ $c->room?->room_number ?? '—' }}</td>
                        <td class="td">
                            <span class="badge-secondary">{{ ucfirst($c->category ?? 'general') }}</span>
                        </td>
                        <td class="td text-sm text-gray-700 max-w-xs">{{ Str::limit($c->description, 80) }}</td>
                        <td class="td">
                            <span class="badge-{{
                                $c->status === 'resolved' ? 'success' :
                                ($c->status === 'in_progress' ? 'info' : 'warning')
                            }}">{{ ucwords(str_replace('_', ' ', $c->status)) }}</span>
                        </td>
                        <td class="td text-sm text-gray-600">{{ Str::limit($c->resolution_notes, 60) ?? '—' }}</td>
                        <td class="td text-sm text-gray-500">{{ $c->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="td text-center text-gray-400">No complaints found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $complaints->links() }}
</div>
@endsection
