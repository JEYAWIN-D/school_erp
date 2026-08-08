@extends('layouts.app')
@section('title', 'Category-wise Fee Variation')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Category-wise Fee Variation</h1>
  <p class="text-slate-500 text-sm">Define different fee amounts per student category (General / SC / ST / OBC / EWS / Minority) for any fee head.</p>

  {{-- Add/Edit variation form --}}
  <div class="card" x-data="{open:false}">
    <button @click="open=!open" class="btn btn-primary btn-sm mb-4">+ Add Category Variation</button>
    <div x-show="open" x-transition>
      <form method="POST" action="{{ route('fees.category-variations.save') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="label">Fee Structure *</label>
            <select name="fee_structure_id" required class="select w-full">
              <option value="">Select Structure</option>
              @foreach($structures as $s)
                <option value="{{ $s->id }}">{{ $s->class?->name }} — {{ $s->feeHead?->name ?? 'N/A' }} ({{ $s->academic_year_id }})</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="label">Fee Head *</label>
            <select name="fee_head_id" required class="select w-full">
              <option value="">Select Head</option>
              @foreach($feeHeads as $h)
                <option value="{{ $h->id }}">{{ $h->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div>
          <label class="label mb-2 block">Amount per Category</label>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach(['General','SC','ST','OBC','EWS','Minority'] as $cat)
            <div>
              <label class="label text-xs">{{ $cat }}</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs">₹</span>
                <input type="number" name="variations[{{ $cat }}]" min="0" step="0.01"
                  class="input pl-6 text-sm" placeholder="0.00">
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <div>
          <label class="label">Remarks</label>
          <input type="text" name="remarks" class="input w-full" placeholder="Optional note">
        </div>
        <div class="flex gap-3">
          <button type="submit" class="btn btn-primary btn-sm">Save Variations</button>
          <button type="button" @click="open=false" class="btn btn-secondary btn-sm">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Existing variations --}}
  @foreach($variations as $structureId => $structureVariations)
  @php $first = $structureVariations->first(); @endphp
  <div class="card">
    <h2 class="font-semibold text-slate-800 mb-3">
      {{ $first->feeStructure?->class?->name }} — {{ $first->feeHead?->name }}
    </h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
      @foreach($structureVariations as $v)
      <div class="bg-slate-50 rounded-lg p-3 text-center">
        <div class="text-xs text-slate-500 mb-1">{{ $v->student_category }}</div>
        <div class="font-bold text-slate-800">₹{{ number_format($v->amount, 2) }}</div>
      </div>
      @endforeach
    </div>
  </div>
  @endforeach

  @if($variations->isEmpty())
  <div class="card text-center py-10 text-slate-400">
    No category-wise fee variations configured yet.
  </div>
  @endif
</div>
@endsection
