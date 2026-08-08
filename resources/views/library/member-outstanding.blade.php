@extends('layouts.app')
@section('title', 'Fine Outstanding')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('library.members') }}" class="btn-icon">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="page-title">Fine Outstanding — {{ $student->full_name }}</h1>
    </div>

    <div class="card flex items-center gap-4 bg-red-50 border border-red-200">
        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-red-600">₹{{ number_format($totalFine, 2) }}</p>
            <p class="text-sm text-red-400">Total unpaid fine</p>
        </div>
    </div>

    @forelse($issues as $issue)
    <div class="card">
        <div class="flex justify-between items-start">
            <div>
                <p class="font-semibold text-slate-800">{{ $issue->book?->title }}</p>
                <p class="text-sm text-slate-400">{{ $issue->book?->author }}</p>
            </div>
            <span class="text-red-600 font-bold">₹{{ number_format($issue->fine_amount, 2) }}</span>
        </div>
        <div class="mt-2 flex gap-4 text-xs text-slate-500">
            <span>Due: {{ $issue->due_date?->format('d M Y') }}</span>
            <span>Returned: {{ $issue->return_date?->format('d M Y') ?? '—' }}</span>
        </div>
        <div class="mt-3">
            <form method="POST" action="{{ route('library.fine.waive', $issue->id) }}" class="inline"
                  onsubmit="return confirm('Waive this fine?')">
                @csrf
                <input type="hidden" name="reason" value="Member request">
                <button class="btn btn-ghost btn-sm text-blue-600">Waive Fine</button>
            </form>
        </div>
    </div>
    @empty
    <div class="card text-center py-8 text-slate-400">No outstanding fines for this member.</div>
    @endforelse
</div>
@endsection
