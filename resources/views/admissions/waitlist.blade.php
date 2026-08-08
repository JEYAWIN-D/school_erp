@extends('layouts.app')
@section('title', 'Waitlist Management')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="page-title">Waitlist Management</h1>
            <p class="page-subtitle">Manage waitlisted applicants — promote when seats open</p>
        </div>
        <a href="{{ route('admissions.pipeline') }}" class="btn btn-secondary">Back to Pipeline</a>
    </div>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

    @if($waitlisted->isEmpty())
    <div class="card text-center py-12 text-slate-400">
        <svg class="w-10 h-10 mx-auto mb-2 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
        </svg>
        <p>No students on the waitlist for {{ $currentYear?->name ?? 'current year' }}.</p>
    </div>
    @else
    <div class="card">
        <div class="table-wrap">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="th w-16">Position</th>
                        <th class="th">Applicant</th>
                        <th class="th">Parent</th>
                        <th class="th">Class</th>
                        <th class="th">Mobile</th>
                        <th class="th">Source</th>
                        <th class="th text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($waitlisted as $e)
                    <tr class="tr">
                        <td class="td">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-100 text-amber-700 font-bold text-sm">
                                {{ $e->waitlist_position }}
                            </span>
                        </td>
                        <td class="td font-medium">
                            <a href="{{ route('admissions.show', $e->id) }}" class="text-indigo-600 hover:underline">
                                {{ $e->student_name }}
                            </a>
                            <p class="text-xs text-slate-400">{{ $e->enquiry_number }}</p>
                        </td>
                        <td class="td text-sm text-slate-600">{{ $e->parent_name }}</td>
                        <td class="td">{{ $e->class?->name ?? '—' }}</td>
                        <td class="td font-mono text-xs">{{ $e->parent_mobile }}</td>
                        <td class="td capitalize text-xs text-slate-500">{{ str_replace('_', ' ', $e->source ?? '') }}</td>
                        <td class="td text-right">
                            <form method="POST" action="{{ route('admissions.waitlist.promote', $e->id) }}"
                                  onsubmit="return confirm('Promote {{ $e->student_name }} from waitlist to Confirmed?')">
                                @csrf
                                <button type="submit" class="btn btn-sm bg-green-600 text-white hover:bg-green-700">
                                    Promote to Confirmed
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
