@extends('layouts.app')
@section('title', 'Night Duty Assignment')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Night Duty Staff Assignment</h1>
      <p class="page-subtitle">Assign warden / staff for night duty at each hostel</p>
    </div>
    <a href="{{ route('hostel.index') }}" class="btn btn-secondary btn-sm">← Hostel</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Assignment Form --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Assign Night Duty</h3>
      <form method="POST" action="{{ route('hostel.night-duty.store') }}" class="space-y-3">
        @csrf
        <div>
          <label class="label">Hostel <span class="text-red-500">*</span></label>
          <select name="hostel_id" class="select" required>
            <option value="">Select hostel</option>
            @foreach($hostels as $h)<option value="{{ $h->id }}">{{ $h->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label class="label">Staff / Employee <span class="text-red-500">*</span></label>
          <select name="employee_id" class="select" required>
            <option value="">Select employee</option>
            @foreach($employees as $emp)
              <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->employee_number }})
                @if($emp->designation) — {{ $emp->designation }}@endif
              </option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Duty Date <span class="text-red-500">*</span></label>
          <input type="date" name="duty_date" class="input" value="{{ today()->toDateString() }}" required>
        </div>
        <div>
          <label class="label">Shift</label>
          <select name="shift" class="select">
            <option value="night">Night Only</option>
            <option value="full">Full Day</option>
          </select>
        </div>
        <div>
          <label class="label">Notes</label>
          <input type="text" name="notes" class="input" placeholder="Any specific instructions...">
        </div>
        <button type="submit" class="btn btn-primary w-full">Assign Duty</button>
      </form>
    </div>

    {{-- Duty List --}}
    <div class="lg:col-span-2">
      <div class="card">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-slate-700">Duty Schedule</h3>
          <form method="GET" class="flex gap-2 items-end">
            <input type="month" name="month" value="{{ $month }}" class="input text-sm py-1" onchange="this.form.submit()">
            <select name="hostel_id" class="select text-sm py-1" onchange="this.form.submit()">
              <option value="">All Hostels</option>
              @foreach($hostels as $h)<option value="{{ $h->id }}" @selected(request('hostel_id')==$h->id)>{{ $h->name }}</option>@endforeach
            </select>
          </form>
        </div>

        @if($duties->isEmpty())
          <p class="text-center py-8 text-slate-400">No night duty assigned for this month.</p>
        @else
        <div class="table-wrap">
          <table class="w-full text-sm">
            <thead><tr>
              <th class="th">Date</th>
              <th class="th">Hostel</th>
              <th class="th">Staff</th>
              <th class="th">Shift</th>
              <th class="th">Notes</th>
              <th class="th"></th>
            </tr></thead>
            <tbody>
              @foreach($duties->sortBy('duty_date') as $duty)
              <tr class="tr">
                <td class="td font-medium">{{ $duty->duty_date->format('d M Y') }}
                  @if($duty->duty_date->isToday()) <span class="badge-green ml-1 text-xs">Today</span>@endif
                </td>
                <td class="td">{{ $duty->hostel?->name ?? '—' }}</td>
                <td class="td">
                  {{ $duty->employee?->first_name }} {{ $duty->employee?->last_name }}
                  <span class="text-xs text-slate-400 block">{{ $duty->employee?->employee_number }}</span>
                </td>
                <td class="td"><span class="badge-slate capitalize text-xs">{{ $duty->shift }}</span></td>
                <td class="td text-slate-500 text-xs">{{ $duty->notes ?? '—' }}</td>
                <td class="td">
                  <form method="POST" action="{{ route('hostel.night-duty.delete', $duty->id) }}"
                    onsubmit="return confirm('Remove this duty assignment?')">
                    @csrf @method('DELETE')
                    <button class="text-xs text-red-400 hover:text-red-600">Remove</button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
