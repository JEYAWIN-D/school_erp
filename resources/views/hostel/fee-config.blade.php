@extends('layouts.app')
@section('title','Hostel Fee Configuration')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Hostel Fee Configuration</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <form method="POST" action="{{ route('hostel.fee.store') }}" class="card space-y-4">
      @csrf
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Add Fee Structure</h3>
      <div><label class="label">Hostel <span class="text-red-500">*</span></label>
        <select name="hostel_id" class="select" required>
          @foreach($hostels as $h)<option value="{{ $h->id }}">{{ $h->name }}</option>@endforeach
        </select>
      </div>
      <div><label class="label">Room Type <span class="text-red-500">*</span></label>
        <select name="room_type" class="select" required>
          @foreach(['single'=>'Single Occupancy','double'=>'Double Occupancy','triple'=>'Triple Occupancy','dormitory'=>'Dormitory'] as $k=>$v)
          <option value="{{ $k }}">{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Monthly Fee (₹) <span class="text-red-500">*</span></label>
          <input type="number" name="monthly_fee" class="input" required step="0.01" min="0">
        </div>
        <div><label class="label">Admission Fee (₹)</label>
          <input type="number" name="admission_fee" class="input" step="0.01" min="0">
        </div>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Mess Fee (₹/month)</label>
          <input type="number" name="mess_fee" class="input" step="0.01" min="0">
        </div>
        <div><label class="label">Security Deposit (₹)</label>
          <input type="number" name="security_deposit" class="input" step="0.01" min="0">
        </div>
      </div>
      <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-lg p-3">
        <input type="checkbox" name="mess_included_default" value="1" id="mess_included_default" checked class="w-4 h-4 text-amber-600 rounded mt-0.5">
        <div>
          <label for="mess_included_default" class="text-sm font-medium text-slate-700">Include Mess Fee by Default</label>
          <p class="text-xs text-slate-500 mt-0.5">When checked, new allotments for this fee structure will include mess fee automatically. Can be overridden per student at allotment time.</p>
        </div>
      </div>
      <div><label class="label">Academic Year</label>
        <select name="academic_year_id" class="select">
          @foreach($academicYears as $y)<option value="{{ $y->id }}" @selected($y->is_current)>{{ $y->name }}</option>@endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Save Fee Structure</button>
    </form>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Current Fee Structures</h3>
      @forelse($feeStructures as $fs)
      <div class="py-3 border-b border-slate-100 last:border-0">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-semibold text-slate-800 text-sm">{{ $fs->hostel?->name }} — {{ ucfirst($fs->room_type) }}</p>
            <p class="text-xs text-slate-400">{{ $fs->academicYear?->name }}</p>
          </div>
          <div class="text-right">
            <p class="font-bold text-slate-800">₹{{ number_format($fs->monthly_fee,0) }}/mo</p>
            @if($fs->mess_fee)
            <p class="text-xs text-slate-400">
              +₹{{ number_format($fs->mess_fee,0) }} mess
              @if(isset($fs->mess_included_default) && !$fs->mess_included_default)
                <span class="text-amber-500">(optional)</span>
              @endif
            </p>
          @endif
          </div>
        </div>
        <div class="flex gap-4 mt-1 text-xs text-slate-400">
          @if($fs->admission_fee)<span>Admission: ₹{{ number_format($fs->admission_fee,0) }}</span>@endif
          @if($fs->security_deposit)<span>Deposit: ₹{{ number_format($fs->security_deposit,0) }}</span>@endif
        </div>
      </div>
      @empty
      <p class="text-slate-400 text-sm text-center py-6">No fee structures.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
