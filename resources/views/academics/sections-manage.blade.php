@extends('layouts.app')
@section('title', 'Class & Section Management')
@section('content')
<div class="space-y-6">

  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Class & Section Management</h1>
      <p class="page-subtitle">Rename classes, manage sections, merge or split student groups</p>
    </div>
    <a href="{{ route('academics.index') }}" class="btn btn-secondary btn-sm">← Academics</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert-danger">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  {{-- ── Class list with rename ─────────────────────────────────────────────── --}}
  <div class="card" x-data="{ renaming: null }">
    <h2 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">All Classes</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
      @foreach($classes as $cls)
      <div class="border border-slate-200 rounded-xl p-3">
        <div class="flex items-center justify-between gap-2">
          <div>
            <span class="font-semibold text-slate-800">{{ $cls->name }}</span>
            <span class="text-xs text-slate-400 ml-1">({{ $cls->sections->count() }} section{{ $cls->sections->count() != 1 ? 's' : '' }})</span>
          </div>
          <button type="button" @click="renaming = (renaming === {{ $cls->id }} ? null : {{ $cls->id }})"
                  class="btn-xs bg-amber-50 text-amber-700 hover:bg-amber-100 rounded px-2 py-0.5 text-xs">
            Rename
          </button>
        </div>

        <div x-show="renaming === {{ $cls->id }}" x-transition class="mt-3 border-t border-slate-100 pt-3">
          <form method="POST" action="{{ route('classes.rename', $cls->id) }}" class="flex gap-2">
            @csrf @method('PUT')
            <input type="text" name="name" value="{{ $cls->name }}" required
                   class="input input-sm flex-1 text-sm" placeholder="New class name">
            <button type="submit" class="btn btn-primary btn-sm text-xs">Save</button>
            <button type="button" @click="renaming = null" class="btn btn-secondary btn-sm text-xs">Cancel</button>
          </form>
        </div>

        @if($cls->sections->isNotEmpty())
        <div class="mt-2 flex flex-wrap gap-1">
          @foreach($cls->sections as $sec)
          <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full
                {{ $sec->is_active ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-400 line-through' }}">
            {{ $sec->name }}
            @if($sec->classTeacher)
              <span class="text-slate-400">· {{ $sec->classTeacher->name }}</span>
            @endif
          </span>
          @endforeach
        </div>
        @endif
      </div>
      @endforeach
    </div>
  </div>

  {{-- ── Section detail panel ─────────────────────────────────────────────── --}}
  <div class="card">
    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
      <h2 class="font-semibold text-slate-700">Section Details & Tools</h2>
      <form method="GET" class="flex gap-2 items-end">
        <div>
          <label class="label text-xs">Select Class</label>
          <select name="class_id" class="select text-sm" onchange="this.form.submit()">
            <option value="">— Choose Class —</option>
            @foreach($classes as $cls)
              <option value="{{ $cls->id }}" @selected($selectedClass?->id == $cls->id)>{{ $cls->name }}</option>
            @endforeach
          </select>
        </div>
      </form>
    </div>

    @if(!$selectedClass)
      <p class="text-slate-400 text-sm text-center py-8">Select a class above to manage its sections.</p>
    @else

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
      <div class="card-flat text-center py-3">
        <p class="text-2xl font-bold text-indigo-600">{{ $sections->count() }}</p>
        <p class="text-xs text-slate-500 mt-0.5">Sections</p>
      </div>
      <div class="card-flat text-center py-3">
        <p class="text-2xl font-bold text-green-600">{{ $sections->where('is_active', true)->count() }}</p>
        <p class="text-xs text-slate-500 mt-0.5">Active</p>
      </div>
      <div class="card-flat text-center py-3">
        <p class="text-2xl font-bold text-slate-700">{{ $sections->sum('active_students_count') }}</p>
        <p class="text-xs text-slate-500 mt-0.5">Total Students</p>
      </div>
      <div class="card-flat text-center py-3">
        <p class="text-2xl font-bold text-amber-600">{{ $sections->sum('capacity') }}</p>
        <p class="text-xs text-slate-500 mt-0.5">Total Capacity</p>
      </div>
    </div>

    <div x-data="{ editId: null, mergeId: null, splitId: null }" class="overflow-x-auto">
      <table class="table-wrap w-full text-sm mb-4">
        <thead>
          <tr>
            <th class="th">Section</th>
            <th class="th">Class Teacher</th>
            <th class="th">Co-Teacher</th>
            <th class="th text-center">Students</th>
            <th class="th text-center">Capacity</th>
            <th class="th text-center">Status</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sections as $sec)
          <tr class="tr">
            <td class="td font-semibold text-slate-800">{{ $sec->name }}</td>
            <td class="td text-slate-600">{{ $sec->classTeacher?->name ?? '—' }}</td>
            <td class="td text-slate-500 text-xs">{{ $sec->coClassTeacher?->name ?? '—' }}</td>
            <td class="td text-center">
              <span class="font-semibold {{ $sec->active_students_count >= $sec->capacity ? 'text-red-600' : 'text-slate-700' }}">
                {{ $sec->active_students_count }}
              </span>
            </td>
            <td class="td text-center text-slate-500">{{ $sec->capacity }}</td>
            <td class="td text-center">
              @if($sec->is_active)
                <span class="badge-green">Active</span>
              @else
                <span class="badge-slate">Inactive</span>
              @endif
            </td>
            <td class="td">
              <div class="flex gap-1 flex-wrap">
                <button type="button"
                        @click="editId = (editId === {{ $sec->id }} ? null : {{ $sec->id }}); mergeId = null; splitId = null"
                        class="btn-xs bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded px-2 py-0.5 text-xs">Edit</button>
                @if($sec->is_active && $sections->where('is_active', true)->count() > 1)
                <button type="button"
                        @click="mergeId = (mergeId === {{ $sec->id }} ? null : {{ $sec->id }}); editId = null; splitId = null"
                        class="btn-xs bg-amber-50 text-amber-700 hover:bg-amber-100 rounded px-2 py-0.5 text-xs">Merge</button>
                @if($sec->active_students_count > 1)
                <button type="button"
                        @click="splitId = (splitId === {{ $sec->id }} ? null : {{ $sec->id }}); editId = null; mergeId = null"
                        class="btn-xs bg-purple-50 text-purple-700 hover:bg-purple-100 rounded px-2 py-0.5 text-xs">Split</button>
                @endif
                @endif
                @if($sec->active_students_count === 0 && $sec->is_active)
                <form method="POST" action="{{ route('sections.deactivate-empty', $sec->id) }}"
                      onsubmit="return confirm('Deactivate empty section {{ $sec->name }}?')">
                  @csrf
                  <button type="submit" class="btn-xs bg-red-50 text-red-600 hover:bg-red-100 rounded px-2 py-0.5 text-xs">Deactivate</button>
                </form>
                @endif
              </div>
            </td>
          </tr>

          {{-- Edit inline --}}
          <tr x-show="editId === {{ $sec->id }}" x-transition class="bg-indigo-50/40">
            <td colspan="7" class="td">
              <form method="POST" action="{{ route('sections.update', $sec->id) }}"
                    class="grid grid-cols-2 sm:grid-cols-5 gap-3 py-2">
                @csrf @method('PUT')
                <div>
                  <label class="label text-xs">Section Name</label>
                  <input type="text" name="name" value="{{ $sec->name }}" class="input text-sm" required>
                </div>
                <div>
                  <label class="label text-xs">Capacity</label>
                  <input type="number" name="capacity" value="{{ $sec->capacity }}" class="input text-sm" min="1" max="200">
                </div>
                <div>
                  <label class="label text-xs">Class Teacher</label>
                  <select name="class_teacher_id" class="select text-xs">
                    <option value="">None</option>
                    @foreach($teachers as $t)
                      <option value="{{ $t->id }}" @selected($sec->class_teacher_id == $t->id)>{{ $t->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div>
                  <label class="label text-xs">Co-Class Teacher</label>
                  <select name="co_class_teacher_id" class="select text-xs">
                    <option value="">None</option>
                    @foreach($teachers as $t)
                      <option value="{{ $t->id }}" @selected($sec->co_class_teacher_id == $t->id)>{{ $t->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="flex items-end gap-2">
                  <label class="flex items-center gap-1 text-xs text-slate-600 mb-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked($sec->is_active)
                           class="rounded border-slate-300 text-indigo-600">
                    Active
                  </label>
                  <button type="submit" class="btn btn-primary btn-sm mb-2">Save</button>
                </div>
              </form>
            </td>
          </tr>

          {{-- Merge inline --}}
          <tr x-show="mergeId === {{ $sec->id }}" x-transition class="bg-amber-50/40">
            <td colspan="7" class="td">
              <div class="py-2">
                <p class="text-xs text-amber-700 font-medium mb-2">
                  Move all <strong>{{ $sec->active_students_count }}</strong> active student(s) from
                  <strong>"{{ $sec->name }}"</strong> into another section, then deactivate this section.
                </p>
                <form method="POST" action="{{ route('sections.merge', $sec->id) }}"
                      onsubmit="return confirm('Move all students from {{ $sec->name }} and deactivate it?')"
                      class="flex gap-2 items-end flex-wrap">
                  @csrf
                  <div>
                    <label class="label text-xs">Target Section</label>
                    <select name="target_section_id" class="select text-sm" required>
                      <option value="">— Select target —</option>
                      @foreach($sections->where('is_active', true)->where('id', '!=', $sec->id) as $other)
                        <option value="{{ $other->id }}">
                          {{ $other->name }} ({{ $other->active_students_count }}/{{ $other->capacity }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <button type="submit" class="btn bg-amber-500 hover:bg-amber-600 text-white btn-sm">Merge & Deactivate</button>
                  <button type="button" @click="mergeId = null" class="btn btn-secondary btn-sm">Cancel</button>
                </form>
              </div>
            </td>
          </tr>

          {{-- Split inline --}}
          <tr x-show="splitId === {{ $sec->id }}" x-transition class="bg-purple-50/40">
            <td colspan="7" class="td">
              <div class="py-2">
                <p class="text-xs text-purple-700 font-medium mb-2">
                  Move the first N students (ordered by roll number) from
                  <strong>"{{ $sec->name }}"</strong> ({{ $sec->active_students_count }} active) into a new section.
                </p>
                <form method="POST" action="{{ route('sections.split', $sec->id) }}"
                      class="flex gap-3 items-end flex-wrap">
                  @csrf
                  <div>
                    <label class="label text-xs">New Section Name</label>
                    <input type="text" name="new_section_name" class="input text-sm" required placeholder="e.g. B2">
                  </div>
                  <div>
                    <label class="label text-xs">
                      Students to Move
                      <span class="text-slate-400">(max {{ max(1, $sec->active_students_count - 1) }})</span>
                    </label>
                    <input type="number" name="move_count" class="input text-sm w-24"
                           min="1" max="{{ max(1, $sec->active_students_count - 1) }}"
                           value="{{ (int)($sec->active_students_count / 2) }}" required>
                  </div>
                  <button type="submit" class="btn bg-purple-600 hover:bg-purple-700 text-white btn-sm">Create & Split</button>
                  <button type="button" @click="splitId = null" class="btn btn-secondary btn-sm">Cancel</button>
                </form>
              </div>
            </td>
          </tr>

          @empty
          <tr class="tr">
            <td colspan="7" class="td text-center text-slate-400">No sections found for this class.</td>
          </tr>
          @endforelse
        </tbody>
      </table>

      {{-- Add new section --}}
      <div x-data="{ open: false }">
        <button type="button" @click="open = !open" class="btn btn-secondary btn-sm">
          + Add New Section
        </button>
        <div x-show="open" x-transition class="mt-3 border border-slate-200 rounded-xl p-4 bg-slate-50">
          <h3 class="text-sm font-medium text-slate-700 mb-3">Create Section in {{ $selectedClass->name }}</h3>
          <form method="POST" action="{{ route('sections.create') }}"
                class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            @csrf
            <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
            <div>
              <label class="label text-xs">Section Name <span class="text-red-500">*</span></label>
              <input type="text" name="name" class="input text-sm" required placeholder="e.g. C">
            </div>
            <div>
              <label class="label text-xs">Capacity <span class="text-red-500">*</span></label>
              <input type="number" name="capacity" class="input text-sm" value="40" min="1" max="200">
            </div>
            <div>
              <label class="label text-xs">Class Teacher</label>
              <select name="class_teacher_id" class="select text-xs">
                <option value="">None</option>
                @foreach($teachers as $t)
                  <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="label text-xs">Co-Class Teacher</label>
              <select name="co_class_teacher_id" class="select text-xs">
                <option value="">None</option>
                @foreach($teachers as $t)
                  <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="flex items-end">
              <button type="submit" class="btn btn-primary btn-sm w-full">Create</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    @endif
  </div>

</div>
@endsection
