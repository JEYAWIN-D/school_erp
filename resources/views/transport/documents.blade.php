@extends('layouts.app')
@section('title','Vehicle Documents & Expiry')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Vehicle Documents & Expiry Tracking</h1>
  @php
    $expiredCount = $vehicles->filter(fn($v)=>$v->expiring_soon)->count();
  @endphp
  @if($expiredCount > 0)
  <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-center gap-3">
    <span class="text-red-500 font-bold text-lg">⚠</span>
    <p class="text-red-700 text-sm"><strong>{{ $expiredCount }} vehicles</strong> have documents expiring within 30 days.</p>
  </div>
  @endif
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Vehicle','Registration','Fitness Expiry','Insurance Expiry','Permit Expiry','PUC Expiry','Tax Expiry','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($vehicles as $v)
        @php
          $today = now();
          $checkDate = fn($d) => $d ? ($d->isPast() ? 'expired' : ($d->diffInDays($today) < 30 ? 'soon' : 'ok')) : 'missing';
          $classes = ['expired'=>'text-red-600 font-bold','soon'=>'text-amber-600 font-semibold','ok'=>'text-green-600','missing'=>'text-slate-300'];
          $badges = ['expired'=>'<span class="text-xs bg-red-100 text-red-600 px-1 rounded">Expired</span>','soon'=>'<span class="text-xs bg-amber-100 text-amber-600 px-1 rounded">Soon</span>','ok'=>'','missing'=>'<span class="text-xs bg-slate-100 text-slate-400 px-1 rounded">NA</span>'];
        @endphp
        <tr class="{{ $v->expiring_soon ? 'bg-amber-50/30' : 'hover:bg-slate-50' }}">
          <td class="px-4 py-3">
            <p class="font-semibold text-slate-800 text-sm">{{ $v->make }} {{ $v->model }}</p>
            <p class="text-xs text-slate-400">{{ $v->vehicle_type }}</p>
          </td>
          <td class="px-4 py-3 font-mono text-xs text-indigo-700">{{ $v->vehicle_number }}</td>
          @foreach(['fitness_expiry','insurance_expiry','permit_expiry','puc_expiry','tax_expiry'] as $field)
          @php $s = $checkDate($v->$field); @endphp
          <td class="px-4 py-3">
            <span class="{{ $classes[$s] }} text-xs">{{ $v->$field ? $v->$field->format('d M Y') : '—' }}</span>
            {!! $badges[$s] !!}
          </td>
          @endforeach
          <td class="px-4 py-3">
            <a href="{{ route('transport.vehicle.edit',$v->id) }}" class="text-indigo-600 hover:underline text-xs">Update Docs</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No vehicles.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
