@extends('layouts.app')
@section('title', 'PF / ESI / PT / TDS Annual Statutory Report')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Statutory Reports — PF / ESI / PT / TDS</h1>
    @if(isset($report) && $report->count())
    <div class="flex gap-2">
      <a href="{{ request()->fullUrlWithQuery(['format' => 'excel']) }}" class="btn btn-secondary btn-sm flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export Excel
      </a>
    </div>
    @endif
  </div>

  <form method="GET" class="card grid grid-cols-2 md:grid-cols-3 gap-4">
    <div>
      <label class="label">Financial Year</label>
      <select name="financial_year" class="select">
        @php $currentY = date('Y'); @endphp
        @foreach(range($currentY - 2, $currentY + 1) as $y)
        <option value="{{ $y }}-{{ $y+1 }}" @selected(($financialYear ?? '') === "$y-" . ($y+1))>FY {{ $y }}-{{ $y+1 }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="label">Department</label>
      <select name="department_id" class="select">
        <option value="">All Departments</option>
        @foreach($departments ?? [] as $dept)
        <option value="{{ $dept->id }}" @selected(request('department_id') == $dept->id)>{{ $dept->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex items-end gap-2">
      <button type="submit" name="action" value="1" class="btn btn-primary">Generate Report</button>
    </div>
  </form>

  @if(isset($report) && $report->count())
  {{-- Summary Cards --}}
  <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
    <div class="card text-center">
      <div class="text-xl font-bold text-indigo-600">{{ $report->count() }}</div>
      <div class="text-xs text-slate-500 mt-1">Employees</div>
    </div>
    <div class="card text-center">
      <div class="text-xl font-bold text-blue-600">₹{{ number_format($report->sum('pf_employee') + $report->sum('pf_employer'), 0) }}</div>
      <div class="text-xs text-slate-500 mt-1">Total PF (Annual)</div>
    </div>
    <div class="card text-center">
      <div class="text-xl font-bold text-emerald-600">₹{{ number_format($report->sum('esi_employee') + $report->sum('esi_employer'), 0) }}</div>
      <div class="text-xs text-slate-500 mt-1">Total ESI (Annual)</div>
    </div>
    <div class="card text-center">
      <div class="text-xl font-bold text-amber-600">₹{{ number_format($report->sum('pt_annual'), 0) }}</div>
      <div class="text-xs text-slate-500 mt-1">Total PT (Annual)</div>
    </div>
    <div class="card text-center">
      <div class="text-xl font-bold text-red-600">₹{{ number_format($report->sum('tds_annual'), 0) }}</div>
      <div class="text-xs text-slate-500 mt-1">Total TDS (Annual)</div>
    </div>
  </div>

  <div class="table-wrap overflow-x-auto">
    <table class="w-full text-sm whitespace-nowrap">
      <thead>
        <tr>
          <th class="th" rowspan="2">Employee</th>
          <th class="th" rowspan="2">PAN / PF No / ESI No</th>
          <th class="th text-right" rowspan="2">Annual Gross</th>
          <th class="th text-center" colspan="2">PF (Annual)</th>
          <th class="th text-center" colspan="2">ESI (Annual)</th>
          <th class="th text-right" rowspan="2">PT</th>
          <th class="th text-right" rowspan="2">TDS</th>
          <th class="th text-center" rowspan="2">Form 16</th>
        </tr>
        <tr>
          <th class="th text-right text-xs">Employee</th>
          <th class="th text-right text-xs">Employer</th>
          <th class="th text-right text-xs">Employee</th>
          <th class="th text-right text-xs">Employer</th>
        </tr>
      </thead>
      <tbody>
        @foreach($report as $row)
        <tr class="tr">
          <td class="td">
            <div class="font-medium">{{ $row['employee']->first_name }} {{ $row['employee']->last_name }}</div>
            <div class="text-xs text-slate-400">{{ $row['employee']->employee_code }}</div>
          </td>
          <td class="td text-xs">
            <div>PAN: {{ $row['employee']->pan_no ?? '—' }}</div>
            <div>PF: {{ $row['employee']->pf_account_no ?? '—' }}</div>
            <div>ESI: {{ $row['employee']->esi_no ?? '—' }}</div>
          </td>
          <td class="td text-right font-medium">₹{{ number_format($row['annual_gross'], 0) }}</td>
          <td class="td text-right text-blue-600">₹{{ number_format($row['pf_employee'], 0) }}</td>
          <td class="td text-right text-blue-800">₹{{ number_format($row['pf_employer'], 0) }}</td>
          <td class="td text-right text-emerald-600">₹{{ number_format($row['esi_employee'], 0) }}</td>
          <td class="td text-right text-emerald-800">₹{{ number_format($row['esi_employer'], 0) }}</td>
          <td class="td text-right text-amber-600">₹{{ number_format($row['pt_annual'], 0) }}</td>
          <td class="td text-right text-red-600 font-semibold">₹{{ number_format($row['tds_annual'], 0) }}</td>
          <td class="td text-center">
            <a href="{{ route('hr.employees.form-16', ['id' => $row['employee']->id, 'financial_year' => $financialYear]) }}"
               target="_blank" class="btn btn-secondary btn-xs">PDF</a>
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="bg-slate-50 font-bold">
          <td class="td" colspan="2">Total</td>
          <td class="td text-right">₹{{ number_format($report->sum('annual_gross'), 0) }}</td>
          <td class="td text-right text-blue-600">₹{{ number_format($report->sum('pf_employee'), 0) }}</td>
          <td class="td text-right text-blue-800">₹{{ number_format($report->sum('pf_employer'), 0) }}</td>
          <td class="td text-right text-emerald-600">₹{{ number_format($report->sum('esi_employee'), 0) }}</td>
          <td class="td text-right text-emerald-800">₹{{ number_format($report->sum('esi_employer'), 0) }}</td>
          <td class="td text-right text-amber-600">₹{{ number_format($report->sum('pt_annual'), 0) }}</td>
          <td class="td text-right text-red-600">₹{{ number_format($report->sum('tds_annual'), 0) }}</td>
          <td class="td"></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <p class="text-xs text-slate-400">PF: 12% employee + ~13.2% employer of basic | ESI: 0.75% employee + 3.25% employer of gross (applies ≤ ₹21,000/month) | PT: Tamil Nadu slabs | TDS: As per income tax slabs</p>

  @else
  <div class="card text-center text-slate-400 py-12">
    Select a financial year and click <strong>Generate Report</strong>.
  </div>
  @endif
</div>
@endsection
