@extends('layouts.admin')
@section('title', 'Terms & Semesters')
@section('content')
<div class="space-y-6" x-data="{ showForm: false, editId: null, editData: {} }">
    <div class="flex items-center justify-between">
        <h1 class="page-title">Terms & Semesters</h1>
        <button @click="showForm = !showForm" class="btn btn-primary btn-sm">+ Add Term</button>
    </div>

    {{-- Year filter --}}
    <form method="GET" class="card py-3">
        <div class="flex gap-3 items-center">
            <select name="academic_year_id" class="select w-52" onchange="this.form.submit()">
                @foreach($academicYears as $yr)
                    <option value="{{ $yr->id }}" @selected($selectedYear?->id == $yr->id)>{{ $yr->name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Add form --}}
    <div x-show="showForm" x-transition class="card">
        <h2 class="font-semibold text-slate-700 mb-4">New Term / Semester</h2>
        <form method="POST" action="{{ route('academics.terms.store') }}">
            @csrf
            <input type="hidden" name="academic_year_id" value="{{ $selectedYear?->id }}">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="label">Name</label>
                    <input name="name" class="input" placeholder="Term 1 / Semester 1" required>
                </div>
                <div>
                    <label class="label">Type</label>
                    <select name="type" class="select">
                        <option value="term">Term</option>
                        <option value="semester">Semester</option>
                        <option value="quarter">Quarter</option>
                    </select>
                </div>
                <div>
                    <label class="label">Order</label>
                    <input name="order_position" type="number" min="1" value="1" class="input w-24">
                </div>
                <div>
                    <label class="label">Start Date</label>
                    <input name="start_date" type="date" class="input" required>
                </div>
                <div>
                    <label class="label">End Date</label>
                    <input name="end_date" type="date" class="input" required>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" @click="showForm = false" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    @forelse($terms as $term)
    <div class="card flex items-center justify-between">
        <div>
            <p class="font-semibold text-slate-800">{{ $term->name }}</p>
            <p class="text-sm text-slate-500 capitalize">{{ $term->type }} &bull; Order {{ $term->order_position }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $term->start_date->format('d M Y') }} — {{ $term->end_date->format('d M Y') }}</p>
        </div>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('academics.terms.update', $term->id) }}">
                @csrf @method('PUT')
                <button type="button" x-data @click="$dispatch('open-edit-{{ $term->id }}')" class="btn btn-ghost btn-sm text-blue-600">Edit</button>
            </form>
            <form method="POST" action="{{ route('academics.terms.delete', $term->id) }}" onsubmit="return confirm('Delete this term?')">
                @csrf @method('DELETE')
                <button class="btn btn-ghost btn-sm text-red-500">Delete</button>
            </form>
        </div>
    </div>
    @empty
    <div class="card text-center py-10 text-slate-400">No terms defined for {{ $selectedYear?->name ?? 'this year' }}. Add one above.</div>
    @endforelse
</div>
@endsection
