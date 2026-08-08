@extends('layouts.app')
@section('title','Employee Loan Management')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Employee Loans</h1>
    <button x-data @click="$dispatch('open-modal','add-loan')" class="btn btn-primary btn-sm">+ New Loan</button>
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Employee</label>
        <select name="employee_id" class="select w-44">
          <option value="">All Employees</option>
          @foreach($employees as $emp)
          <option value="{{ $emp->id }}" @selected(request('employee_id')==$emp->id)>{{ $emp->full_name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label text-xs">Status</label>
        <select name="status" class="select w-32">
          <option value="">All</option>
          <option value="active" @selected(request('status')==='active')>Active</option>
          <option value="closed" @selected(request('status')==='closed')>Closed</option>
          <option value="paused" @selected(request('status')==='paused')>Paused</option>
        </select>
      </div>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        @foreach(['Employee','Type','Principal','EMI','Paid','Outstanding','Status','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($loans as $loan)
        <tr class="hover:bg-slate-50" x-data="{payment:false}">
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800">{{ $loan->employee?->full_name }}</p>
            <p class="text-xs text-slate-400">{{ $loan->employee?->department?->name }}</p>
          </td>
          <td class="px-4 py-3 capitalize text-slate-600">{{ str_replace('_',' ',$loan->loan_type) }}</td>
          <td class="px-4 py-3">₹{{ number_format($loan->principal_amount, 0) }}</td>
          <td class="px-4 py-3">
            ₹{{ number_format($loan->emi_amount, 0) }}/mo
            <div class="text-xs text-slate-400">{{ $loan->emi_months }} months</div>
          </td>
          <td class="px-4 py-3 text-green-600">₹{{ number_format($loan->total_paid, 0) }}</td>
          <td class="px-4 py-3">
            <span class="{{ $loan->outstanding_balance > 0 ? 'text-red-600 font-semibold' : 'text-slate-400' }}">
              ₹{{ number_format($loan->outstanding_balance, 0) }}
            </span>
            @if($loan->principal_amount > 0)
            <div class="w-24 h-1.5 bg-slate-200 rounded mt-1">
              <div class="h-1.5 bg-green-500 rounded" style="width:{{ min(100, round($loan->total_paid/$loan->principal_amount*100)) }}%"></div>
            </div>
            @endif
          </td>
          <td class="px-4 py-3">
            <span class="badge-{{ $loan->status==='active' ? 'green' : ($loan->status==='closed' ? 'slate' : 'amber') }} text-xs capitalize">
              {{ $loan->status }}
            </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap gap-1">
              @if($loan->status === 'active')
              <button @click="payment=!payment" class="btn btn-secondary btn-xs">Record Payment</button>
              <form method="POST" action="{{ route('hr.loans.close', $loan->id) }}" class="inline" onsubmit="return confirm('Mark loan as closed?')">
                @csrf <button class="btn btn-ghost btn-xs text-red-500">Close</button>
              </form>
              @endif
            </div>
            <div x-show="payment" x-transition class="mt-2">
              <form method="POST" action="{{ route('hr.loans.payment', $loan->id) }}" class="flex gap-2 items-center">
                @csrf
                <input type="number" name="amount" class="input py-1 w-28 text-sm" placeholder="Amount" min="1" max="{{ $loan->outstanding_balance }}" step="0.01">
                <button type="submit" class="btn btn-primary btn-xs">Pay</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No loans found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($loans->hasPages())
    <div class="px-4 pb-3">{{ $loans->links() }}</div>
    @endif
  </div>
</div>

{{-- New Loan Modal --}}
<div x-data="{open:false}" @open-modal.window="if($event.detail==='add-loan')open=true"
  x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
  <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
  <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6 z-10 space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">New Employee Loan</h3>
      <button @click="open=false" class="text-slate-400 hover:text-slate-600 text-xl">&times;</button>
    </div>
    <form method="POST" action="{{ route('hr.loans.store') }}" class="space-y-4">
      @csrf
      <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="label">Employee <span class="text-red-500">*</span></label>
          <select name="employee_id" class="select" required>
            <option value="">Select Employee</option>
            @foreach($employees as $emp)
            <option value="{{ $emp->id }}">{{ $emp->full_name }} — {{ $emp->department?->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Loan Type</label>
          <select name="loan_type" class="select">
            @foreach(['personal'=>'Personal','vehicle'=>'Vehicle','house'=>'House','education'=>'Education','emergency'=>'Emergency'] as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Principal Amount <span class="text-red-500">*</span></label>
          <input type="number" name="principal_amount" class="input" min="1" step="0.01" required>
        </div>
        <div>
          <label class="label">Interest Rate % (annual)</label>
          <input type="number" name="interest_rate" class="input" min="0" max="100" step="0.01" placeholder="0 = interest-free">
        </div>
        <div>
          <label class="label">EMI Months <span class="text-red-500">*</span></label>
          <input type="number" name="emi_months" class="input" min="1" required placeholder="e.g. 12">
        </div>
        <div>
          <label class="label">Disbursement Date</label>
          <input type="date" name="disbursement_date" class="input" value="{{ today()->toDateString() }}">
        </div>
        <div>
          <label class="label">First EMI Month</label>
          <input type="date" name="emi_start_month" class="input" value="{{ today()->addMonth()->startOfMonth()->toDateString() }}">
        </div>
        <div class="col-span-2">
          <label class="label">Purpose</label>
          <input type="text" name="purpose" class="input" placeholder="Reason for loan">
        </div>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" @click="open=false" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Create Loan</button>
      </div>
    </form>
  </div>
</div>
@endsection
