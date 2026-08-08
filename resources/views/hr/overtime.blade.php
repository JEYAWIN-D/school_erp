@extends('layouts.app')
@section('title', 'Overtime Management')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Overtime Entry</h1>
    <a href="{{ route('hr.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Entry Form --}}
    <div class="card space-y-4">
      <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Overtime Entry</h2>
      <form method="POST" action="{{ route('hr.overtime.store') }}" class="space-y-3">
        @csrf
        <div>
          <label class="label text-xs">Month</label>
          <input type="month" name="month_filter" value="{{ $month }}" class="input text-sm"
                 onchange="this.form.action='{{ route('hr.overtime') }}?month='+this.value; this.form.submit();">
        </div>
        <div>
          <label class="label text-xs">Employee <span class="text-red-500">*</span></label>
          <select name="employee_id" class="select text-sm" required>
            <option value="">Select Employee</option>
            @foreach($employees as $emp)
              <option value="{{ $emp->id }}">{{ $emp->name }} — {{ $emp->department?->name }}</option>
            @endforeach
          </select>
          @error('employee_id')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="label text-xs">Date <span class="text-red-500">*</span></label>
          <input type="date" name="entry_date" class="input text-sm" required value="{{ old('entry_date') }}">
        </div>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="label text-xs">Hours <span class="text-red-500">*</span></label>
            <input type="number" name="hours" step="0.5" min="0.5" max="24" class="input text-sm" required value="{{ old('hours') }}">
          </div>
          <div>
            <label class="label text-xs">Rate/Hour (₹) <span class="text-red-500">*</span></label>
            <input type="number" name="rate_per_hour" step="0.01" min="0" class="input text-sm" required value="{{ old('rate_per_hour') }}">
          </div>
        </div>
        <div>
          <label class="label text-xs">Remarks</label>
          <input type="text" name="remarks" class="input text-sm" value="{{ old('remarks') }}" placeholder="Optional note">
        </div>
        <input type="hidden" name="redirect_month" value="{{ $month }}">
        <button type="submit" class="btn btn-primary w-full">Save Entry</button>
      </form>
    </div>

    {{-- Month Entries --}}
    <div class="lg:col-span-2 card overflow-x-auto">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-slate-700">Entries for {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</h2>
        <form method="GET" class="flex gap-2">
          <input type="month" name="month" value="{{ $month }}" class="input text-sm">
          <button type="submit" class="btn btn-secondary btn-sm">Go</button>
        </form>
      </div>
      @if($entries->isEmpty())
        <p class="text-slate-400 text-sm text-center py-8">No overtime entries for this month.</p>
      @else
      <table class="table-wrap w-full text-sm">
        <thead>
          <tr>
            <th class="th">Employee</th>
            <th class="th">Date</th>
            <th class="th">Hours</th>
            <th class="th">Rate</th>
            <th class="th">Amount</th>
            <th class="th">Remarks</th>
          </tr>
        </thead>
        <tbody>
          @foreach($entries as $entry)
          <tr class="tr">
            <td class="td">
              {{ $entry->employee->name }}<br>
              <span class="text-xs text-slate-400">{{ $entry->employee->department?->name }}</span>
            </td>
            <td class="td">{{ $entry->entry_date->format('d M Y') }}</td>
            <td class="td text-center">{{ $entry->hours }}</td>
            <td class="td text-right">₹{{ number_format($entry->rate_per_hour, 2) }}</td>
            <td class="td text-right font-semibold text-indigo-700">₹{{ number_format($entry->amount, 2) }}</td>
            <td class="td text-slate-500 text-xs">{{ $entry->remarks ?? '—' }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr class="bg-slate-50 font-semibold">
            <td colspan="4" class="td text-right">Total:</td>
            <td class="td text-right text-indigo-700">₹{{ number_format($entries->sum('amount'), 2) }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
      @endif
    </div>
  </div>
</div>
@endsection
