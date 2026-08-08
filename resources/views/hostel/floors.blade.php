@extends('layouts.admin')
@section('title', 'Hostel Floor Master')
@section('content')
<div class="space-y-6" x-data="{ showForm: false }">
    <div class="flex items-center justify-between">
        <h1 class="page-title">Hostel Floor Master</h1>
        <button @click="showForm = !showForm" class="btn btn-primary btn-sm">+ Add Floor</button>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    {{-- Add form --}}
    <div x-show="showForm" x-transition class="card">
        <h2 class="font-semibold text-slate-700 mb-4">New Floor</h2>
        <form method="POST" action="{{ route('hostel.floors.store') }}">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="label">Hostel</label>
                    <select name="hostel_id" class="select" required>
                        @foreach($hostels as $h)
                            <option value="{{ $h->id }}">{{ $h->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Floor Name</label>
                    <input name="name" class="input" placeholder="Ground Floor / Floor 1" required>
                </div>
                <div>
                    <label class="label">Floor Number</label>
                    <input name="floor_number" type="number" min="0" value="0" class="input w-24">
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Add Floor</button>
                <button type="button" @click="showForm = false" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>

    {{-- Floor filter --}}
    <form method="GET" class="card py-3">
        <select name="hostel_id" class="select w-52" onchange="this.form.submit()">
            <option value="">All Hostels</option>
            @foreach($hostels as $h)
                <option value="{{ $h->id }}" @selected(request('hostel_id') == $h->id)>{{ $h->name }}</option>
            @endforeach
        </select>
    </form>

    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead><tr>
                    <th class="th">Hostel</th>
                    <th class="th">Floor Name</th>
                    <th class="th text-center">Floor No.</th>
                    <th class="th text-center">Rooms</th>
                    <th class="th text-center">Actions</th>
                </tr></thead>
                <tbody>
                    @forelse($floors as $floor)
                    <tr class="tr">
                        <td class="td text-slate-500">{{ $floor->hostel?->name }}</td>
                        <td class="td font-medium">{{ $floor->name }}</td>
                        <td class="td text-center">{{ $floor->floor_number }}</td>
                        <td class="td text-center">{{ $floor->rooms->count() }}</td>
                        <td class="td text-center">
                            <form method="POST" action="{{ route('hostel.floors.delete', $floor->id) }}"
                                  onsubmit="return confirm('Delete this floor?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="td text-center text-slate-400 py-8">No floors defined yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $floors->links() }}
</div>
@endsection
