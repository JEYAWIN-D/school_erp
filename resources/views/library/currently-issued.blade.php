@extends('layouts.app')
@section('title', 'Currently Issued Books')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Currently Issued Books</h1>
      <p class="page-subtitle">All books currently checked out from the library</p>
    </div>
    <a href="{{ route('library.index') }}" class="btn btn-secondary">Back</a>
  </div>

  {{-- Quick Return by Barcode/QR scan --}}
  <div class="card" x-data="returnScanner()" x-init="init()">
    <div class="flex items-center justify-between mb-2">
      <h2 class="text-sm font-semibold text-slate-700">Quick Return via Barcode Scan</h2>
      <button type="button" @click="toggleScanner()" class="btn-sm btn-secondary" x-text="scanning ? 'Stop Scanner' : 'Scan to Return'"></button>
    </div>
    <div x-show="scanning" x-transition class="space-y-2">
      <div class="relative bg-black rounded-lg overflow-hidden" style="max-height:220px;">
        <video id="return-video" class="w-full" autoplay playsinline></video>
        <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
          <div class="border-2 border-yellow-400 w-48 h-20 rounded opacity-80"></div>
        </div>
      </div>
    </div>
    <div class="flex gap-2 mt-3">
      <input type="text" x-model="returnCode" @keydown.enter.prevent="lookupReturn(returnCode)"
        placeholder="Scan or type accession/ISBN to look up issued copy" class="input flex-1 font-mono text-sm">
      <button type="button" @click="lookupReturn(returnCode)" class="btn-sm btn-primary">Find</button>
    </div>
    <div x-show="returnResult" x-transition class="mt-2 bg-blue-50 border border-blue-200 rounded px-3 py-2 text-sm" x-html="returnResult"></div>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Search Student</label>
        <input type="text" name="search" class="input" placeholder="Student name..." value="{{ request('search') }}">
      </div>
      <div class="flex items-end gap-2">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="overdue_only" value="1" {{ request('overdue_only') ? 'checked' : '' }} class="rounded text-red-500">
          <span class="text-sm font-medium text-slate-700">Overdue Only</span>
        </label>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Book</th>
            <th class="th">Student</th>
            <th class="th">Issue Date</th>
            <th class="th">Due Date</th>
            <th class="th">Status</th>
            <th class="th">Renewals</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($issues as $issue)
          @php $isOverdue = today()->gt($issue->due_date); @endphp
          <tr class="tr {{ $isOverdue ? 'bg-red-50' : '' }}">
            <td class="td" data-accession="{{ $issue->book?->accession_number }}" data-isbn="{{ $issue->book?->isbn }}">
              <div class="font-medium">{{ $issue->book?->title }}</div>
              <div class="text-xs text-slate-400">{{ $issue->book?->accession_number }}</div>
            </td>
            <td class="td">{{ $issue->student?->first_name }} {{ $issue->student?->last_name }}</td>
            <td class="td text-sm">{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}</td>
            <td class="td text-sm {{ $isOverdue ? 'text-red-600 font-semibold' : '' }}">
              {{ \Carbon\Carbon::parse($issue->due_date)->format('d M Y') }}
              @if($isOverdue)
              <div class="text-xs text-red-500">{{ \Carbon\Carbon::parse($issue->due_date)->diffInDays(today()) }} days overdue</div>
              @endif
            </td>
            <td class="td">
              <span class="badge-{{ $isOverdue ? 'red' : 'green' }}">{{ $isOverdue ? 'Overdue' : 'Issued' }}</span>
            </td>
            <td class="td text-center">{{ $issue->renew_count ?? 0 }}/3</td>
            <td class="td">
              <div x-data="{ open: false }" class="relative inline-block">
                <button @click="open = !open" class="btn btn-secondary btn-sm">Actions ▾</button>
                <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-1 bg-white shadow-lg rounded-lg border border-slate-200 z-10 min-w-[150px] py-1">
                  @if(($issue->renew_count ?? 0) < 3)
                  <div x-data="{ showRenew: false }">
                    <button @click="showRenew = !showRenew" class="block w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Renew</button>
                    <div x-show="showRenew" class="px-3 pb-2">
                      <form method="POST" action="{{ route('library.issue.renew', $issue->id) }}" class="flex gap-2 mt-2">
                        @csrf
                        <input type="date" name="due_date" class="input text-xs" min="{{ today()->addDay()->toDateString() }}" value="{{ today()->addDays(14)->toDateString() }}" required>
                        <button type="submit" class="btn btn-primary btn-sm text-xs">Go</button>
                      </form>
                    </div>
                  </div>
                  @endif
                  <form method="POST" action="{{ route('library.return') }}">
                    @csrf
                    <input type="hidden" name="issue_id" value="{{ $issue->id }}">
                    @php
                      $daysOverdue = $isOverdue ? \Carbon\Carbon::parse($issue->due_date)->diffInDays(today()) : 0;
                      $fineAmt = $daysOverdue * $fineRate;
                      $returnMsg = 'Return \'' . addslashes($issue->book?->title ?? 'this book') . '\'?' . ($isOverdue ? ' Fine: ₹' . $fineAmt . ' (' . $daysOverdue . ' days × ₹' . $fineRate . '/day)' : '');
                    @endphp
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                            onclick="return confirm('{{ $returnMsg }}')">Return</button>
                  </form>
                  <form method="POST" action="{{ route('library.issue.mark-lost', $issue->id) }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-orange-600 hover:bg-orange-50" onclick="return confirm('Mark book as LOST?')">Mark as Lost</button>
                  </form>
                  <a href="{{ route('library.issue.receipt', $issue->id) }}" target="_blank"
                     class="block w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Print Receipt</a>
                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="td text-center py-10 text-slate-400">No books currently issued.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($issues->hasPages())<div class="mt-4">{{ $issues->links() }}</div>@endif
  </div>
</div>
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxing-js/0.20.0/zxing.min.js" crossorigin="anonymous"></script>
<script>
function returnScanner() {
  return {
    scanning: false,
    returnCode: '',
    returnResult: '',
    codeReader: null,

    init() {},

    async toggleScanner() {
      if (this.scanning) {
        if (this.codeReader) { this.codeReader.reset(); this.codeReader = null; }
        this.scanning = false;
      } else {
        if (typeof ZXing === 'undefined') { this.returnResult = '<span class="text-red-600">ZXing not loaded. Use manual entry.</span>'; return; }
        this.codeReader = new ZXing.BrowserMultiFormatReader();
        const devices = await this.codeReader.listVideoInputDevices();
        if (!devices.length) { this.returnResult = '<span class="text-red-600">No camera found.</span>'; return; }
        const dev = devices.find(d => /back|rear|environment/i.test(d.label)) || devices[devices.length-1];
        this.scanning = true;
        this.codeReader.decodeFromVideoDevice(dev.deviceId, document.getElementById('return-video'), (result) => {
          if (result) { this.lookupReturn(result.getText()); }
        });
      }
    },

    lookupReturn(code) {
      if (!code) return;
      const rows = document.querySelectorAll('tbody tr');
      let found = false;
      rows.forEach(row => {
        const accEl = row.querySelector('[data-accession]');
        const acc = accEl ? accEl.dataset.accession : '';
        const isbn = accEl ? accEl.dataset.isbn : '';
        if (acc === code.trim() || isbn === code.trim()) {
          row.style.background = '#fef3c7';
          row.scrollIntoView({ behavior: 'smooth', block: 'center' });
          this.returnResult = `<strong>Found:</strong> ${row.querySelector('td')?.innerText || 'Record located'} — use the Actions menu to return.`;
          found = true;
        }
      });
      if (!found) {
        this.returnResult = `<span class="text-red-600">No issued record found for: <strong>${code.trim()}</strong></span>`;
      }
      this.returnCode = '';
    },
  };
}
</script>
@endpush
@endsection
