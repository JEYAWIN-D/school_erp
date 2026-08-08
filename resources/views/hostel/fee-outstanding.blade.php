@extends('layouts.app')
@section('title', 'Hostel Fee Outstanding')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Hostel Fee Outstanding</h1>
      <p class="page-subtitle">{{ $currentYear?->name }} — Students with pending hostel dues</p>
    </div>
    <a href="{{ route('hostel.index') }}" class="btn btn-secondary">Back</a>
  </div>

  <div class="card">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="label">Hostel</label>
        <select name="hostel_id" class="select">
          <option value="">All Hostels</option>
          @foreach($hostels as $hostel)
          <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
  </div>

  @if($outstanding->isNotEmpty())
  @php $totalBalance = $outstanding->sum('balance'); @endphp
  <div class="grid grid-cols-2 gap-4">
    <div class="card text-center py-5"><p class="text-3xl font-bold text-red-600">{{ $outstanding->count() }}</p><p class="text-sm text-slate-500">Students with Dues</p></div>
    <div class="card text-center py-5"><p class="text-3xl font-bold text-red-600">₹{{ number_format($totalBalance, 2) }}</p><p class="text-sm text-slate-500">Total Outstanding</p></div>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table class="w-full">
        <thead>
          <tr>
            <th class="th">Student</th>
            <th class="th">Hostel / Room</th>
            <th class="th">Monthly Fee</th>
            <th class="th">Expected</th>
            <th class="th">Paid</th>
            <th class="th">Balance</th>
          </tr>
        </thead>
        <tbody>
          @foreach($outstanding as $row)
          <tr class="tr">
            <td class="td">
              <a href="{{ route('students.show', $row['allotment']->student_id) }}" class="font-medium text-indigo-600 hover:underline">
                {{ $row['allotment']->student?->first_name }} {{ $row['allotment']->student?->last_name }}
              </a>
            </td>
            <td class="td text-sm text-slate-500">
              {{ $row['allotment']->room?->hostel?->name }} — Room {{ $row['allotment']->room?->room_number }}
            </td>
            <td class="td">₹{{ number_format($row['allotment']->monthly_fee, 2) }}</td>
            <td class="td">₹{{ number_format($row['totalExpected'], 2) }}</td>
            <td class="td text-green-600">₹{{ number_format($row['totalPaid'], 2) }}</td>
            <td class="td font-semibold text-red-600">₹{{ number_format($row['balance'], 2) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @else
  <div class="card text-center py-12 text-slate-400">No outstanding hostel fees found.</div>
  @endif
</div>
@endsection
