@extends('layouts.app')
@section('title', 'FeePayment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-16">

  {{-- Top Navigation Bar --}}
  <div class="flex items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <a href="{{ $student ? route('students.show', $student->id) : route('students.index') }}" 
         class="w-10 h-10 rounded-xl bg-white border border-slate-200 shadow-xs hover:bg-slate-50 flex items-center justify-center text-slate-600 transition-all"
         title="Back to Student Profile">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
      </a>
      <div>
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Student Profile / Billing</h2>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">FeePayment</h1>
      </div>
    </div>
  </div>

  {{-- Alert Messages --}}
  @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3 shadow-xs">
      <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <span class="font-medium">{{ session('success') }}</span>
    </div>
  @endif

  @if(isset($errors) && $errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1 shadow-xs">
      <div class="flex items-center gap-2 font-bold text-rose-900">
        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Please review the following errors:</span>
      </div>
      <ul class="list-disc list-inside space-y-1 pl-6 text-xs font-medium">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if($student)
  {{-- Student Information Banner --}}
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-extrabold text-xl shadow-sm shrink-0">
          {{ strtoupper(substr($student->first_name,0,1) . substr($student->last_name,0,1)) }}
        </div>
        <div>
          <div class="flex items-center gap-2.5 flex-wrap">
            <h2 class="text-lg font-bold text-slate-900">{{ $student->full_name }}</h2>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
              {{ ucfirst($student->status) }}
            </span>
          </div>
          <p class="text-xs text-slate-500 font-medium mt-0.5">
            Admission No: <span class="font-mono font-bold text-indigo-600">{{ $student->admission_no ?? $student->admission_number }}</span>
            &bull; Class: <span class="font-bold text-slate-800">{{ $student->currentEnrollment?->class?->name ?? 'Unassigned' }}</span>
            @if($student->currentEnrollment?->section)
              &bull; Sec: <span class="font-bold text-slate-800">{{ $student->currentEnrollment->section->name }}</span>
            @endif
            @if($student->currentEnrollment?->house)
              &bull; House: <span class="font-semibold text-slate-700">{{ $student->currentEnrollment->house }}</span>
            @endif
          </p>
        </div>
      </div>
      <div class="text-left sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Academic Year</p>
        <p class="text-sm font-extrabold text-slate-800">{{ $student->currentEnrollment?->academicYear?->name ?? $currentYear?->name ?? '2025-2026' }}</p>
      </div>
    </div>
  </div>

  @if($dues->count())
  @php
    $duesMap = [];
    foreach($dues as $d) {
      $duesMap[$d->item_key] = [
        'balance'     => (float)$d->balance,
        'name'        => $d->name,
        'term_number' => $d->term_number ?? null,
      ];
    }
  @endphp

  {{-- Alpine JS Helper placed before x-data to guarantee global availability --}}
  <script>
  function feePaymentForm(duesMap) {
    return {
      duesMap: duesMap || {},
      selectedItemKey: '',
      amount: {{ $dues->first()?->balance ?? 0 }},
      discount: 0,
      paymentType: 'single',
      singleMode: 'cash',
      submitting: false,
      splits: [
        { payment_mode: 'cash', amount: {{ $dues->first()?->balance ?? 0 }}, transaction_id: '' },
        { payment_mode: 'upi',  amount: 0, transaction_id: '' }
      ],

      init() {
        const keys = Object.keys(this.duesMap);
        if (keys.length > 0) {
          this.selectedItemKey = keys[0];
          this.handleFeeItemChange();
        }
      },

      handleFeeItemChange() {
        const item = this.duesMap[this.selectedItemKey];
        if (item) {
          this.amount = parseFloat(item.balance) || 0;
          this.discount = 0;
          this.syncSplitAmounts();
        }
      },

      netPayable() {
        const amt = parseFloat(this.amount) || 0;
        const disc = parseFloat(this.discount) || 0;
        return Math.max(0, amt - disc);
      },

      totalSplitAmount() {
        return this.splits.reduce((acc, curr) => acc + (parseFloat(curr.amount) || 0), 0);
      },

      splitDifference() {
        return this.netPayable() - this.totalSplitAmount();
      },

      isSplitValid() {
        if (this.splits.length < 2) return false;
        for (let s of this.splits) {
          if (!s.amount || parseFloat(s.amount) <= 0) return false;
        }
        return Math.abs(this.splitDifference()) < 0.01;
      },

      syncSplitAmounts() {
        const payable = this.netPayable();
        if (payable > 0 && this.splits.length >= 2) {
          if (!this.splits[0].amount || this.splits[0].amount === 0) {
            this.splits[0].amount = payable;
            this.splits[1].amount = 0;
          }
        }
      },

      addSplitRow() {
        this.splits.push({
          payment_mode: 'upi',
          amount: 0,
          transaction_id: ''
        });
      },

      removeSplitRow(index) {
        if (this.splits.length > 2) {
          this.splits.splice(index, 1);
        }
      },

      formatCurrency(val) {
        return Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      },

      handleSubmit(e) {
        const amt = parseFloat(this.amount) || 0;
        if (amt <= 0) {
          e.preventDefault();
          alert('Please enter a valid payment amount greater than 0.');
          return false;
        }

        if (this.paymentType === 'split') {
          if (!this.isSplitValid()) {
            e.preventDefault();
            alert('Payment amounts do not match the selected payment amount.');
            return false;
          }
        }

        if (this.submitting) {
          e.preventDefault();
          return false;
        }

        this.submitting = true;
        return true;
      }
    };
  }
  </script>

  {{-- Fee Payment Card & Form --}}
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8"
       x-data="feePaymentForm({{ json_encode($duesMap) }})"
       x-init="init()">
    <form id="fee-collect-form" 
          method="POST" 
          action="{{ route('fees.collect.save') }}" 
          class="space-y-6" 
          novalidate 
          @submit="handleSubmit($event)">
      @csrf
      <input type="hidden" name="student_id" value="{{ $student->id }}">
      <input type="hidden" name="idempotency_token" value="{{ (string) \Illuminate\Support\Str::uuid() }}">

      {{-- Fee Head / Term Selection --}}
      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
          Fee Head <span class="text-rose-500">*</span>
        </label>
        <select name="fee_item_id" 
                class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-semibold text-slate-900 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                x-model="selectedItemKey"
                @change="handleFeeItemChange()">
          @foreach($dues as $d)
            <option value="{{ $d->item_key }}" {{ old('fee_item_id') === $d->item_key ? 'selected' : '' }}>
              {{ $d->display_label }}
            </option>
          @endforeach
        </select>
        <p class="text-[11px] text-slate-400 mt-1 font-medium">Select the term or fee head to record this collection against.</p>
      </div>

      {{-- Amount, Discount, Payment Date (Late Fee completely removed) --}}
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
            Amount <span class="text-rose-500">*</span>
          </label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-sm">₹</span>
            <input type="number" 
                   name="amount" 
                   x-model.number="amount"
                   @input="syncSplitAmounts()"
                   value="{{ $dues->first()?->balance ?? '' }}"
                   class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold font-mono text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" 
                   min="1" 
                   step="0.01">
          </div>
          <p class="text-[11px] text-slate-400 mt-1">Full or partial amount</p>
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
            Discount
          </label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-sm">₹</span>
            <input type="number" 
                   name="discount" 
                   x-model.number="discount"
                   @input="syncSplitAmounts()"
                   class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-bold font-mono text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" 
                   min="0" 
                   step="0.01" 
                   value="0">
          </div>
          <p class="text-[11px] text-slate-400 mt-1">Concession / Waiver</p>
        </div>

        <div>
          <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
            Payment Date <span class="text-rose-500">*</span>
          </label>
          <input type="date" 
                 name="payment_date" 
                 class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" 
                 value="{{ old('payment_date', today()->toDateString()) }}"
                 max="{{ today()->toDateString() }}">
          <p class="text-[11px] text-slate-400 mt-1">Date payment was received</p>
        </div>
      </div>

      {{-- Payment Mode Selection (Single vs Split) --}}
      <div class="space-y-4 pt-2">
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700">
          Payment Mode <span class="text-rose-500">*</span>
        </label>

        {{-- Mode Selector Pills --}}
        <div class="flex items-center gap-4">
          <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl border cursor-pointer transition select-none text-sm font-bold"
                 :class="paymentType === 'single' ? 'bg-indigo-50 border-indigo-300 text-indigo-900 shadow-xs' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
            <input type="radio" name="payment_type" value="single" x-model="paymentType" class="text-indigo-600 focus:ring-indigo-500">
            <span>Single Payment</span>
          </label>

          <label class="flex items-center gap-2.5 px-4 py-2.5 rounded-xl border cursor-pointer transition select-none text-sm font-bold"
                 :class="paymentType === 'split' ? 'bg-indigo-50 border-indigo-300 text-indigo-900 shadow-xs' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'">
            <input type="radio" name="payment_type" value="split" x-model="paymentType" class="text-indigo-600 focus:ring-indigo-500">
            <span>Split Payment</span>
          </label>
        </div>

        {{-- SINGLE PAYMENT SECTION --}}
        <div x-show="paymentType === 'single'" class="space-y-4 p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Payment Method <span class="text-rose-500">*</span></label>
              <select name="payment_mode" 
                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-semibold text-slate-900 bg-white" 
                      x-model="singleMode"
                      :disabled="paymentType !== 'single'">
                <option value="cash">Cash</option>
                <option value="upi">UPI</option>
                <option value="card">Card / POS</option>
                <option value="bank_transfer">Bank Transfer / NEFT / RTGS</option>
                <option value="cheque">Cheque</option>
                <option value="dd">Demand Draft (DD)</option>
                <option value="online">Online Payment</option>
              </select>
            </div>

            {{-- Reference / UTR for online/UPI/Card --}}
            <div x-show="singleMode === 'upi' || singleMode === 'online' || singleMode === 'card' || singleMode === 'bank_transfer'">
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Transaction ID / UTR</label>
              <input type="text" 
                     name="transaction_id" 
                     class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono text-slate-900 bg-white" 
                     placeholder="e.g. UTR123456789"
                     :disabled="paymentType !== 'single'">
            </div>
          </div>

          {{-- Cheque / DD fields --}}
          <div x-show="singleMode === 'cheque' || singleMode === 'dd'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2 border-t border-slate-200">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Cheque / DD Number</label>
              <input type="text" 
                     name="cheque_number" 
                     class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-mono text-slate-900 bg-white" 
                     placeholder="Number"
                     :disabled="paymentType !== 'single'">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Cheque Date</label>
              <input type="date" 
                     name="cheque_date" 
                     class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 bg-white"
                     :disabled="paymentType !== 'single'">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Bank Name</label>
              <input type="text" 
                     name="cheque_bank" 
                     class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 bg-white" 
                     placeholder="Bank"
                     :disabled="paymentType !== 'single'">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">Branch</label>
              <input type="text" 
                     name="cheque_branch" 
                     class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 bg-white" 
                     placeholder="Branch"
                     :disabled="paymentType !== 'single'">
            </div>
          </div>
        </div>

        {{-- SPLIT PAYMENT SECTION --}}
        <div x-show="paymentType === 'split'" class="space-y-4 p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
          <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <div>
              <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-800">Payment Breakdown</h4>
              <p class="text-[11px] text-slate-500">Divide the payable amount across multiple payment methods.</p>
            </div>
            <button type="button" 
                    @click="addSplitRow()" 
                    class="px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-xs transition">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
              Add Payment Method
            </button>
          </div>

          {{-- Split Rows Table --}}
          <div class="space-y-3">
            <template x-for="(split, index) in splits" :key="index">
              <div class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                {{-- Payment Method --}}
                <div class="w-full sm:w-1/3">
                  <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Method</label>
                  <select :name="'splits[' + index + '][payment_mode]'" 
                          x-model="split.payment_mode" 
                          :disabled="paymentType !== 'split'"
                          class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs font-bold text-slate-800 bg-white">
                    <option value="cash">Cash</option>
                    <option value="upi">UPI</option>
                    <option value="card">Card / POS</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cheque">Cheque</option>
                    <option value="dd">DD</option>
                    <option value="online">Online</option>
                  </select>
                </div>

                {{-- Amount --}}
                <div class="w-full sm:w-1/3">
                  <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Amount (₹)</label>
                  <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">₹</span>
                    <input type="number" 
                           :name="'splits[' + index + '][amount]'" 
                           x-model.number="split.amount" 
                           :disabled="paymentType !== 'split'"
                           min="0.01" 
                           step="0.01" 
                           class="w-full pl-6 pr-3 py-2 rounded-lg border border-slate-300 text-xs font-bold font-mono text-slate-900">
                  </div>
                </div>

                {{-- Reference / Details --}}
                <div class="w-full sm:w-1/3">
                  <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Reference / UTR / Cheque</label>
                  <input type="text" 
                         :name="'splits[' + index + '][transaction_id]'" 
                         x-model="split.transaction_id" 
                         :disabled="paymentType !== 'split'"
                         placeholder="Optional Ref / ID" 
                         class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs text-slate-800">
                </div>

                {{-- Remove Row Button --}}
                <div class="sm:pt-5 shrink-0 flex justify-end">
                  <button type="button" 
                          @click="removeSplitRow(index)" 
                          :disabled="splits.length <= 2"
                          class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition disabled:opacity-30 disabled:cursor-not-allowed"
                          title="Remove payment method">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </div>
              </div>
            </template>
          </div>

          {{-- Split Breakdown Totals & Validation Box --}}
          <div class="mt-4 p-4 rounded-xl bg-white border border-slate-200/90 shadow-xs space-y-2">
            <div class="grid grid-cols-3 gap-3 text-center border-b border-slate-100 pb-3">
              <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Paid</p>
                <p class="text-base font-black font-mono text-indigo-700 mt-0.5">₹<span x-text="formatCurrency(totalSplitAmount())"></span></p>
              </div>
              <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Amount Due</p>
                <p class="text-base font-black font-mono text-slate-800 mt-0.5">₹<span x-text="formatCurrency(netPayable())"></span></p>
              </div>
              <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Difference</p>
                <p class="text-base font-black font-mono mt-0.5" 
                   :class="isSplitValid() ? 'text-emerald-600' : 'text-rose-600'">
                  ₹<span x-text="formatCurrency(Math.abs(splitDifference()))"></span>
                </p>
              </div>
            </div>

            <template x-if="!isSplitValid()">
              <div class="flex items-center gap-2 text-rose-600 text-xs font-bold pt-1">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Payment amounts do not match the selected payment amount.</span>
              </div>
            </template>
            <template x-if="isSplitValid()">
              <div class="flex items-center gap-2 text-emerald-600 text-xs font-bold pt-1">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Split breakdown perfectly matches the payable amount.</span>
              </div>
            </template>
          </div>
        </div>
      </div>

      {{-- Remarks --}}
      <div>
        <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">Remarks</label>
        <textarea name="remarks" 
                  rows="2" 
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition resize-none" 
                  placeholder="Optional payment notes or comments..."></textarea>
      </div>

      {{-- Action Buttons --}}
      <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
        <a href="{{ route('students.show', $student->id) }}" 
           class="px-5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs">
          Cancel
        </a>
        <button type="submit" 
                :disabled="submitting || (paymentType === 'split' && !isSplitValid())"
                class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-bold shadow-md transition flex items-center gap-2">
          <span x-show="submitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
          <span x-text="submitting ? 'Recording Payment...' : 'Record Payment'">Record Payment</span>
        </button>
      </div>
    </form>
  </div>
  @else
  <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center space-y-3">
    <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    </div>
    <h3 class="text-base font-bold text-slate-900">No Outstanding Dues</h3>
    <p class="text-sm text-slate-500">All fees for this student have been fully paid.</p>
    <div class="pt-2">
      <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary btn-sm">Return to Student Profile</a>
    </div>
  </div>
  @endif

  @else
  <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center space-y-4">
    <h3 class="text-lg font-bold text-slate-800">No Student Selected</h3>
    <p class="text-sm text-slate-500">Please navigate from a student's profile to record a payment.</p>
    <div>
      <a href="{{ route('students.index') }}" class="btn btn-primary btn-sm">Go to Students Directory</a>
    </div>
  </div>
  @endif

</div>
@endsection
