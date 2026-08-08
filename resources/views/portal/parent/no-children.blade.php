@extends('portal.layout')
@section('title', 'Parent Portal')
@section('content')
<div style="background:#fff;border-radius:12px;padding:40px;text-align:center;max-width:480px;margin:40px auto;box-shadow:0 1px 4px rgba(0,0,0,.08);">
  <svg style="width:56px;height:56px;color:#cbd5e1;margin:0 auto 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
  </svg>
  <h2 style="font-size:1.1rem;font-weight:600;color:#334155;margin-bottom:8px;">No Children Linked</h2>
  <p style="color:#94a3b8;font-size:.875rem;line-height:1.5;">
    Your account has not been linked to any student records yet. Please contact the school administration.
  </p>
</div>
@endsection
