@extends('layouts.app')
@section('title','Late Fee Rules')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Late Fee Rules</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="{{ route('fees.late-fee.store') }}" class="card space-y-4">
      @csrf
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Rule</h3>
      <div><label class="label">Rule Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" required value="{{ old('name') }}" placeholder="e.g. Default Late Fee">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Type <span class="text-red-500">*</span></label>
          <select name="type" class="select" required>
            <option value="flat">Flat Amount</option>
            <option value="per_day">Per Day</option>
            <option value="percentage">Percentage</option>
          </select>
        </div>
        <div><label class="label">Amount / Rate <span class="text-red-500">*</span></label>
          <input type="number" name="amount" class="input" required step="0.01" min="0" value="{{ old('amount') }}">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Grace Days</label>
          <input type="number" name="grace_days" class="input" value="{{ old('grace_days',0) }}" min="0">
        </div>
        <div><label class="label">Max Late Fee</label>
          <input type="number" name="max_amount" class="input" step="0.01" min="0" value="{{ old('max_amount') }}" placeholder="Leave blank = no cap">
        </div>
      </div>
      <div><label class="label">Applicable Fee Heads</label>
        <div class="grid grid-cols-2 gap-2 border border-slate-100 rounded p-2">
          @foreach($feeHeads as $fh)
          <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="fee_head_ids[]" value="{{ $fh->id }}"> {{ $fh->name }}</label>
          @endforeach
        </div>
      </div>
      <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" id="lf_active" checked>
        <label for="lf_active" class="text-sm text-slate-600">Active</label>
      </div>
      <button type="submit" class="btn btn-primary">Save Rule</button>
    </form>
    <div class="card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Existing Rules</h3>
      @forelse($rules as $rule)
      <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
        <div>
          <p class="font-semibold text-slate-800 text-sm">{{ $rule->name }}</p>
          <p class="text-xs text-slate-400">
            {{ ucfirst($rule->type) }}: ₹{{ number_format($rule->amount,2) }}
            @if($rule->grace_days) | Grace: {{ $rule->grace_days }}d @endif
            @if($rule->max_amount) | Max: ₹{{ number_format($rule->max_amount,2) }} @endif
          </p>
        </div>
        <div class="flex items-center gap-2">
          <span class="{{ $rule->is_active ? 'badge-green' : 'badge-slate' }} text-xs">{{ $rule->is_active ? 'Active' : 'Off' }}</span>
          <form method="POST" action="{{ route('fees.late-fee.delete',$rule->id) }}">@csrf @method('DELETE')
            <button type="submit" class="text-red-400 hover:text-red-600 text-xs">Delete</button>
          </form>
        </div>
      </div>
      @empty
      <p class="text-slate-400 text-sm text-center py-6">No late fee rules.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
