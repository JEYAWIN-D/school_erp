@extends('layouts.app')

@section('title', 'Standard-wise Admission Kit Configuration — Warehouse')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

  {{-- Top Navigation Header --}}
  <div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ route('warehouse.index') }}" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition border border-slate-200">
        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Standard-wise Admission Kit Configuration</h1>
        <p class="text-xs text-slate-500 font-medium">Configure items automatically mapped to student admissions per Class / Standard</p>
      </div>
    </div>
  </div>

  {{-- Class Selector Header Card --}}
  <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs">
    <form method="GET" action="{{ route('warehouse.admission-kit-config') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="w-full sm:w-80">
        <label class="block text-xs font-bold text-slate-700 mb-1">Select Class / Standard</label>
        <select name="class_id" onchange="this.form.submit()" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
          @foreach($classes as $cls)
            <option value="{{ $cls->id }}" {{ ($selectedClass && $selectedClass->id == $cls->id) ? 'selected' : '' }}>
              {{ $cls->name }} (Class ID: {{ $cls->id }})
            </option>
          @endforeach
        </select>
      </div>

      <div class="text-xs text-slate-500 font-medium sm:text-right">
        <span class="font-extrabold text-slate-900 block">{{ $currentKits ? $currentKits->count() : 0 }} Kit Items Mapped</span>
        <span>Items configured below are automatically reserved & deducted upon new admission.</span>
      </div>
    </form>
  </div>

  {{-- Kit Items Mapping Form --}}
  <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
      <h2 class="text-base font-extrabold text-slate-900">
        Kit Items for {{ $selectedClass?->name ?? 'Class' }}
      </h2>
      <span class="text-xs text-slate-500 font-medium">Select Academic Inventory Items Only</span>
    </div>

    <form method="POST" action="{{ route('warehouse.admission-kit-config.save') }}">
      @csrf
      <input type="hidden" name="class_id" value="{{ $selectedClass?->id }}">

      <div class="p-6 space-y-4">
        @if($currentKits && $currentKits->isEmpty())
          <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-800 text-xs font-medium flex items-center gap-2.5">
            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>No admission kit has been configured for {{ $selectedClass?->name ?? 'this class' }} yet. Select items below and click Save.</span>
          </div>
        @endif

        @if($academicItems->isEmpty())
          <div class="p-6 text-center text-slate-400">
            <p class="text-sm font-bold">No Academic Inventory items found in database.</p>
            <p class="text-xs mt-1">Please add Academic Inventory items in the Warehouse dashboard first.</p>
          </div>
        @else
          <div class="grid grid-cols-1 gap-4">
            @foreach($academicItems as $item)
              @php
                $existing = $currentKits->firstWhere('item_id', $item->id);
                $isIncluded = $existing !== null;
              @endphp
              <div class="p-4 rounded-2xl border transition flex flex-col md:flex-row md:items-center justify-between gap-4 {{ $isIncluded ? 'border-blue-200 bg-blue-50/30' : 'border-slate-200 hover:border-slate-300' }}"
                   x-data="{ enabled: {{ $isIncluded ? 'true' : 'false' }} }">
                
                {{-- Left: Item Info & Checkbox --}}
                <div class="flex items-start gap-3">
                  <input type="checkbox" name="items[{{ $item->id }}][enabled]" value="1"
                         x-model="enabled"
                         class="mt-1 w-4 h-4 text-blue-600 rounded-md border-slate-300 focus:ring-blue-500 cursor-pointer">
                  <div>
                    <label class="font-extrabold text-slate-900 text-sm cursor-pointer" @click="enabled = !enabled">
                      {{ $item->name }}
                    </label>
                    <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-500">
                      <span class="font-mono text-slate-400">SKU: {{ $item->item_code }}</span>
                      <span>&bull;</span>
                      <span>Cat: {{ $item->category?->name ?? 'Academic' }}</span>
                      <span>&bull;</span>
                      <span class="font-bold font-mono text-slate-700">Available Stock: {{ $item->current_stock }} {{ $item->unit }}</span>
                    </div>
                  </div>
                </div>

                {{-- Right: Quantity & Pricing Controls --}}
                <div class="flex items-center gap-3 flex-wrap sm:flex-nowrap" x-show="enabled" x-collapse>
                  <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Default Qty</label>
                    <input type="number" name="items[{{ $item->id }}][default_quantity]" min="1"
                           value="{{ $existing ? $existing->default_quantity : 1 }}"
                           class="w-20 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold font-mono text-slate-900">
                  </div>

                  <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Student Price (₹)</label>
                    <input type="number" step="0.01" name="items[{{ $item->id }}][student_charge]" min="0"
                           value="{{ $existing ? $existing->student_charge : $item->student_price }}"
                           class="w-28 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold font-mono text-blue-700">
                  </div>

                  <div class="pt-4 flex items-center gap-2">
                    <label class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 cursor-pointer">
                      <input type="checkbox" name="items[{{ $item->id }}][allow_additional_qty]" value="1"
                             {{ ($existing && $existing->allow_additional_qty) || in_array(strtolower($item->name), ['notebook', 'uniform', 'socks', 'dress']) ? 'checked' : '' }}
                             class="w-3.5 h-3.5 text-blue-600 rounded-md border-slate-300">
                      <span>Allow Extra Paid Purchase</span>
                    </label>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
        <a href="{{ route('warehouse.index') }}" class="btn btn-secondary text-xs font-bold">Cancel</a>
        <button type="submit" class="btn btn-primary text-xs font-bold">Save Admission Kit Config for {{ $selectedClass->name }}</button>
      </div>
    </form>
  </div>

</div>
@endsection
