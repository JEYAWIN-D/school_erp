@extends('layouts.admin')
@section('title', 'Fee Revision')
@section('content')
<div class="space-y-6">
    <h1 class="page-title">Fee Revision</h1>
    <p class="text-sm text-slate-500">Update fee amounts for a class mid-year. Changes affect future invoices.</p>

    @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert-error">{{ session('error') }}</div>@endif

    {{-- Class selector --}}
    <form method="GET" class="card py-3">
        <div class="flex gap-3">
            <select name="class_id" class="select w-48" onchange="this.form.submit()">
                <option value="">Select Class</option>
                @foreach($classes as $cls)
                    <option value="{{ $cls->id }}" @selected(request('class_id') == $cls->id)>{{ $cls->name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    @if($structures->count())
    <form method="POST" action="{{ route('fees.revision.save') }}">
        @csrf
        <input type="hidden" name="class_id" value="{{ request('class_id') }}">
        <div class="card p-0 overflow-hidden">
            <div class="p-4 bg-amber-50 border-b border-amber-100 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/></svg>
                <span class="text-sm text-amber-700 font-medium">Revising fee amounts will update the fee structure. Existing payments are not affected.</span>
            </div>
            <div class="table-wrap">
                <table>
                    <thead><tr>
                        <th class="th">Fee Head</th>
                        <th class="th text-right">Current Amount (₹)</th>
                        <th class="th text-right">Revised Amount (₹)</th>
                    </tr></thead>
                    <tbody>
                        @foreach($structures as $structure)
                        <tr class="tr">
                            <td class="td font-medium">{{ $structure->head?->name ?? 'N/A' }}</td>
                            <td class="td text-right text-slate-500">{{ number_format($structure->amount, 2) }}</td>
                            <td class="td text-right">
                                <input type="number" name="amounts[{{ $structure->id }}]"
                                    value="{{ $structure->amount }}" min="0" step="0.01"
                                    class="input text-right w-36">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="flex justify-end mt-4">
            <button type="submit" class="btn btn-primary">Save Revised Amounts</button>
        </div>
    </form>
    @elseif(request('class_id'))
    <div class="card text-center py-10 text-slate-400">No fee structure found for this class in the current year.</div>
    @endif
</div>
@endsection
