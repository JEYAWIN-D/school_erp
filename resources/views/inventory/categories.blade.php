@extends('layouts.app')
@section('title', 'Inventory Categories')
@section('content')
<div class="space-y-6" x-data="{ editId: null, editName: '', editCode: '', editDesc: '' }">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Inventory Categories</h1>
    <a href="{{ route('inventory.index') }}" class="btn-sm btn-secondary">← Items</a>
  </div>

  @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add Form --}}
    <div class="card">
      <h3 class="font-semibold text-slate-700 mb-4">Add Category</h3>
      <form method="POST" action="{{ route('inventory.categories.store') }}" class="space-y-3">
        @csrf
        <div>
          <label class="label">Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" class="input" required>
        </div>
        <div>
          <label class="label">Code</label>
          <input type="text" name="code" class="input" placeholder="e.g. STAT">
        </div>
        <div>
          <label class="label">Description</label>
          <textarea name="description" rows="2" class="input"></textarea>
        </div>
        <button type="submit" class="btn-primary w-full">Add Category</button>
      </form>
    </div>

    {{-- List --}}
    <div class="lg:col-span-2 table-wrap">
      <table class="w-full text-sm">
        <thead>
          <tr>
            <th class="th">Name</th>
            <th class="th">Code</th>
            <th class="th text-center">Items</th>
            <th class="th">Status</th>
            <th class="th">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $cat)
          <tr class="tr">
            <td class="td font-medium">{{ $cat->name }}</td>
            <td class="td font-mono text-xs">{{ $cat->code ?? '—' }}</td>
            <td class="td text-center">{{ $cat->items_count }}</td>
            <td class="td">
              @if($cat->is_active) <span class="badge-green">Active</span>
              @else <span class="badge-slate">Inactive</span> @endif
            </td>
            <td class="td">
              <button @click="editId={{ $cat->id }};editName='{{ addslashes($cat->name) }}';editCode='{{ addslashes($cat->code ?? '') }}';editDesc='{{ addslashes($cat->description ?? '') }}'"
                class="btn-xs btn-secondary">Edit</button>
            </td>
          </tr>
          @empty
          <tr><td class="td text-center text-slate-400" colspan="5">No categories yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Edit Modal --}}
  <div x-show="editId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm space-y-4" @click.stop>
      <h3 class="font-semibold text-slate-700">Edit Category</h3>
      <form method="POST" :action="'/inventory/categories/' + editId" class="space-y-3">
        @csrf @method('PUT')
        <div>
          <label class="label">Name</label>
          <input type="text" name="name" x-model="editName" class="input" required>
        </div>
        <div>
          <label class="label">Code</label>
          <input type="text" name="code" x-model="editCode" class="input">
        </div>
        <div>
          <label class="label">Description</label>
          <textarea name="description" rows="2" class="input" x-model="editDesc"></textarea>
        </div>
        <div class="flex gap-2">
          <button type="submit" class="btn-primary flex-1">Update</button>
          <button type="button" @click="editId=null" class="btn-secondary flex-1">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
