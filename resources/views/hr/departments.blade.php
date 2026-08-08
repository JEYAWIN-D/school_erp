@extends('layouts.app')
@section('title','Departments & Designations')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Departments & Designations</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Departments --}}
    <div class="card space-y-4">
      <div class="flex items-center justify-between pb-2 border-b border-slate-100">
        <h3 class="font-semibold text-slate-700">Departments</h3>
        <button x-data @click="$dispatch('open-modal','add-dept')" class="btn btn-primary btn-sm">+ Add</button>
      </div>
      <div class="space-y-2">
        @forelse($departments as $dept)
        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
          <div>
            <p class="font-semibold text-slate-800 text-sm">{{ $dept->name }}</p>
            <p class="text-xs text-slate-400">{{ $dept->code }} | {{ $dept->designations_count }} designations | {{ $dept->employees_count }} staff</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="{{ $dept->is_active ? 'badge-green' : 'badge-slate' }} text-xs">{{ $dept->is_active ? 'Active' : 'Off' }}</span>
            <button x-data @click="$dispatch('open-modal','edit-dept-{{ $dept->id }}')" class="text-indigo-600 hover:underline text-xs">Edit</button>
          </div>
        </div>
        {{-- Edit modal --}}
        <div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='edit-dept-{{ $dept->id }}')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
          <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h3 class="font-semibold text-slate-700 mb-4">Edit Department</h3>
            <form method="POST" action="{{ route('hr.departments.update',$dept->id) }}" class="space-y-3">
              @csrf @method('PUT')
              <div><label class="label">Name</label><input type="text" name="name" class="input" value="{{ $dept->name }}" required></div>
              <div><label class="label">Code</label><input type="text" name="code" class="input" value="{{ $dept->code }}"></div>
              <div><label class="label">Description</label><textarea name="description" class="input h-16">{{ $dept->description }}</textarea></div>
              <div class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked($dept->is_active)><label class="text-sm">Active</label></div>
              <div class="flex gap-2"><button type="submit" class="btn btn-primary btn-sm">Save</button><button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button></div>
            </form>
          </div>
        </div>
        @empty
        <p class="text-slate-400 text-sm text-center py-4">No departments.</p>
        @endforelse
      </div>
    </div>
    {{-- Designations --}}
    <div class="card space-y-4">
      <div class="flex items-center justify-between pb-2 border-b border-slate-100">
        <h3 class="font-semibold text-slate-700">Designations & Pay Bands</h3>
        <button x-data @click="$dispatch('open-modal','add-desig')" class="btn btn-primary btn-sm">+ Add</button>
      </div>
      <div class="space-y-2">
        @forelse($designations as $d)
        <div class="flex items-start justify-between py-2 border-b border-slate-100 last:border-0">
          <div>
            <p class="font-semibold text-slate-800 text-sm">{{ $d->name }}</p>
            <p class="text-xs text-slate-400">{{ $d->department?->name }}
              @if($d->grade) &nbsp;|&nbsp; Grade: <span class="text-indigo-600 font-medium">{{ $d->grade }}</span>@endif
              @if($d->pay_scale) &nbsp;|&nbsp; Scale: <span class="text-amber-600 font-medium">{{ $d->pay_scale }}</span>@endif
            </p>
            @if($d->pay_band_min || $d->pay_band_max)
            <p class="text-xs text-green-700 font-medium mt-0.5">
              Pay Band: ₹{{ number_format($d->pay_band_min ?? 0, 0) }} – ₹{{ number_format($d->pay_band_max ?? 0, 0) }} / month
            </p>
            @endif
            @if($d->spatie_role)
              <span class="badge-indigo text-xs mt-0.5">Role: {{ $d->spatie_role }}</span>
            @endif
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <span class="{{ $d->is_active ? 'badge-green' : 'badge-slate' }} text-xs">{{ $d->is_active ? 'Active' : 'Off' }}</span>
            <button x-data @click="$dispatch('open-modal','edit-desig-{{ $d->id }}')" class="text-indigo-600 hover:underline text-xs">Edit</button>
          </div>
        </div>
        {{-- Edit Designation Modal --}}
        <div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='edit-desig-{{ $d->id }}')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
          <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg overflow-y-auto max-h-screen">
            <h3 class="font-semibold text-slate-700 mb-4">Edit Designation</h3>
            <form method="POST" action="{{ route('hr.designations.update', $d->id) }}" class="space-y-3">
              @csrf @method('PUT')
              <div><label class="label">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" class="input" value="{{ $d->name }}" required>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div><label class="label">Department</label>
                  <select name="department_id" class="select">
                    <option value="">None</option>
                    @foreach($departments as $dept)<option value="{{ $dept->id }}" @selected($d->department_id==$dept->id)>{{ $dept->name }}</option>@endforeach
                  </select>
                </div>
                <div><label class="label">Grade</label>
                  <input type="text" name="grade" class="input" value="{{ $d->grade }}" placeholder="e.g. A1, PB-2">
                </div>
              </div>
              <div><label class="label">Pay Scale / Level</label>
                <input type="text" name="pay_scale" class="input" value="{{ $d->pay_scale }}" placeholder="e.g. Level-7, Scale 5400-20200">
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div><label class="label">Min Pay Band (₹/month)</label>
                  <input type="number" name="pay_band_min" class="input" value="{{ $d->pay_band_min }}" step="100" min="0" placeholder="e.g. 25000">
                </div>
                <div><label class="label">Max Pay Band (₹/month)</label>
                  <input type="number" name="pay_band_max" class="input" value="{{ $d->pay_band_max }}" step="100" min="0" placeholder="e.g. 60000">
                </div>
              </div>
              <div><label class="label">Description</label>
                <textarea name="description" class="input h-14">{{ $d->description }}</textarea>
              </div>
              <div>
                <label class="label">Access Role (Spatie)</label>
                <input type="text" name="spatie_role" class="input" value="{{ $d->spatie_role }}" placeholder="e.g. teacher, principal, accountant">
                <p class="text-xs text-slate-400 mt-1">Employees with this designation will be auto-assigned this role for module access control.</p>
              </div>
              <div class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked($d->is_active)><label class="text-sm">Active</label></div>
              <div class="flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
              </div>
            </form>
          </div>
        </div>
        @empty
        <p class="text-slate-400 text-sm text-center py-4">No designations.</p>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- Add Dept Modal --}}
<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-dept')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">Add Department</h3>
    <form method="POST" action="{{ route('hr.departments.store') }}" class="space-y-3">
      @csrf
      <div><label class="label">Name <span class="text-red-500">*</span></label><input type="text" name="name" class="input" required></div>
      <div><label class="label">Code</label><input type="text" name="code" class="input" placeholder="DEPT01"></div>
      <div><label class="label">Description</label><textarea name="description" class="input h-16"></textarea></div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>

{{-- Add Designation Modal --}}
<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-desig')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg overflow-y-auto max-h-screen">
    <h3 class="font-semibold text-slate-700 mb-4">Add Designation</h3>
    <form method="POST" action="{{ route('hr.designations.store') }}" class="space-y-3">
      @csrf
      <div><label class="label">Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" class="input" required placeholder="e.g. Head of Department">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Department <span class="text-red-500">*</span></label>
          <select name="department_id" class="select" required>
            <option value="">Select</option>
            @foreach($departments as $dept)<option value="{{ $dept->id }}">{{ $dept->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="label">Grade</label>
          <input type="text" name="grade" class="input" placeholder="e.g. A1, PB-2">
        </div>
      </div>
      <div><label class="label">Pay Scale / Level</label>
        <input type="text" name="pay_scale" class="input" placeholder="e.g. Level-7, 5400-20200">
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Min Pay Band (₹/month)</label>
          <input type="number" name="pay_band_min" class="input" step="100" min="0" placeholder="e.g. 25000">
        </div>
        <div><label class="label">Max Pay Band (₹/month)</label>
          <input type="number" name="pay_band_max" class="input" step="100" min="0" placeholder="e.g. 60000">
        </div>
      </div>
      <div><label class="label">Description</label>
        <textarea name="description" class="input h-14"></textarea>
      </div>
      <div>
        <label class="label">Access Role (Spatie)</label>
        <input type="text" name="spatie_role" class="input" placeholder="e.g. teacher, principal, accountant">
        <p class="text-xs text-slate-400 mt-1">Employees assigned this designation will auto-receive this role for module access control.</p>
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Save</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
