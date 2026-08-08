@extends('layouts.app')
@section('title', 'Library Fine Defaulters')
@section('content')
<div class="space-y-6">
    <h1 class="page-title">Library Fine Defaulters</h1>

    @if($defaulters->count())
    <div class="card p-0 overflow-hidden">
        <div class="p-3 bg-red-50 border-b border-red-100 flex items-center gap-2">
            <span class="text-red-600 font-medium">{{ $defaulters->total() }} records with unpaid fine</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th class="th">Student</th>
                        <th class="th">Book</th>
                        <th class="th">Issue Date</th>
                        <th class="th">Due Date</th>
                        <th class="th">Return Date</th>
                        <th class="th text-right">Fine (₹)</th>
                        <th class="th text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($defaulters as $issue)
                    <tr class="tr">
                        <td class="td">
                            <div class="font-medium text-gray-800">{{ $issue->student?->full_name }}</div>
                            <div class="text-xs text-gray-500">Adm# {{ $issue->student?->admission_number }}</div>
                        </td>
                        <td class="td">
                            <div class="font-medium">{{ $issue->book?->title }}</div>
                            <div class="text-xs text-gray-500">{{ $issue->book?->author }}</div>
                        </td>
                        <td class="td">{{ $issue->issue_date?->format('d M Y') }}</td>
                        <td class="td">{{ $issue->due_date?->format('d M Y') }}</td>
                        <td class="td">{{ $issue->return_date?->format('d M Y') ?? '—' }}</td>
                        <td class="td text-right font-semibold text-red-600">₹{{ number_format($issue->fine_amount, 2) }}</td>
                        <td class="td text-center">
                            <form method="POST" action="{{ route('library.fine.waive', $issue->id) }}" class="inline"
                                  onsubmit="return confirm('Waive fine for this record?')">
                                @csrf
                                <input type="hidden" name="reason" value="Admin waiver">
                                <button class="text-xs text-blue-600 hover:underline">Waive</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{ $defaulters->links() }}
    @else
    <div class="alert-success">No outstanding library fines.</div>
    @endif
</div>
@endsection
