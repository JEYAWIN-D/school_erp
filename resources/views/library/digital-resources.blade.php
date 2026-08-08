@extends('layouts.app')
@section('title','Digital Resources')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Digital Resources</h1>
    <button x-data @click="$dispatch('open-modal','add-resource')" class="btn btn-primary btn-sm">+ Upload Resource</button>
  </div>
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap">
    <select name="type" class="select w-36">
      <option value="">All Types</option>
      @foreach(['ebook'=>'E-Book','video'=>'Video','audio'=>'Audio','document'=>'Document','link'=>'Web Link'] as $k=>$v)
      <option value="{{ $k }}" @selected(request('type')===$k)>{{ $v }}</option>
      @endforeach
    </select>
    <select name="subject_id" class="select w-40">
      <option value="">All Subjects</option>
      @foreach($subjects as $s)<option value="{{ $s->id }}" @selected(request('subject_id')==$s->id)>{{ $s->name }}</option>@endforeach
    </select>
    <select name="class_id" class="select w-40">
      <option value="">All Classes</option>
      @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
    </select>
    <select name="access" class="select w-36">
      <option value="">Any Access</option>
      <option value="all" @selected(request('access')==='all')>All Classes (Global)</option>
      <option value="restricted" @selected(request('access')==='restricted')>Class-Restricted</option>
    </select>
    <input type="text" name="search" value="{{ request('search') }}" class="input flex-1" placeholder="Search title...">
    <button type="submit" class="btn btn-secondary btn-sm">Search</button>
    @if(request()->hasAny(['type','subject_id','class_id','access','search']))
      <a href="{{ route('library.digital') }}" class="btn btn-secondary btn-sm text-slate-400">Clear</a>
    @endif
  </div></form>
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($resources as $r)
    <div class="card flex flex-col justify-between">
      <div class="space-y-2">
        <div class="flex items-center gap-2 flex-wrap">
          <span class="badge-{{ match($r->type ?? '') {'ebook'=>'green','video'=>'red','audio'=>'amber',default=>'slate'} }} text-xs capitalize">{{ $r->type ?? '—' }}</span>
          @if($r->is_premium ?? false)<span class="badge-indigo text-xs">Premium</span>@endif
          @if($r->class_id)
            <span class="badge-amber text-xs" title="Restricted to this class only">
              {{ $r->class?->name ?? 'Class #'.$r->class_id }}
            </span>
          @else
            <span class="badge-green text-xs">All Classes</span>
          @endif
        </div>
        <p class="font-semibold text-slate-800 text-sm">{{ $r->title }}</p>
        @if($r->description)<p class="text-xs text-slate-400 line-clamp-2">{{ $r->description }}</p>@endif
        <div class="flex items-center gap-3 text-xs text-slate-400">
          @if($r->subject)<span>{{ $r->subject?->name }}</span>@endif
          @if($r->file_size ?? null)<span>{{ round($r->file_size/1024/1024,1) }}MB</span>@endif
          <span>{{ $r->access_count ?? $r->download_count ?? 0 }} views</span>
        </div>
      </div>
      <div class="flex gap-2 mt-3 pt-3 border-t border-slate-100">
        <a href="{{ route('library.digital.view',$r->id) }}" target="_blank" class="btn btn-secondary btn-sm flex-1 text-center text-xs">View</a>
        @if($r->file_path)
        <a href="{{ route('library.digital.download',$r->id) }}" class="btn btn-primary btn-sm text-xs">Download</a>
        @endif
        <form method="POST" action="{{ route('library.digital.delete',$r->id) }}" class="inline">@csrf @method('DELETE')
          <button type="submit" class="btn btn-secondary btn-sm text-xs text-red-400" onclick="return confirm('Delete?')">Del</button>
        </form>
      </div>
    </div>
    @empty
    <div class="col-span-3 card text-center py-12 text-slate-400">No digital resources.</div>
    @endforelse
  </div>
  @if($resources->hasPages())<div>{{ $resources->links() }}</div>@endif
</div>

<div x-data="{show:false,rtype:'ebook'}" x-on:open-modal.window="show=($event.detail==='add-resource')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg max-h-screen overflow-y-auto">
    <h3 class="font-semibold text-slate-700 mb-4">Add Digital Resource</h3>
    <form method="POST" action="{{ route('library.digital.store') }}" enctype="multipart/form-data" class="space-y-3">
      @csrf
      <div><label class="label">Title <span class="text-red-500">*</span></label><input type="text" name="title" class="input" required></div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Type</label>
          <select name="type" class="select" x-model="rtype">
            @foreach(['ebook'=>'E-Book','video'=>'Video','audio'=>'Audio','document'=>'Document','link'=>'Web Link'] as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div><label class="label">Subject</label>
          <select name="subject_id" class="select">
            <option value="">None</option>
            @foreach($subjects as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
          </select>
        </div>
      </div>
      <div>
        <label class="label">Access Control</label>
        <select name="class_id" class="select">
          <option value="">Available to All Classes</option>
          @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }} only</option>@endforeach
        </select>
        <p class="text-xs text-slate-400 mt-1">Leave blank to make this resource accessible to all classes.</p>
      </div>
      <div x-show="rtype!=='link'"><label class="label">Upload File</label><input type="file" name="file" class="input"></div>
      <div x-show="rtype==='link'"><label class="label">URL</label><input type="url" name="url" class="input" placeholder="https://..."></div>
      <div><label class="label">Description</label><textarea name="description" class="input h-16"></textarea></div>
      <div class="flex items-center gap-2"><input type="checkbox" name="is_premium" value="1"><label class="text-sm text-slate-600">Premium (restricted access)</label></div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Upload</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
