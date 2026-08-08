@extends('layouts.app')
@section('title','Hostel Complaints')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <div>
      <h1 class="page-title">Hostel Maintenance Complaints</h1>
      <p class="page-subtitle">Track, assign, and resolve hostel maintenance issues</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('hostel.overdue-outpasses') }}" class="btn btn-secondary btn-sm">Late Returns</a>
      <a href="{{ route('hostel.complaints-report') }}" class="btn btn-secondary btn-sm">Report</a>
      <button x-data @click="$dispatch('open-modal','add-complaint')" class="btn btn-primary btn-sm">+ Raise Complaint</button>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" class="card-flat py-3"><div class="flex gap-3 flex-wrap items-end">
    <select name="status" class="select w-36">
      <option value="">All Status</option>
      <option value="open" @selected(request('status')==='open')>Open</option>
      <option value="in_progress" @selected(request('status')==='in_progress')>In Progress</option>
      <option value="resolved" @selected(request('status')==='resolved')>Resolved</option>
    </select>
    <select name="priority" class="select w-32">
      <option value="">All Priority</option>
      <option value="high" @selected(request('priority')==='high')>High</option>
      <option value="medium" @selected(request('priority')==='medium')>Medium</option>
      <option value="low" @selected(request('priority')==='low')>Low</option>
    </select>
    <select name="category" class="select w-40">
      <option value="">All Categories</option>
      @foreach(['electrical','plumbing','furniture','cleanliness','pest_control','other'] as $cat)
      <option value="{{ $cat }}" @selected(request('category')===$cat)>{{ ucfirst(str_replace('_',' ',$cat)) }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    @if(request()->hasAny(['status','priority','category']))
      <a href="{{ route('hostel.complaints') }}" class="btn btn-secondary btn-sm text-slate-400">Clear</a>
    @endif
  </div></form>

  <div class="grid grid-cols-3 gap-4 mb-2">
    @php $open=$complaints->where('status','open')->count(); $inProgress=$complaints->where('status','in_progress')->count(); $resolved=$complaints->where('status','resolved')->count(); @endphp
    <div class="card text-center py-3"><p class="text-xs text-slate-400">Open</p><p class="text-2xl font-bold text-red-600">{{ $open }}</p></div>
    <div class="card text-center py-3"><p class="text-xs text-slate-400">In Progress</p><p class="text-2xl font-bold text-amber-600">{{ $inProgress }}</p></div>
    <div class="card text-center py-3"><p class="text-xs text-slate-400">Resolved</p><p class="text-2xl font-bold text-green-600">{{ $resolved }}</p></div>
  </div>

  <div class="card overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b"><tr>
        @foreach(['Student','Room','Category','Description','Priority','Status','Assigned To / Vendor','Raised On','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase font-medium tracking-wide">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($complaints as $c)
        <tr x-data="{open:false}" class="hover:bg-slate-50">
          <td class="px-4 py-3 font-medium text-slate-800 text-sm">{{ $c->student?->full_name }}</td>
          <td class="px-4 py-3 text-slate-500 text-xs">
            {{ $c->room?->room_number ?? '—' }}
            @if($c->room_id)
              <a href="{{ route('hostel.complaints.by-room', $c->room_id) }}" class="text-indigo-500 block text-xs hover:underline">History</a>
            @endif
          </td>
          <td class="px-4 py-3"><span class="badge-slate capitalize text-xs">{{ str_replace('_',' ',$c->category ?? $c->complaint_type) }}</span></td>
          <td class="px-4 py-3 text-slate-500 text-xs max-w-xs">
            <p class="truncate">{{ $c->description }}</p>
            @if($c->resolution_notes)<p class="text-green-600 text-xs mt-1 italic truncate">✓ {{ $c->resolution_notes }}</p>@endif
          </td>
          <td class="px-4 py-3">
            <span class="text-xs font-semibold {{ ($c->priority==='high')?'text-red-600':(($c->priority==='medium')?'text-amber-600':'text-slate-400') }} capitalize">{{ $c->priority ?? 'medium' }}</span>
          </td>
          <td class="px-4 py-3"><span class="badge-{{ $c->status==='resolved'?'green':($c->status==='in_progress'?'amber':'red') }} capitalize text-xs">{{ str_replace('_',' ',$c->status) }}</span>
            @if($c->resolved_at)<p class="text-xs text-slate-400">{{ $c->resolved_at->format('d M') }}</p>@endif
          </td>
          <td class="px-4 py-3 text-slate-500 text-xs">
            @if($c->assignedTo)
              <span class="text-indigo-600">{{ $c->assignedTo->first_name }} {{ $c->assignedTo->last_name }}</span>
            @elseif($c->vendor_name)
              <span class="text-amber-600">{{ $c->vendor_name }}</span>
            @else
              <span class="text-slate-300">—</span>
            @endif
          </td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $c->created_at->format('d M') }}</td>
          <td class="px-4 py-3">
            <button @click="open=!open" class="text-xs text-indigo-500 hover:underline">{{ $c->status==='resolved'?'View':'Edit' }}</button>
          </td>
        </tr>
        <tr x-show="open" style="display:none" class="bg-indigo-50/40">
          <td colspan="9" class="px-4 pb-4 pt-2">
            <form method="POST" action="{{ route('hostel.complaints.update', $c->id) }}" class="grid grid-cols-2 md:grid-cols-4 gap-3 items-end">
              @csrf @method('PATCH')
              <div>
                <label class="label">Status</label>
                <select name="status" class="select text-sm">
                  <option value="open" @selected($c->status==='open')>Open</option>
                  <option value="in_progress" @selected($c->status==='in_progress')>In Progress</option>
                  <option value="resolved" @selected($c->status==='resolved')>Resolved</option>
                </select>
              </div>
              <div>
                <label class="label">Assign to Staff</label>
                <select name="assigned_to" class="select text-sm">
                  <option value="">None</option>
                  @foreach($employees as $emp)
                  <option value="{{ $emp->id }}" @selected($c->assigned_to==$emp->id)>{{ $emp->first_name }} {{ $emp->last_name }}@if($emp->designation) ({{ $emp->designation }})@endif</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="label">Assign to Vendor</label>
                <input type="text" name="vendor_name" value="{{ $c->vendor_name }}" class="input text-sm" placeholder="Vendor / contractor name">
              </div>
              <div>
                <label class="label">Resolution Notes</label>
                <input type="text" name="resolution_notes" value="{{ $c->resolution_notes }}" class="input text-sm" placeholder="What was done...">
              </div>
              <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                <button type="button" @click="open=false" class="btn btn-secondary btn-sm">Cancel</button>
              </div>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">No complaints.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($complaints->hasPages())<div class="px-4 pb-3">{{ $complaints->links() }}</div>@endif
  </div>
</div>

<div x-data="{show:false}" x-on:open-modal.window="show=($event.detail==='add-complaint')" x-show="show" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.away="show=false">
  <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
    <h3 class="font-semibold text-slate-700 mb-4">Raise Complaint</h3>
    <form method="POST" action="{{ route('hostel.complaints.store') }}" class="space-y-3">
      @csrf
      <div><label class="label">Student <span class="text-red-500">*</span></label>
        <select name="student_id" class="select" required>
          <option value="">Select</option>
          @foreach($students as $s)<option value="{{ $s->id }}">{{ $s->full_name }}</option>@endforeach
        </select>
      </div>
      <div class="grid grid-cols-2 gap-3">
        <div><label class="label">Category</label>
          <select name="category" class="select">
            @foreach(['electrical'=>'Electrical','plumbing'=>'Plumbing','furniture'=>'Furniture','cleanliness'=>'Cleanliness','pest_control'=>'Pest Control','other'=>'Other'] as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div><label class="label">Priority</label>
          <select name="priority" class="select">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
          </select>
        </div>
      </div>
      <div><label class="label">Description <span class="text-red-500">*</span></label>
        <textarea name="description" class="input h-20" required></textarea>
      </div>
      <div class="flex gap-2 pt-2">
        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
        <button type="button" @click="show=false" class="btn btn-secondary btn-sm">Cancel</button>
      </div>
    </form>
  </div>
</div>
@endsection
