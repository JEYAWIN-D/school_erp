@extends('layouts.app')
@section('title','Subject Management')
@section('content')
<div class="space-y-6" x-data="{editId:null,editOpen:false}">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Subject Management</h1>
    <button x-data @click="$dispatch('open-modal','add-subject')" class="btn btn-primary btn-sm">+ Add Subject</button>
  </div>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Subject','Code','Type','Classes','Status','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($subjects as $s)
        <tr class="hover:bg-slate-50" x-data="{}">
          <td class="px-4 py-3 font-medium text-slate-800">{{ $s->name }}</td>
          <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $s->code ?? '—' }}</td>
          <td class="px-4 py-3"><span class="badge-slate capitalize text-xs">{{ $s->type ?? 'theory' }}</span></td>
          <td class="px-4 py-3 text-slate-500 text-xs">{{ $s->allocations_count ?? 0 }} classes</td>
          <td class="px-4 py-3"><span class="{{ $s->is_active ? 'badge-green' : 'badge-slate' }}">{{ $s->is_active ? 'Active' : 'Off' }}</span></td>
          <td class="px-4 py-3 flex gap-2">
            <button @click="$dispatch('open-modal','edit-subject-{{ $s->id }}')" class="text-indigo-600 hover:underline text-xs">Edit</button>
            <form method="POST" action="{{ route('academics.subjects.toggle',$s->id) }}" class="inline">@csrf
              <button type="submit" class="text-amber-600 hover:underline text-xs">{{ $s->is_active ? 'Deactivate' : 'Activate' }}</button>
            </form>
          </td>
        </tr>
        {{-- Edit modal for this row --}}
        <div x-data x-show="false" x-on:open-modal.window="$el.style.display = ($event.detail === 'edit-subject-{{ $s->id }}') ? 'block' : $el.style.display"
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
          <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md" @click.away="$el.style.display='none'">
            <h3 class="font-semibold text-slate-700 mb-4">Edit Subject</h3>
            <form method="POST" action="{{ route('academics.subjects.update',$s->id) }}" class="space-y-3">
              @csrf @method('PUT')
              <div><label class="label">Name</label><input type="text" name="name" class="input" value="{{ $s->name }}" required></div>
              <div class="grid grid-cols-2 gap-3">
                <div><label class="label">Code</label><input type="text" name="code" class="input" value="{{ $s->code }}"></div>
                <div><label class="label">Type</label>
                  <select name="type" class="select">
                    @foreach(['theory'=>'Theory','practical'=>'Practical','activity'=>'Activity','language'=>'Language'] as $k=>$v)
                    <option value="{{ $k }}" @selected($s->type===$k)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="flex gap-2 pt-2">
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                <button type="button" @click="$el.closest('.fixed').style.display='none'" class="btn btn-secondary btn-sm">Cancel</button>
              </div>
            </form>
          </div>
        </div>
        @empty
        <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No subjects found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($subjects->hasPages())<div class="px-4 pb-3">{{ $subjects->links() }}</div>@endif
  </div>
</div>

{{-- Add Subject Modal --}}
<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-subject')" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden" style="display:none" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">Add New Subject</h3>
    <form method="POST" action="{{ route('academics.subjects.store') }}" class="space-y-3">
      @csrf
      <div><label class="label">Name <span class="text-red-500">*</span></label><input type="text" name="name" class="input" required></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Code</label><input type="text" name="code" class="input"></div>
        <div><label class="label">Type</label>
          <select name="type" class="select">
            @foreach(['theory'=>'Theory','practical'=>'Practical','activity'=>'Activity','language'=>'Language'] as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div><label class="label">Medium of Instruction</label>
        <select name="medium" class="select">
          <option value="english">English</option>
          <option value="tamil">Tamil</option>
          <option value="hindi">Hindi</option>
        </select>
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Save Subject</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
