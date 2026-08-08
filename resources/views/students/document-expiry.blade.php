@extends('layouts.app')
@section('title', 'Document Expiry Tracking')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Document Expiry Tracking</h1>

  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3 items-end">
      <div>
        <label class="label">Alert window (days ahead)</label>
        <input type="number" name="days" value="{{ $daysAhead }}" min="1" max="365" class="input w-28">
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Refresh</button>
    </div>
  </form>

  {{-- Expiring soon --}}
  <div class="card">
    <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
      Expiring Within {{ $daysAhead }} Days
      <span class="badge-amber">{{ $expiring->count() }}</span>
    </h2>
    @if($expiring->count())
    <div class="table-wrap">
      <table class="min-w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Document Type</th>
          <th class="th">File</th>
          <th class="th">Expiry Date</th>
          <th class="th">Days Remaining</th>
        </tr></thead>
        <tbody>
          @foreach($expiring as $doc)
          @php $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($doc->expiry_date), false); @endphp
          <tr class="tr">
            <td class="td font-medium">{{ $doc->student?->full_name }}
              <div class="text-xs text-slate-400">{{ $doc->student?->admission_number }}</div>
            </td>
            <td class="td text-slate-600 capitalize">{{ str_replace('_',' ',$doc->document_type) }}</td>
            <td class="td text-xs">
              <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-indigo-600 hover:underline">{{ $doc->original_name }}</a>
            </td>
            <td class="td">{{ $doc->expiry_date?->format('d M Y') }}</td>
            <td class="td">
              <span class="{{ $daysLeft <= 7 ? 'badge-red' : 'badge-amber' }}">{{ $daysLeft }} days</span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @else
    <p class="text-slate-400 text-sm">No documents expiring in the next {{ $daysAhead }} days.</p>
    @endif
  </div>

  {{-- Already expired --}}
  <div class="card">
    <h2 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
      Already Expired
      <span class="badge-red">{{ $expired->count() }}</span>
    </h2>
    @if($expired->count())
    <div class="table-wrap">
      <table class="min-w-full text-sm">
        <thead><tr>
          <th class="th">Student</th>
          <th class="th">Document Type</th>
          <th class="th">Expiry Date</th>
          <th class="th">Expired</th>
        </tr></thead>
        <tbody>
          @foreach($expired as $doc)
          @php $daysAgo = abs(now()->diffInDays(\Carbon\Carbon::parse($doc->expiry_date), false)); @endphp
          <tr class="tr">
            <td class="td font-medium">{{ $doc->student?->full_name }}
              <div class="text-xs text-slate-400">{{ $doc->student?->admission_number }}</div>
            </td>
            <td class="td text-slate-600 capitalize">{{ str_replace('_',' ',$doc->document_type) }}</td>
            <td class="td text-red-500">{{ $doc->expiry_date?->format('d M Y') }}</td>
            <td class="td"><span class="badge-red text-xs">{{ $daysAgo }} days ago</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @else
    <p class="text-slate-400 text-sm">No expired documents on record.</p>
    @endif
  </div>
</div>
@endsection
