@extends('layouts.admin')
@section('title', 'Room Transfer')
@section('content')
<div class="space-y-6">
    <h1 class="page-title">Room Transfer</h1>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    {{-- Filter --}}
    <form method="GET" class="card py-3">
        <select name="hostel_id" class="select w-52" onchange="this.form.submit()">
            <option value="">All Hostels</option>
            @foreach($hostels as $h)
                <option value="{{ $h->id }}" @selected(request('hostel_id') == $h->id)>{{ $h->name }}</option>
            @endforeach
        </select>
    </form>

    <div class="card p-0 overflow-hidden">
        <div class="p-3 bg-slate-50 border-b border-slate-100">
            <p class="font-semibold text-slate-700">Current Allotments — click Transfer to move a student</p>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th class="th">Student</th>
                    <th class="th">Current Room</th>
                    <th class="th">Hostel</th>
                    <th class="th text-center">Transfer</th>
                </tr></thead>
                <tbody>
                    @forelse($allotments as $allotment)
                    <tr class="tr" x-data="{ open: false }">
                        <td class="td">
                            <div class="font-medium text-slate-800">{{ $allotment->student?->full_name }}</div>
                            <div class="text-xs text-slate-400">{{ $allotment->student?->admission_number }}</div>
                        </td>
                        <td class="td">{{ $allotment->room?->room_number }}</td>
                        <td class="td text-slate-500">{{ $allotment->room?->hostel?->name }}</td>
                        <td class="td text-center">
                            <button @click="open = !open" class="btn btn-ghost btn-sm text-blue-600">Transfer</button>
                            <div x-show="open" x-transition class="mt-3 p-3 bg-slate-50 rounded-lg border border-slate-200 text-left">
                                <form method="POST" action="{{ route('hostel.transfer.process') }}">
                                    @csrf
                                    <input type="hidden" name="allotment_id" value="{{ $allotment->id }}">
                                    <div class="space-y-2">
                                        <div>
                                            <label class="label text-xs">New Room</label>
                                            <select name="new_room_id" class="select text-sm" required>
                                                <option value="">Select Room</option>
                                                @foreach($availableRooms as $room)
                                                    @if($room->id !== $allotment->room_id)
                                                    <option value="{{ $room->id }}">{{ $room->hostel?->name }} — Room {{ $room->room_number }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="label text-xs">Reason</label>
                                            <input name="transfer_reason" class="input text-sm" placeholder="Optional reason">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm w-full">Confirm Transfer</button>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="td text-center text-slate-400 py-8">No active allotments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $allotments->links() }}
</div>
@endsection
