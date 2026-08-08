@extends('layouts.admin')
@section('title', 'Bus Attendants')
@section('content')
<div class="space-y-6" x-data="{ showForm: false, editId: null, editData: {} }">
    <div class="flex justify-between items-center">
        <h1 class="page-title">Bus Attendants</h1>
        <button @click="showForm = !showForm" class="btn-primary">+ Add Attendant</button>
    </div>

    <div x-show="showForm" x-transition class="card">
        <h2 class="font-semibold text-gray-700 mb-4">Add New Attendant</h2>
        <form method="POST" action="{{ route('transport.attendants.store') }}" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @csrf
            <div>
                <label class="label">Full Name *</label>
                <input type="text" name="name" class="input w-full" required>
            </div>
            <div>
                <label class="label">Mobile</label>
                <input type="text" name="mobile" class="input w-full" maxlength="15">
            </div>
            <div>
                <label class="label">Aadhaar Number</label>
                <input type="text" name="aadhaar" class="input w-full" maxlength="12">
            </div>
            <div>
                <label class="label">Photo</label>
                <input type="file" name="photo" class="input w-full" accept="image/*">
            </div>
            <div>
                <label class="label">Assign to Vehicle</label>
                <select name="vehicle_id" class="select w-full">
                    <option value="">— None —</option>
                    @foreach($vehicles as $v)
                        <option value="{{ $v->id }}">{{ $v->vehicle_number }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Assign to Route</label>
                <select name="route_id" class="select w-full">
                    <option value="">— None —</option>
                    @foreach($routes as $r)
                        <option value="{{ $r->id }}">{{ $r->route_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 flex gap-3 justify-end">
                <button type="button" @click="showForm = false" class="btn-secondary">Cancel</button>
                <button class="btn-primary">Save Attendant</button>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <form method="GET" class="flex gap-3 items-end">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name…" class="input flex-1">
            <button class="btn-primary">Search</button>
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Name</th>
                        <th class="th">Mobile</th>
                        <th class="th">Aadhaar</th>
                        <th class="th">Vehicle</th>
                        <th class="th">Route</th>
                        <th class="th text-center">Status</th>
                        <th class="th text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendants as $a)
                    <tr class="tr">
                        <td class="td">
                            <div class="flex items-center gap-2">
                                @if($a->photo)
                                <img src="{{ asset('storage/' . $a->photo) }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">{{ strtoupper(substr($a->name, 0, 1)) }}</div>
                                @endif
                                <span class="font-medium">{{ $a->name }}</span>
                            </div>
                        </td>
                        <td class="td">{{ $a->mobile ?? '—' }}</td>
                        <td class="td text-sm text-gray-500">{{ $a->aadhaar ? '****' . substr($a->aadhaar, -4) : '—' }}</td>
                        <td class="td">{{ $a->vehicle?->vehicle_number ?? '—' }}</td>
                        <td class="td">{{ $a->route?->route_name ?? '—' }}</td>
                        <td class="td text-center">
                            <span class="badge-{{ $a->is_active ? 'success' : 'secondary' }}">
                                {{ $a->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="td text-center">
                            <form method="POST" action="{{ route('transport.attendants.delete', $a->id) }}"
                                  onsubmit="return confirm('Remove attendant?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-500 text-sm hover:underline">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="td text-center text-gray-400">No attendants found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $attendants->links() }}
</div>
@endsection
