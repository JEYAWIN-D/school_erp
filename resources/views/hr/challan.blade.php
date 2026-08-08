@extends('layouts.app')
@section('title','Statutory Challan Generation')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Statutory Challan Generation</h1>
    @if($challanData->count())
    <div class="flex gap-2">
      <a href="{{ route('hr.payroll.challan', array_merge(request()->all(), ['format' => 'pdf'])) }}"
        class="btn btn-secondary btn-sm">Download PDF</a>
    </div>
    @endif
  </div>

  <form method="GET" class="card-flat py-3">
    <div class="flex flex-wrap gap-3 items-end">
      <div>
        <label class="label text-xs">Month</label>
        <input type="month" name="month" value="{{ $month }}" class="input w-40">
      </div>
      <div>
        <label class="label text-xs">Challan Type</label>
        <select name="type" class="select w-32">
          <option value="pf"  @selected($type === 'pf')>PF (EPF)</option>
          <option value="esi" @selected($type === 'esi')>ESI</option>
          <option value="pt"  @selected($type === 'pt')>PT (Professional Tax)</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Generate</button>
    </div>
  </form>

  @if($challanData->count())
  {{-- Summary cards --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    @if($type === 'pf')
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-blue-600">₹{{ number_format($totals['pf_employee'], 2) }}</p>
      <p class="text-xs text-slate-400 mt-1">Employee PF (12%)</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-indigo-600">₹{{ number_format($totals['pf_employer'], 2) }}</p>
      <p class="text-xs text-slate-400 mt-1">Employer PF (12%)</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-slate-800">₹{{ number_format($totals['pf_total'], 2) }}</p>
      <p class="text-xs text-slate-400 mt-1">Total PF Contribution</p>
    </div>
    @elseif($type === 'esi')
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-blue-600">₹{{ number_format($totals['esi_employee'], 2) }}</p>
      <p class="text-xs text-slate-400 mt-1">Employee ESI (0.75%)</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-indigo-600">₹{{ number_format($totals['esi_employer'], 2) }}</p>
      <p class="text-xs text-slate-400 mt-1">Employer ESI (3.25%)</p>
    </div>
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-slate-800">₹{{ number_format($totals['esi_total'], 2) }}</p>
      <p class="text-xs text-slate-400 mt-1">Total ESI Contribution</p>
    </div>
    @else
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-green-600">₹{{ number_format($totals['pt_total'], 2) }}</p>
      <p class="text-xs text-slate-400 mt-1">Total PT Payable</p>
    </div>
    @endif
    <div class="card text-center py-4">
      <p class="text-2xl font-bold text-slate-600">{{ $challanData->count() }}</p>
      <p class="text-xs text-slate-400 mt-1">Employees</p>
    </div>
  </div>

  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        @if($type === 'pf')
          @foreach(['#','Employee','EMP ID','UAN','Basic','Emp PF (12%)','Employer PF (12%)','Total PF'] as $h)
        @elseif($type === 'esi')
          @foreach(['#','Employee','ESI Number','Gross','Emp ESI (0.75%)','Employer ESI (3.25%)','Total ESI'] as $h)
        @else
          @foreach(['#','Employee','Designation','Gross Salary','PT Amount'] as $h)
        @endif
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($challanData as $i => $row)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $i + 1 }}</td>
          <td class="px-4 py-3">
            <p class="font-medium text-slate-800">{{ $row['employee']?->full_name }}</p>
            <p class="text-xs text-slate-400">{{ $row['employee']?->employee_number }}</p>
          </td>
          @if($type === 'pf')
          <td class="px-4 py-3 text-xs text-slate-500">{{ $row['employee']?->employee_number }}</td>
          <td class="px-4 py-3 text-xs text-slate-400">{{ $row['employee']?->uan_number ?? '—' }}</td>
          <td class="px-4 py-3">₹{{ number_format($row['record']->basic_salary ?? 0, 2) }}</td>
          <td class="px-4 py-3 text-blue-600">₹{{ number_format($row['pf_employee'], 2) }}</td>
          <td class="px-4 py-3 text-indigo-600">₹{{ number_format($row['pf_employer'], 2) }}</td>
          <td class="px-4 py-3 font-semibold">₹{{ number_format($row['pf_total'], 2) }}</td>
          @elseif($type === 'esi')
          <td class="px-4 py-3 text-xs text-slate-400">{{ $row['employee']?->esi_number ?? '—' }}</td>
          <td class="px-4 py-3">₹{{ number_format($row['record']->gross_salary ?? 0, 2) }}</td>
          <td class="px-4 py-3 text-blue-600">₹{{ number_format($row['esi_employee'], 2) }}</td>
          <td class="px-4 py-3 text-indigo-600">₹{{ number_format($row['esi_employer'], 2) }}</td>
          <td class="px-4 py-3 font-semibold">₹{{ number_format($row['esi_total'], 2) }}</td>
          @else
          <td class="px-4 py-3 text-xs text-slate-500">{{ $row['employee']?->designation }}</td>
          <td class="px-4 py-3">₹{{ number_format($row['record']->gross_salary ?? 0, 2) }}</td>
          <td class="px-4 py-3 font-semibold text-green-700">₹{{ number_format($row['pt'], 2) }}</td>
          @endif
        </tr>
        @endforeach
        {{-- Totals row --}}
        <tr class="bg-slate-100 font-bold">
          <td colspan="{{ $type === 'pf' ? 5 : ($type === 'esi' ? 4 : 3) }}" class="px-4 py-3 text-right text-sm">Total</td>
          @if($type === 'pf')
          <td class="px-4 py-3 text-blue-700">₹{{ number_format($totals['pf_employee'], 2) }}</td>
          <td class="px-4 py-3 text-indigo-700">₹{{ number_format($totals['pf_employer'], 2) }}</td>
          <td class="px-4 py-3">₹{{ number_format($totals['pf_total'], 2) }}</td>
          @elseif($type === 'esi')
          <td class="px-4 py-3 text-blue-700">₹{{ number_format($totals['esi_employee'], 2) }}</td>
          <td class="px-4 py-3 text-indigo-700">₹{{ number_format($totals['esi_employer'], 2) }}</td>
          <td class="px-4 py-3">₹{{ number_format($totals['esi_total'], 2) }}</td>
          @else
          <td class="px-4 py-3 text-green-700">₹{{ number_format($totals['pt_total'], 2) }}</td>
          @endif
        </tr>
      </tbody>
    </table>
  </div>
  @elseif(request('month'))
  <div class="card text-center py-10 text-slate-400">No approved payroll records found for {{ $month }}.</div>
  @endif
</div>
@endsection
