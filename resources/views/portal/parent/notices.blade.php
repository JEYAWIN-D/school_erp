@extends('portal.layout')
@section('title', 'Notice Board')
@section('content')

<h2 style="font-size: 1.0625rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Notice Board</h2>

<div style="display: flex; flex-direction: column; gap: .75rem;">
  @forelse($notices as $n)
  @php $isRead = isset($n->read_at) && $n->read_at; @endphp
  <div class="portal-card notice-card" data-notice-id="{{ $n->id }}"
       style="cursor: pointer; transition: opacity .2s; {{ $isRead ? 'opacity:.75;' : '' }}">
    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;">
      <div style="flex: 1; min-width: 0;">
        <div style="margin-bottom: .375rem; display: flex; align-items: center; flex-wrap: wrap; gap: .375rem;">
          @if(isset($n->priority) && $n->priority === 'urgent')
            <span class="badge-red">Urgent</span>
          @endif
          @if(!$isRead)
            <span style="width:.5rem;height:.5rem;border-radius:50%;background:#3b82f6;display:inline-block;flex-shrink:0;" title="Unread"></span>
          @endif
          <span style="font-size: .9375rem; font-weight: {{ $isRead ? '500' : '600' }}; color: #1e293b;">{{ $n->title }}</span>
        </div>
        @if(($n->body ?? null) || ($n->description ?? null))
          <p style="font-size: .8125rem; color: #64748b; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">{{ strip_tags($n->body ?? $n->description ?? '') }}</p>
        @endif
        @if(isset($n->attachment) && $n->attachment)
          <a href="{{ Storage::url($n->attachment) }}" target="_blank" onclick="event.stopPropagation();"
             style="display: inline-flex; align-items: center; gap: .25rem; margin-top: .5rem; font-size: .75rem; color: #3b82f6; text-decoration: none; font-weight: 500;">
            <svg style="width:.875rem;height:.875rem" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
            View Attachment
          </a>
        @endif
      </div>
      <div style="display: flex; flex-direction: column; align-items: flex-end; gap: .25rem; flex-shrink: 0;">
        <span style="font-size: .75rem; color: #94a3b8; white-space: nowrap;">{{ \Carbon\Carbon::parse($n->created_at)->format('d M Y') }}</span>
        @if($isRead)
          <span style="font-size: .7rem; color: #94a3b8;">Read</span>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="portal-card" style="text-align: center; padding: 3rem 1rem;">
    <svg style="width: 3rem; height: 3rem; margin: 0 auto .875rem; color: #e2e8f0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
    <p style="color: #94a3b8; font-size: .875rem;">No notices at this time</p>
  </div>
  @endforelse
</div>

@if($notices->hasPages())
<div style="margin-top: 1.25rem;">{{ $notices->links() }}</div>
@endif

@push('scripts')
<script>
(function() {
  var csrf = document.querySelector('meta[name="csrf-token"]').content;
  document.querySelectorAll('.notice-card').forEach(function(card) {
    card.addEventListener('click', function() {
      var id = card.dataset.noticeId;
      var dot = card.querySelector('span[title="Unread"]');
      if (!dot) return;
      fetch('/portal/notice/' + id + '/read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' }
      }).then(function() {
        dot.remove();
        card.style.opacity = '.75';
        var title = card.querySelector('span[style*="font-weight: 600"]') || card.querySelector('span[style*="font-weight:600"]');
        if (title) title.style.fontWeight = '500';
      });
    });
  });
})();
</script>
@endpush
@endsection
