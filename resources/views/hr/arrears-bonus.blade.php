@extends('layouts.app')
@section('title', 'Arrears & Bonus')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Arrears & Bonus Addition</h1>
    <a href="{{ route('hr.index') }}" class="btn btn-secondary btn-sm">Back</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Entry Form --}}
    <div class="card space-y-4">
      <h2 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Entry</h2>
      <form method="POST" action="{{ route('hr.arrears-bonus.store') }}" class="space-y-3">
        @csrf
        <div>
          <label class="label text-xs">Pay Month <span class="text-red-500">*</span></label>
          <input type="month" name="month" class="input text-sm" required value="{{ old('month', $month) }}">
        </div>
        <div>
          <label class="label text-xs">Employee <span class="text-red-500">*</span></label>
          <select name="employee_id" class="select text-sm" required>
            <option value="">Select Employee</option>
            @foreach($employees as $emp)
              <option value="{{ $emp->id }}" @selected(old('employee_id') == $emp->id)>{{ $emp->name }} — {{ $emp->department?->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label text-xs">Type <span class="text-red-500">*</span></label>
          <select name="type" class="select text-sm" required>
            <option value="arrears" @selected(old('type') === 'arrears')>Arrears</option>
            <option value="bonus" @selected(old('type') === 'bonus')>Bonus</option>
            <option value="other_addition" @selected(old('type') === 'other_addition')>Other Addition</option>
          </select>
        </div>
        <div>
          <label class="label text-xs">Amount (₹) <span class="text-red-500">*</span></label>
          <input type="number" name="amount" step="0.01" min="0.01" class="input text-sm" required value="{{ old('amount') }}">
        </div>
        <div>
          <label class="label text-xs">Description</label>
          <input type="text" name="description" class="input text-sm" value="{{ old('description') }}" placeholder="e.g. Festival bonus, Salary correction">
        </div>
        <button type="submit" class="btn btn-primary w-full">Save</button>
      </form>
    </div>

    {{-- Entries List --}}
    <div class="lg:col-span-2 card overflow-x-auto">
      <div class="flex items-center justify-between mb-3">
        <div>
          <h2 class="font-semibold text-slate-700">Entries for {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</h2>
          <p class="text-xs text-slate-500 mt-0.5">Total: ₹{{ number_format($totalMonth, 2) }}</p>
        </div>
        <form method="GET" class="flex gap-2">
          <input type="month" name="month" value="{{ $month }}" class="input text-sm">
          <button type="submit" class="btn btn-secondary btn-sm">Go</button>
        </form>
      </div>
      @if($entries->isEmpty())
        <p class="text-slate-400 text-sm text-center py-8">No entries for this month.</p>
      @else
      <table class="table-wrap w-full text-sm">
        <thead>
          <tr>
            <th class="th">Employee</th>
            <th class="th">Type</th>
            <th class="th">Amount</th>
            <th class="th">Description</th>
            <th class="th">Added On</th>
          </tr>
        </thead>
        <tbody>
          @foreach($entries as $entry)
          <tr class="tr">
            <td class="td">
              {{ $entry->employee->name }}<br>
              <span class="text-xs text-slate-400">{{ $entry->employee->department?->name }}</span>
            </td>
            <td class="td">
              <span class="badge-{{ $entry->type === 'arrears' ? 'amber' : ($entry->type === 'bonus' ? 'green' : 'blue') }}">
                {{ ucfirst(str_replace('_', ' ', $entry->type)) }}
              </span>
            </td>
            <td class="td font-semibold text-right text-green-700">₹{{ number_format($entry->amount, 2) }}</td>
            <td class="td text-slate-500 text-xs">{{ $entry->description ?? '—' }}</td>
            <td class="td text-xs text-slate-400">{{ $entry->created_at->format('d M Y') }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr class="bg-slate-50 font-semibold">
            <td colspan="2" class="td text-right">Total:</td>
            <td class="td text-right text-green-700">₹{{ number_format($totalMonth, 2) }}</td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
      </table>
      @endif
    </div>
  </div>
</div>
@endsection
