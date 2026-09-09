@extends('layouts.app')
@section('title', 'Subjects')
@section('content')
@push('modals')
<div x-data="{show:false}" @open-modal.window="if($event.detail==='add-subject') show=true" x-show="show" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="show=false">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
      <h2 class="font-semibold text-slate-800">Add Subject</h2>
      <button @click="show=false" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
    </div>
    <form method="POST" action="{{ route('academics.subjects.store') }}" class="px-6 py-5 space-y-4">
      @csrf
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="label">Subject Name *</label>
          <input type="text" name="name" required class="input w-full" placeholder="e.g. Mathematics">
        </div>
        <div>
          <label class="label">Subject Code</label>
          <input type="text" name="code" class="input w-full" placeholder="e.g. MATH01">
        </div>
        <div>
          <label class="label">Class</label>
          <select name="class_id" class="select w-full">
            <option value="">All Classes</option>
            @foreach($classes ?? [] as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Handling Teacher</label>
          <select name="teacher_id" class="select w-full">
            <option value="">— Select Teacher —</option>
            @foreach($teachers ?? [] as $t)
              <option value="{{ $t->id }}">{{ $t->full_name ?: $t->first_name . ' ' . $t->last_name }} ({{ $t->employee_code ?: 'EMP-' . $t->id }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Subject Type</label>
          <select name="type" class="select w-full">
            <option value="theory">Theory</option>
            <option value="practical">Practical</option>
            <option value="language">Language</option>
          </select>
        </div>
        <div>
          <label class="label">Max Marks</label>
          <input type="number" name="max_marks" value="100" min="0" class="input w-full">
        </div>
        <div>
          <label class="label">Pass Marks</label>
          <input type="number" name="pass_marks" value="35" min="0" class="input w-full">
        </div>
        <div>
          <label class="label">Board / Curriculum</label>
          <select name="board_curriculum" class="select w-full">
            <option value="cbse">CBSE</option>
            <option value="state">State Board</option>
            <option value="both">Both</option>
          </select>
        </div>
        <div>
          <label class="label">Medium</label>
          <select name="medium" class="select w-full">
            <option value="english">English</option>
            <option value="tamil">Tamil</option>
            <option value="both">Both</option>
          </select>
        </div>
        <div>
          <label class="label">Language Type</label>
          <select name="language_type" class="select w-full">
            <option value="none">Not a Language</option>
            <option value="first">1st Language</option>
            <option value="second">2nd Language</option>
            <option value="third">3rd Language</option>
          </select>
        </div>
        <div>
          <label class="label">Stream (XI–XII)</label>
          <select name="stream" class="select w-full">
            <option value="">No Stream</option>
            <option value="science">Science</option>
            <option value="commerce">Commerce</option>
            <option value="arts">Arts</option>
            <option value="vocational">Vocational</option>
          </select>
        </div>
        <div>
          <label class="label">Credit Hours</label>
          <input type="number" name="credit_hours" value="0" min="0" max="40" class="input w-full">
        </div>
        <div>
          <label class="label">Sort Order</label>
          <input type="number" name="sort_order" value="0" min="0" class="input w-full">
        </div>
      </div>
      <div class="flex gap-6 text-sm">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_elective" value="1" class="rounded"> <span>Elective Subject</span></label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_coscholastic" value="1" class="rounded"> <span>Co-Scholastic (graded A–D)</span></label>
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded"> <span>Active</span></label>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
        <button type="submit" class="btn btn-primary btn-sm">Add Subject</button>
      </div>
    </form>
  </div>
</div>
@endpush
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Subjects &amp; Faculty Handling</h1>
    <button x-data @click="$dispatch('open-modal','add-subject')" class="btn btn-primary btn-sm">+ Add Subject</button>
  </div>
  <form method="GET" class="card-flat py-3">
    <div class="flex gap-3">
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Search subject..." class="input w-48">
      <select name="type" class="select w-36">
        <option value="">All Types</option>
        <option value="theory" @selected(request('type')==='theory')>Theory</option>
        <option value="practical" @selected(request('type')==='practical')>Practical</option>
        <option value="language" @selected(request('type')==='language')>Language</option>
      </select>
      <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    </div>
  </form>
  <div class="card overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        @foreach(['Subject','Code','Class','Handling Teacher(s)','Type','Board / Medium','Credits','Flags','Status','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 font-medium text-xs uppercase tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($subjects as $s)
        <tr class="hover:bg-slate-50" x-data="{edit:false}">
          <td class="px-4 py-3 font-medium text-slate-800">
            {{ $s->name }}
            @if($s->stream)<span class="ml-1 badge-purple text-xs">{{ ucfirst($s->stream) }}</span>@endif
          </td>
          <td class="px-4 py-3 font-mono text-slate-500 text-xs">{{ $s->code ?? '—' }}</td>
          <td class="px-4 py-3 text-xs font-semibold text-slate-600">{{ $s->class?->name ?? 'All Classes' }}</td>
          <td class="px-4 py-3">
            @php
              $allocatedTeachers = $s->allocations->map(fn($a) => $a->employee)->filter()->unique('id');
            @endphp
            @if($allocatedTeachers->isNotEmpty())
              <div class="flex flex-wrap gap-1">
                @foreach($allocatedTeachers as $at)
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <svg class="w-3 h-3 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>{{ $at->full_name ?: $at->first_name . ' ' . $at->last_name }}</span>
                  </span>
                @endforeach
              </div>
            @else
              <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                Unassigned
              </span>
            @endif
          </td>
          <td class="px-4 py-3"><span class="badge-slate capitalize">{{ $s->type ?? 'theory' }}</span></td>
          <td class="px-4 py-3 text-xs text-slate-500">
            <span class="font-medium text-slate-700">{{ strtoupper($s->board_curriculum ?? 'cbse') }}</span>
            <span class="text-slate-400">·</span>
            <span class="capitalize">{{ $s->medium ?? 'english' }}</span>
          </td>
          <td class="px-4 py-3 text-center text-xs text-slate-500">{{ $s->credit_hours ?? 0 }}</td>
          <td class="px-4 py-3">
            @if($s->is_elective)<span class="badge-amber text-xs">Elective</span>@endif
            @if($s->is_coscholastic)<span class="badge-blue text-xs">Co-Sch</span>@endif
            @if($s->language_type !== 'none')<span class="badge-green text-xs">{{ ucfirst($s->language_type) }} Lang</span>@endif
          </td>
          <td class="px-4 py-3"><span class="{{ $s->is_active ? 'badge-green' : 'badge-slate' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
          <td class="px-4 py-3">
            <div class="flex gap-1">
              <button @click="edit=!edit" class="btn btn-secondary btn-xs">Edit</button>
              <form method="POST" action="{{ route('academics.subjects.toggle', $s->id) }}" class="inline">@csrf
                <button class="btn btn-ghost btn-xs text-slate-500">{{ $s->is_active ? 'Deactivate' : 'Activate' }}</button>
              </form>
            </div>
            <div x-show="edit" x-transition class="mt-2 p-3 bg-slate-50 rounded-xl border border-slate-200 shadow-sm">
              <form method="POST" action="{{ route('academics.subjects.update', $s->id) }}" class="space-y-2">
                @csrf @method('PUT')
                <div class="grid grid-cols-2 gap-2">
                  <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase">Subject Name</label>
                    <input type="text" name="name" value="{{ $s->name }}" class="input text-xs py-1 w-full" placeholder="Name">
                  </div>
                  <div>
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase">Subject Code</label>
                    <input type="text" name="code" value="{{ $s->code }}" class="input text-xs py-1 w-full" placeholder="Code">
                  </div>
                  <div class="col-span-2">
                    <label class="block text-[10px] font-semibold text-slate-500 uppercase">Assigned Handling Teacher</label>
                    <select name="teacher_id" class="select text-xs py-1 w-full">
                      <option value="">— Select Handling Teacher —</option>
                      @foreach($teachers ?? [] as $t)
                        <option value="{{ $t->id }}" @selected($s->allocations->contains('employee_id', $t->id))>
                          {{ $t->full_name ?: $t->first_name . ' ' . $t->last_name }} ({{ $t->employee_code ?: 'EMP-' . $t->id }})
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <select name="board_curriculum" class="select text-xs py-1">
                    @foreach(['cbse'=>'CBSE','state'=>'State Board','both'=>'Both'] as $v=>$l)
                      <option value="{{ $v }}" @selected($s->board_curriculum===$v)>{{ $l }}</option>
                    @endforeach
                  </select>
                  <select name="medium" class="select text-xs py-1">
                    <option value="english" @selected($s->medium==='english')>English</option>
                    <option value="tamil" @selected($s->medium==='tamil')>Tamil</option>
                    <option value="both" @selected($s->medium==='both')>Both</option>
                  </select>
                  <input type="number" name="credit_hours" value="{{ $s->credit_hours }}" class="input text-xs py-1" placeholder="Credit hrs" min="0" max="40">
                  <select name="stream" class="select text-xs py-1">
                    <option value="">No Stream</option>
                    @foreach(['science','commerce','arts','vocational'] as $st)
                      <option value="{{ $st }}" @selected($s->stream===$st)>{{ ucfirst($st) }}</option>
                    @endforeach
                  </select>
                  <select name="language_type" class="select text-xs py-1">
                    @foreach(['none'=>'Not a language','first'=>'1st Language','second'=>'2nd Language','third'=>'3rd Language'] as $v=>$l)
                      <option value="{{ $v }}" @selected($s->language_type===$v)>{{ $l }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="flex gap-3 text-xs">
                  <label><input type="checkbox" name="is_elective" value="1" @checked($s->is_elective)> Elective</label>
                  <label><input type="checkbox" name="is_coscholastic" value="1" @checked($s->is_coscholastic)> Co-Scholastic</label>
                </div>
                <button class="btn btn-primary btn-xs">Save</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="10" class="px-4 py-8 text-center text-slate-400">No subjects found.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if(isset($subjects) && method_exists($subjects,'hasPages') && $subjects->hasPages())
      <div class="px-4 pb-3 text-sm">{{ $subjects->links() }}</div>
    @endif
  </div>
</div>
@endsection
