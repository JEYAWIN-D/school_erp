@extends('layouts.app')
@section('title', 'Scholarship Renewal Reminders')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Scholarship Renewal Reminders</h1>
            <p class="page-subtitle">Concessions expiring soon or already expired</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <label class="label mb-0">Days ahead</label>
            <input type="number" name="days" value="{{ $daysAhead }}" class="input w-20" min="1" max="365">
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>

    {{-- Expiring Soon --}}
    <div class="card">
        <h3 class="font-semibold text-amber-600 mb-4 pb-2 border-b border-amber-100">
            Expiring in ≤ {{ $daysAhead }} days
            <span class="text-slate-400 font-normal text-sm">({{ $expiring->count() }} concessions)</span>
        </h3>
        @if($expiring->isEmpty())
            <p class="text-slate-400 text-sm text-center py-6">No concessions expiring in this window.</p>
        @else
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Student</th>
                    <th class="th">Scheme / Type</th>
                    <th class="th">Value</th>
                    <th class="th">Expiry Date</th>
                    <th class="th">Days Left</th>
                    <th class="th">Action</th>
                </tr></thead>
                <tbody>
                    @foreach($expiring as $c)
                    @php $daysLeft = now()->diffInDays($c->valid_to, false); @endphp
                    <tr class="tr">
                        <td class="td">
                            <p class="font-medium">{{ $c->student?->first_name }} {{ $c->student?->last_name }}</p>
                            <p class="text-xs text-slate-400">{{ $c->student?->admission_number }}</p>
                        </td>
                        <td class="td">{{ $c->scheme?->name ?? ucfirst($c->concession_type) }}</td>
                        <td class="td">
                            {{ $c->value_type === 'percentage' ? $c->value . '%' : '₹' . number_format($c->value, 2) }}
                        </td>
                        <td class="td">{{ $c->valid_to?->format('d M Y') }}</td>
                        <td class="td">
                            <span class="badge {{ $daysLeft <= 7 ? 'badge-danger' : 'badge-warning' }}">
                                {{ $daysLeft }} day{{ $daysLeft != 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td class="td">
                            <a href="{{ route('students.concessions', $c->student_id) }}" class="btn btn-primary btn-xs">Renew</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Already Expired --}}
    <div class="card">
        <h3 class="font-semibold text-red-600 mb-4 pb-2 border-b border-red-100">
            Already Expired (still marked active)
            <span class="text-slate-400 font-normal text-sm">({{ $expired->count() }} concessions)</span>
        </h3>
        @if($expired->isEmpty())
            <p class="text-slate-400 text-sm text-center py-6">No expired active concessions found.</p>
        @else
        <div class="table-wrap">
            <table class="w-full">
                <thead><tr>
                    <th class="th">Student</th>
                    <th class="th">Scheme / Type</th>
                    <th class="th">Expiry Date</th>
                    <th class="th">Action</th>
                </tr></thead>
                <tbody>
                    @foreach($expired as $c)
                    <tr class="tr">
                        <td class="td">
                            <p class="font-medium">{{ $c->student?->first_name }} {{ $c->student?->last_name }}</p>
                            <p class="text-xs text-slate-400">{{ $c->student?->admission_number }}</p>
                        </td>
                        <td class="td">{{ $c->scheme?->name ?? ucfirst($c->concession_type) }}</td>
                        <td class="td text-red-500">{{ $c->valid_to?->format('d M Y') }}</td>
                        <td class="td">
                            <div class="flex gap-1">
                                <a href="{{ route('students.concessions', $c->student_id) }}" class="btn btn-primary btn-xs">Renew</a>
                                <form method="POST" action="{{ route('fees.concessions.delete', $c->id) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-secondary btn-xs" onclick="return confirm('Revoke this expired concession?')">Revoke</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
