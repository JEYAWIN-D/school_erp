@extends('layouts.app')
@section('title','Tally Export')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Tally Export</h1>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  {{-- Ledger Mapping Configuration --}}
  <div class="card">
    <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100 mb-4">Configure Tally Ledger Mapping</h3>
    <p class="text-sm text-slate-500 mb-3">Map each fee head to its corresponding Tally ledger name. Leave blank to use the fee head name as-is.</p>
    <form method="POST" action="{{ route('fees.tally.ledger-map') }}" class="space-y-2">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach($feeHeads as $fh)
        <div class="flex items-center gap-3">
          <span class="text-sm text-slate-600 w-40 shrink-0">{{ $fh->name }}</span>
          <span class="text-slate-300">→</span>
          <input type="text" name="ledger_names[{{ $fh->id }}]" value="{{ $fh->tally_ledger_name }}"
                 class="input text-sm flex-1" placeholder="{{ $fh->name }} (default)">
        </div>
        @endforeach
      </div>
      <div class="pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Save Mapping</button>
      </div>
    </form>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Export Vouchers</h3>
      <form method="GET" action="{{ route('fees.tally.download') }}" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <div><label class="label">From Date <span class="text-red-500">*</span></label>
            <input type="date" name="from_date" class="input" required value="{{ request('from_date') }}">
          </div>
          <div><label class="label">To Date <span class="text-red-500">*</span></label>
            <input type="date" name="to_date" class="input" required value="{{ request('to_date') }}">
          </div>
        </div>
        <div><label class="label">Voucher Type</label>
          <select name="voucher_type" class="select">
            <option value="receipt">Receipt Vouchers</option>
            <option value="payment">Payment Vouchers</option>
            <option value="all">All</option>
          </select>
        </div>
        <div><label class="label">Format</label>
          <select name="format" class="select">
            <option value="xml">XML (Tally Import)</option>
            <option value="csv">CSV</option>
          </select>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800">
          <strong>Note:</strong> Export the XML file and import it into Tally ERP via Gateway of Tally → Import of Data → Vouchers.
        </div>
        <button type="submit" class="btn btn-primary">Export for Tally</button>
      </form>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Export History</h3>
      @forelse($exportLogs as $log)
      <div class="flex items-center justify-between py-3 border-b border-slate-100 last:border-0">
        <div>
          <p class="text-sm font-medium text-slate-800">{{ $log->from_date }} — {{ $log->to_date }}</p>
          <p class="text-xs text-slate-400">{{ $log->records_count }} records | {{ strtoupper($log->format) }} | By {{ $log->user?->name }}</p>
        </div>
        <div class="text-right">
          <p class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</p>
        </div>
      </div>
      @empty
      <p class="text-slate-400 text-sm text-center py-6">No export history.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
