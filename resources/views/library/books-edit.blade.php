@extends('layouts.app')
@section('title','Edit Book')
@section('content')
<div class="space-y-6 max-w-3xl">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Edit Book</h1>
    <a href="{{ route('library.books') }}" class="btn btn-secondary btn-sm">Back to Books</a>
  </div>

  @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('library.books.update',$book->id) }}" enctype="multipart/form-data" class="card space-y-4">
    @csrf @method('PUT')
    <div><label class="label">Title <span class="text-red-500">*</span></label>
      <input type="text" name="title" class="input" required value="{{ old('title',$book->title) }}">
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Author</label><input type="text" name="author" class="input" value="{{ old('author',$book->author) }}"></div>
      <div><label class="label">Publisher</label><input type="text" name="publisher" class="input" value="{{ old('publisher',$book->publisher) }}"></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">ISBN</label><input type="text" name="isbn" class="input" value="{{ old('isbn',$book->isbn) }}"></div>
      <div><label class="label">Category</label><input type="text" name="category" class="input" value="{{ old('category',$book->category) }}"></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Total Copies</label><input type="number" name="total_copies" class="input" min="1" value="{{ old('total_copies',$book->total_copies) }}"></div>
      <div><label class="label">Location</label><input type="text" name="location" class="input" value="{{ old('location',$book->location) }}" placeholder="e.g. Shelf A3"></div>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div><label class="label">Purchase Price</label><input type="number" name="purchase_price" class="input" step="0.01" value="{{ old('purchase_price',$book->purchase_price) }}"></div>
      <div><label class="label">Purchase Date</label><input type="date" name="purchase_date" class="input" value="{{ old('purchase_date',$book->purchase_date?->toDateString()) }}"></div>
    </div>

    {{-- Cover Image --}}
    <div>
      <label class="label">Cover Image</label>
      @if($book->cover_image)
        <div class="flex items-center gap-3 mb-2">
          <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover" class="w-16 h-20 object-cover rounded border border-slate-200 shadow-sm">
          <span class="text-sm text-slate-500">Current cover</span>
        </div>
      @endif
      <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
      <p class="text-xs text-slate-400 mt-1">JPG, PNG, or WebP — max 2 MB. Leave blank to keep current.</p>
    </div>

    <div class="flex gap-3">
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
  </form>

  {{-- Deaccession --}}
  @if(!$book->is_deaccessioned)
  <div class="card border-red-100" x-data="{ open: false }">
    <button type="button" @click="open = !open"
            class="flex items-center justify-between w-full text-left">
      <span class="font-semibold text-red-700">Write Off / Deaccession</span>
      <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-red-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>
    <div x-show="open" x-transition class="mt-3 pt-3 border-t border-red-100">
      <p class="text-sm text-slate-600 mb-3">Writing off removes this book from active stock. This action cannot be undone.</p>
      <form method="POST" action="{{ route('library.books.deaccession', $book->id) }}" class="space-y-3"
            onsubmit="return confirm('Write off \'{{ addslashes($book->title) }}\'? This cannot be undone.')">
        @csrf
        <div>
          <label class="label text-xs">Reason for Write-off <span class="text-red-500">*</span></label>
          <input type="text" name="reason" class="input text-sm" required placeholder="e.g. Damaged beyond repair, Lost, Outdated">
        </div>
        <button type="submit" class="btn btn-sm bg-red-600 text-white hover:bg-red-700">Confirm Write Off</button>
      </form>
    </div>
  </div>
  @else
  <div class="card border-slate-200 bg-slate-50">
    <p class="text-sm font-medium text-slate-500">
      This book was written off on {{ $book->deaccession_date?->format('d M Y') }}.
      <br>Reason: {{ $book->deaccession_reason ?? '—' }}
    </p>
  </div>
  @endif
</div>
@endsection
