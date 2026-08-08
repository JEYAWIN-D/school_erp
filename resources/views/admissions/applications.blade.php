@extends('layouts.app')
@section('title', 'Submitted Applications')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Submitted Applications</h1>
    <a href="{{ route('admissions.form-builder') }}" class="btn btn-secondary btn-sm">← Form Builder</a>
  </div>

  @if(session('success'))<div class="alert-success">{{ session('success') }}</div>@endif

  {{-- Filters --}}
  <form method="GET" class="card-flat py-3 flex flex-wrap gap-3 items-end">
    <div>
      <label class="label text-xs">Form</label>
      <select name="form_config_id" class="select text-sm py-1.5" onchange="this.form.submit()">
        <option value="">All Forms</option>
        @foreach($configs as $c)<option value="{{ $c->id }}" @selected(request('form_config_id')==$c->id)>{{ $c->title }}</option>@endforeach
      </select>
    </div>
    <div>
      <label class="label text-xs">Status</label>
      <select name="status" class="select text-sm py-1.5" onchange="this.form.submit()">
        <option value="">All</option>
        <option value="submitted"     @selected(request('status')=='submitted')>Submitted</option>
        <option value="under_review"  @selected(request('status')=='under_review')>Under Review</option>
        <option value="shortlisted"   @selected(request('status')=='shortlisted')>Shortlisted</option>
        <option value="rejected"      @selected(request('status')=='rejected')>Rejected</option>
        <option value="admitted"      @selected(request('status')=='admitted')>Admitted</option>
      </select>
    </div>
    <div class="flex-1">
      <label class="label text-xs">Search</label>
      <input type="text" name="search" value="{{ request('search') }}" class="input text-sm py-1.5" placeholder="Name, application no, mobile...">
    </div>
    <button type="submit" class="btn btn-secondary btn-sm">Search</button>
  </form>

  <div class="table-wrap">
    <table class="w-full">
      <thead><tr>
        <th class="th">App. No.</th>
        <th class="th">Student</th>
        <th class="th">Parent</th>
        <th class="th">Form / Class</th>
        <th class="th">Status</th>
        <th class="th">Documents</th>
        <th class="th">Submitted</th>
        <th class="th">Actions</th>
      </tr></thead>
      <tbody>
        @forelse($apps as $app)
        <tr class="tr">
          <td class="td font-mono text-sm font-semibold">{{ $app->application_number }}</td>
          <td class="td">
            <p class="font-medium text-slate-800 text-sm">{{ $app->student_name ?? ($app->form_data['student_name'] ?? '—') }}</p>
          </td>
          <td class="td text-sm">
            <p>{{ $app->parent_name ?? ($app->form_data['parent_name'] ?? '—') }}</p>
            <p class="text-xs text-slate-400">{{ $app->parent_mobile }}</p>
          </td>
          <td class="td text-sm text-slate-500">{{ $app->formConfig?->title }}</td>
          <td class="td">
            @php $sc = ['submitted'=>'indigo','under_review'=>'amber','shortlisted'=>'green','rejected'=>'red','admitted'=>'teal']; @endphp
            <span class="badge-{{ $sc[$app->status] ?? 'slate' }} text-xs capitalize">{{ str_replace('_',' ',$app->status) }}</span>
          </td>
          <td class="td text-xs text-slate-500">{{ count($app->documents ?? []) }} file(s)</td>
          <td class="td text-xs text-slate-400">{{ $app->created_at->format('d M Y') }}</td>
          <td class="td">
            <div x-data="{ open: false }">
              <button @click="open=!open" class="btn btn-xs btn-secondary">Review</button>
              <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center" @click.self="open=false">
                <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg max-h-screen overflow-y-auto">
                  <h3 class="font-semibold text-slate-700 mb-4">Application: {{ $app->application_number }}</h3>
                  <dl class="space-y-2 text-sm mb-4">
                    @foreach($app->form_data ?? [] as $k => $v)
                    <div class="flex gap-2">
                      <dt class="text-slate-400 w-40 shrink-0">{{ ucwords(str_replace('_',' ',$k)) }}</dt>
                      <dd class="font-medium text-slate-700">{{ $v }}</dd>
                    </div>
                    @endforeach
                  </dl>
                  @if(!empty($app->documents))
                  <div class="border-t border-slate-100 pt-3 mb-4">
                    <p class="text-xs font-semibold text-slate-500 mb-2">Documents</p>
                    @foreach($app->documents as $name => $path)
                    <a href="{{ Storage::url($path) }}" target="_blank" class="text-indigo-600 text-xs hover:underline block">{{ ucwords(str_replace('_',' ',$name)) }}</a>
                    @endforeach
                  </div>
                  @endif
                  <form method="POST" action="{{ route('admissions.applications.status', $app->id) }}" class="space-y-3 border-t border-slate-100 pt-3">
                    @csrf
                    <select name="status" class="select text-sm">
                      @foreach(['submitted'=>'Submitted','under_review'=>'Under Review','shortlisted'=>'Shortlisted','rejected'=>'Rejected','admitted'=>'Admitted'] as $v=>$l)
                      <option value="{{ $v }}" @selected($app->status===$v)>{{ $l }}</option>
                      @endforeach
                    </select>
                    <textarea name="admin_notes" class="input text-sm" rows="2" placeholder="Admin notes...">{{ $app->admin_notes }}</textarea>
                    <div class="flex gap-2">
                      <button type="submit" class="btn btn-primary btn-sm">Save</button>
                      <a href="{{ route('admissions.applications.pdf', $app->id) }}" class="btn btn-secondary btn-sm">PDF</a>
                      <button type="button" @click="open=false" class="btn btn-secondary btn-sm">Close</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="td text-center py-8 text-slate-400">No applications submitted yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($apps->hasPages())<div class="mt-4">{{ $apps->links() }}</div>@endif
</div>
@endsection
