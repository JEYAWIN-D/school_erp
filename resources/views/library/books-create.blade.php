@extends('layouts.app')
@section('title', 'Add Book')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="{{ route('library.books') }}" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="page-title">Add Book</h1>
  </div>

  {{-- ISBN Lookup --}}
  <div class="card" x-data="{
    isbnInput: '',
    loading: false,
    error: '',
    coverUrl: '',
    async lookup() {
      if (!this.isbnInput.trim()) return;
      this.loading = true; this.error = ''; this.coverUrl = '';
      try {
        const res = await fetch('{{ route('library.isbn-lookup') }}?isbn=' + encodeURIComponent(this.isbnInput));
        const data = await res.json();
        if (!res.ok) { this.error = data.error || 'Not found'; this.loading = false; return; }
        document.getElementById('f_title').value      = data.title || '';
        document.getElementById('f_author').value     = data.author || '';
        document.getElementById('f_publisher').value  = data.publisher || '';
        document.getElementById('f_year').value       = data.year || '';
        document.getElementById('f_isbn').value       = this.isbnInput;
        if (data.cover_url) this.coverUrl = data.cover_url;
      } catch(e) { this.error = 'Request failed. Check internet connection.'; }
      this.loading = false;
    }
  }">
    <h3 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
      <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
      ISBN Auto-Fill <span class="text-xs text-slate-400 font-normal">via Open Library</span>
    </h3>
    <div class="flex gap-2">
      <input type="text" x-model="isbnInput" class="input flex-1"
        placeholder="Enter ISBN-10 or ISBN-13 (e.g. 9780134685991)"
        @keydown.enter.prevent="lookup()">
      <button type="button" @click="lookup()" class="btn btn-secondary"
        :disabled="loading" :class="loading ? 'opacity-60' : ''">
        <span x-text="loading ? 'Looking up…' : 'Lookup'"></span>
      </button>
    </div>
    <p x-show="error" class="text-xs text-red-500 mt-2" x-text="error"></p>
    <div x-show="coverUrl" class="mt-3 flex items-center gap-3">
      <img :src="coverUrl" alt="Cover" class="h-16 rounded shadow">
      <p class="text-xs text-green-600">✓ Book details filled from Open Library</p>
    </div>
  </div>

  <form method="POST" action="{{ route('library.books.store') }}" enctype="multipart/form-data" class="card space-y-4">
    @csrf
    <div>
      <label class="label">Title <span class="text-red-500">*</span></label>
      <input type="text" id="f_title" name="title" value="{{ old('title') }}" class="input @error('title') input-error @enderror">
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="label">Author</label>
        <input type="text" id="f_author" name="author" value="{{ old('author') }}" class="input">
      </div>
      <div>
        <label class="label">Publisher</label>
        <input type="text" id="f_publisher" name="publisher" value="{{ old('publisher') }}" class="input">
      </div>
      <div>
        <label class="label">ISBN</label>
        <input type="text" id="f_isbn" name="isbn" value="{{ old('isbn') }}" class="input">
      </div>
      <div>
        <label class="label">Publication Year</label>
        <input type="number" id="f_year" name="publication_year" value="{{ old('publication_year') }}" class="input" min="1800" max="{{ date('Y') + 1 }}">
      </div>
      <div>
        <label class="label">Total Copies <span class="text-red-500">*</span></label>
        <input type="number" name="total_copies" value="{{ old('total_copies', 1) }}" class="input @error('total_copies') input-error @enderror" min="1">
      </div>
      <div>
        <label class="label">Purchase Price</label>
        <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" class="input" min="0" step="0.01">
      </div>
      <div>
        <label class="label">Purchase Date</label>
        <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="input">
      </div>
      <div>
        <label class="label">Shelf Location</label>
        <input type="text" name="location" value="{{ old('location') }}" class="input" placeholder="e.g. Rack A-3">
      </div>
      <div>
        <label class="label">Category</label>
        <select name="category" class="select">
          <option value="">Select Category</option>
          @foreach(['Fiction','Non-Fiction','Science','Mathematics','Reference','Textbook','Periodical','Biography','History','Technology','Language','Other'] as $cat)
          <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ $cat }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="label">Language</label>
        <input type="text" name="language" value="{{ old('language', 'English') }}" class="input">
      </div>
    </div>
    <div class="sm:col-span-2">
      <label class="label">Cover Image <span class="text-slate-400 font-normal text-xs">(optional)</span></label>
      <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
             class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
      <p class="text-xs text-slate-400 mt-1">JPG, PNG, or WebP — max 2 MB</p>
    </div>
    <div class="flex justify-end gap-3 pt-2">
      <a href="{{ route('library.books') }}" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Add Book</button>
    </div>
  </form>
</div>
@endsection
