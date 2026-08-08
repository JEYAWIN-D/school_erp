@extends('layouts.app')
@section('title', 'Edit — ' . $employee->first_name)
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="{{ route('hr.employees.show', $employee->id) }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="page-title">Edit Employee</h1>
  </div>
  <form method="POST" action="{{ route('hr.employees.update', $employee->id) }}" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Personal & Employment</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div><label class="label">First Name</label><input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}" class="input"></div>
        <div><label class="label">Last Name</label><input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}" class="input"></div>
        <div><label class="label">Mobile</label><input type="tel" name="mobile" value="{{ old('mobile', $employee->mobile) }}" class="input"></div>
        <div><label class="label">Email</label><input type="email" name="email" value="{{ old('email', $employee->email) }}" class="input"></div>
        <div><label class="label">Designation</label><input type="text" name="designation" value="{{ old('designation', $employee->designation) }}" class="input"></div>
        <div><label class="label">Department</label><input type="text" name="department" value="{{ old('department', $employee->department) }}" class="input"></div>
        <div><label class="label">Type</label>
          <select name="employee_type" class="select">
            @foreach(['teaching' => 'Teaching', 'non_teaching' => 'Non-Teaching', 'admin' => 'Admin', 'support' => 'Support'] as $v => $l)
              <option value="{{ $v }}" @selected(old('employee_type', $employee->employee_type) === $v)>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="flex items-center gap-2 pt-5">
          <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $employee->is_active)) class="w-4 h-4 text-blue-600 rounded">
          <label for="is_active" class="text-sm text-slate-700">Active Employee</label>
        </div>
      </div>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Photo</h3>
      <div class="flex items-center gap-6">
        @if($employee->photo)
          <img src="{{ asset('storage/'.$employee->photo) }}" class="w-20 h-20 rounded-full object-cover border-2 border-slate-200" alt="">
        @else
          <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 text-2xl font-bold">
            {{ strtoupper(substr($employee->first_name, 0, 1)) }}
          </div>
        @endif
        <div class="flex-1">
          <label class="label text-xs">Upload New Photo</label>
          <input type="file" name="photo" accept="image/*" class="input text-sm">
          <p class="text-xs text-slate-400 mt-1">JPG/PNG, max 2MB. Replaces existing photo.</p>
        </div>
      </div>
    </div>
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Statutory Details</h3>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label class="label">PF Account No.</label>
          <input type="text" name="pf_account_no" value="{{ old('pf_account_no', $employee->pf_account_no) }}" class="input" placeholder="e.g. TN/52345/12345">
        </div>
        <div>
          <label class="label">ESI No.</label>
          <input type="text" name="esi_no" value="{{ old('esi_no', $employee->esi_no) }}" class="input" placeholder="17-digit ESI number">
        </div>
        <div>
          <label class="label">PAN No.</label>
          <input type="text" name="pan_no" value="{{ old('pan_no', $employee->pan_no) }}" class="input" placeholder="ABCDE1234F" maxlength="15" style="text-transform:uppercase">
        </div>
      </div>
    </div>
    <div class="flex justify-end gap-3">
      <a href="{{ route('hr.employees.show', $employee->id) }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
  </form>
</div>
@endsection
