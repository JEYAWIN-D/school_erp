@extends('layouts.app')
@section('title', 'Purchase Requisitions')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Purchase Requisitions</h1>
    <div class="flex gap-2">
      <a href="{{ route('inventory.index') }}" class="btn-sm btn-secondary">← Inventory</a>
      <a href="{{ route('inventory.requisitions.create') }}" class="btn-primary btn-sm">+ New Requisition</a>
    </div>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  <form method="GET" class="flex gap-3">
    <select name="status" class="select max-w-xs">
      <option value="">All Status</option>
      <option value="pending" @selected(request('status')=='pending')>Pending</option>
      <option value="approved" @selected(request('status')=='approved')>Approved</option>
      <option value="rejected" @selected(request('status')=='rejected')>Rejected</option>
      <option value="ordered" @selected(request('status')=='ordered')>Ordered</option>
    </select>
    <button type="submit" class="btn-primary btn-sm">Filter</button>
  </form>

  <div class="table-wrap">
    <table class="w-full text-sm">
      <thead>
        <tr>
          <th class="th">PR No.</th>
          <th class="th">Required By</th>
          <th class="th">Purpose</th>
          <th class="th">Requested By</th>
          <th class="th">Status</th>
          <th class="th">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($requisitions as $pr)
        <tr class="tr" x-data="{ open: false }">
          <td class="td font-mono font-semibold text-indigo-600">{{ $pr->pr_number }}</td>
          <td class="td">{{ \Carbon\Carbon::parse($pr->required_by)->format('d M Y') }}</td>
          <td class="td text-xs text-slate-500">{{ Str::limit($pr->purpose, 50) ?? '—' }}</td>
          <td class="td text-xs">{{ $pr->requestedBy?->name ?? '—' }}</td>
          <td class="td">
            @php $colors=['pending'=>'badge-amber','approved'=>'badge-green','rejected'=>'badge-red','ordered'=>'badge-blue']; @endphp
            <span class="{{ $colors[$pr->status] ?? 'badge-slate' }}">{{ ucfirst($pr->status) }}</span>
          </td>
          <td class="td flex gap-1">
            <button @click="open=!open" class="btn-xs btn-secondary">Items</button>
            <a href="{{ route('inventory.requisitions.print', $pr->id) }}" target="_blank" class="btn-xs btn-secondary">Print</a>
            @if($pr->status === 'pending')
            <form method="POST" action="{{ route('inventory.requisitions.approve', $pr->id) }}" class="inline"
                  onsubmit="return confirm('Approve requisition PR-{{ $pr->id }}?')">
              @csrf @method('PATCH')
              <input type="hidden" name="action" value="approve">
              <button type="submit" class="btn-xs btn-primary">Approve</button>
            </form>
            <form method="POST" action="{{ route('inventory.requisitions.approve', $pr->id) }}" class="inline"
                  onsubmit="return confirm('Reject requisition PR-{{ $pr->id }}? This cannot be undone.')">
              @csrf @method('PATCH')
              <input type="hidden" name="action" value="reject">
              <button type="submit" class="btn-xs btn-secondary text-rose-600">Reject</button>
            </form>
            @endif
          </td>
        </tr>
        <tr x-show="open" x-cloak class="bg-slate-50">
          <td colspan="6" class="px-6 py-3">
            <table class="w-full text-xs">
              <thead><tr class="text-slate-500"><th class="text-left py-1">Item</th><th class="text-right py-1">Qty</th><th class="text-right py-1">Est. Price</th></tr></thead>
              <tbody>
                @foreach($pr->items as $pi)
                <tr>
                  <td class="py-1">{{ $pi->item?->name ?? 'N/A' }}</td>
                  <td class="text-right py-1">{{ $pi->quantity }} {{ $pi->item?->unit }}</td>
                  <td class="text-right py-1">{{ $pi->estimated_price ? '₹'.number_format($pi->estimated_price,2) : '—' }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </td>
        </tr>
        @empty
        <tr><td class="td text-center text-slate-400" colspan="6">No requisitions found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div>{{ $requisitions->withQueryString()->links() }}</div>
</div>
@endsection
