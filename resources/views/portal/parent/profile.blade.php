@extends('portal.layout')
@section('title', 'My Profile')
@section('content')

<h2 style="font-size: 1.0625rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">My Profile</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1rem;">

  {{-- Contact details --}}
  <div class="portal-card">
    <div class="section-title">
      <svg style="width:1rem;height:1rem;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
      Contact Details
    </div>
    <form method="POST" action="{{ route('portal.parent.profile.update') }}">
      @csrf @method('PUT')
      <div style="display: flex; flex-direction: column; gap: .75rem;">
        <div>
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Full Name</label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; color: #1e293b; outline: none; box-sizing: border-box;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
          @error('name') <p style="color: #dc2626; font-size: .75rem; margin-top: .25rem;">{{ $message }}</p> @enderror
        </div>
        <div>
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Mobile</label>
          <input type="text" name="mobile" value="{{ old('mobile', $user->mobile ?? '') }}"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; color: #1e293b; outline: none; box-sizing: border-box;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <div>
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Email</label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; color: #1e293b; outline: none; box-sizing: border-box;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
          @error('email') <p style="color: #dc2626; font-size: .75rem; margin-top: .25rem;">{{ $message }}</p> @enderror
        </div>
        <button type="submit"
                style="width: 100%; padding: .625rem; background: #2563eb; color: white; border: none; border-radius: .5rem; font-size: .875rem; font-weight: 600; cursor: pointer; transition: background .1s;"
                onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
          Save Changes
        </button>
      </div>
    </form>
  </div>

  {{-- Change password --}}
  <div class="portal-card">
    <div class="section-title">
      <svg style="width:1rem;height:1rem;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
      Change Password
    </div>
    <form method="POST" action="{{ route('portal.change-password') }}">
      @csrf @method('PUT')
      <div style="display: flex; flex-direction: column; gap: .75rem;">
        <div>
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Current Password</label>
          <input type="password" name="current_password"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; outline: none; box-sizing: border-box;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
          @error('current_password') <p style="color: #dc2626; font-size: .75rem; margin-top: .25rem;">{{ $message }}</p> @enderror
        </div>
        <div>
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">New Password</label>
          <input type="password" name="password"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; outline: none; box-sizing: border-box;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <div>
          <label style="display: block; font-size: .75rem; font-weight: 600; color: #475569; margin-bottom: .375rem;">Confirm New Password</label>
          <input type="password" name="password_confirmation"
                 style="width: 100%; border: 1px solid #e2e8f0; border-radius: .5rem; padding: .5rem .75rem; font-size: .875rem; outline: none; box-sizing: border-box;"
                 onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <button type="submit"
                style="width: 100%; padding: .625rem; background: #475569; color: white; border: none; border-radius: .5rem; font-size: .875rem; font-weight: 600; cursor: pointer; transition: background .1s;"
                onmouseover="this.style.background='#334155'" onmouseout="this.style.background='#475569'">
          Change Password
        </button>
      </div>
    </form>
  </div>

</div>

{{-- Linked children --}}
<div class="portal-card">
  <div class="section-title">
    <svg style="width:1rem;height:1rem;color:#3b82f6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
    Linked Children
  </div>
  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: .75rem;">
    @foreach($children as $child)
    <div style="display: flex; align-items: center; gap: .75rem; padding: .75rem; background: #f8fafc; border-radius: .75rem; border: 1px solid #f1f5f9;">
      <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: linear-gradient(135deg, #3b82f6, #6366f1); display: flex; align-items: center; justify-content: center; font-size: .875rem; font-weight: 700; color: white; flex-shrink: 0;">
        {{ strtoupper(substr($child->first_name, 0, 1)) }}
      </div>
      <div>
        <p style="font-size: .875rem; font-weight: 600; color: #1e293b;">{{ $child->first_name }} {{ $child->last_name }}</p>
        <p style="font-size: .72rem; color: #94a3b8;">Adm# {{ $child->admission_no }}</p>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection
