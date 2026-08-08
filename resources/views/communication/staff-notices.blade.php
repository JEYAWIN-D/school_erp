@extends('layouts.app')
@section('title', 'Staff Notice Board')
@section('content')
<div class="space-y-6" x-data>
  <div class="flex items-center justify-between flex-wrap gap-3">
    <h1 class="page-title">Staff Notice Board</h1>
    <a href="{{ route('communication.index') }}" class="btn-secondary btn-sm">← Back</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Post new notice --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Post New Notice</h3>
      <form method="POST" action="{{ route('communication.staff-notices.store') }}" class="space-y-3">
        @csrf
        <div>
          <label class="label">Title</label>
          <input type="text" name="title" value="{{ old('title') }}" class="input w-full" required>
          @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="label">Message</label>
          <textarea name="body" rows="5" class="input w-full" required>{{ old('body') }}</textarea>
        </div>
        <div>
          <label class="label">Priority</label>
          <select name="priority" class="select w-full">
            <option value="normal">Normal</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>
        <div>
          <label class="label">Target Department (optional)</label>
          <input type="text" name="target_department" value="{{ old('target_department') }}"
                 placeholder="Leave blank for all staff" class="input w-full">
        </div>
        <div>
          <label class="label">Expires At (optional)</label>
          <input type="date" name="expires_at" value="{{ old('expires_at') }}" class="input w-full">
        </div>
        <button type="submit" class="btn-primary w-full">Post Notice</button>
      </form>
    </div>

    {{-- Notices list --}}
    <div class="lg:col-span-2 space-y-3">
      @forelse($notices as $n)
      <div class="card" x-data="{ read: {{ in_array($n->id, $readIds) ? 'true' : 'false' }} }">
        <div class="flex items-start justify-between gap-3">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
              @if($n->priority === 'urgent')
                <span class="badge-red text-xs">Urgent</span>
              @endif
              <span class="font-semibold text-slate-800">{{ $n->title }}</span>
              <span x-show="!read" class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0"></span>
            </div>
            <p class="text-sm text-slate-600 whitespace-pre-line">{{ $n->body }}</p>
            <div class="flex items-center gap-3 mt-2 text-xs text-slate-400">
              <span>{{ $n->author?->name ?? '—' }}</span>
              <span>&bull;</span>
              <span>{{ \Carbon\Carbon::parse($n->created_at)->format('d M Y H:i') }}</span>
              @if($n->target_department) <span>&bull; Dept: {{ $n->target_department }}</span> @endif
              @if($n->expires_at) <span>&bull; Expires {{ \Carbon\Carbon::parse($n->expires_at)->format('d M Y') }}</span> @endif
              <a href="{{ route('communication.staff-notices.receipts', $n->id) }}" class="text-blue-500 hover:underline">Read receipts</a>
            </div>
          </div>
          <div class="flex flex-col gap-2 flex-shrink-0">
            @unless(in_array($n->id, $readIds))
            <button x-on:click="fetch('{{ route('communication.staff-notices.read', $n->id) }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}}); read = true"
                    class="btn-xs btn-secondary whitespace-nowrap">Mark Read</button>
            @endunless
            @if(auth()->user()->hasRole(['Super Admin', 'School Admin']))
            <form method="POST" action="{{ route('communication.staff-notices.delete', $n->id) }}">
              @csrf @method('DELETE')
              <button type="submit" onclick="return confirm('Delete this notice?')" class="btn-xs text-red-600 hover:bg-red-50 border border-red-200 rounded-lg px-2 py-1 w-full">Delete</button>
            </form>
            @endif
          </div>
        </div>
      </div>
      @empty
      <div class="card text-center py-10">
        <p class="text-slate-400">No staff notices at this time</p>
      </div>
      @endforelse
      <div>{{ $notices->links() }}</div>
    </div>

  </div>
</div>
@endsection
