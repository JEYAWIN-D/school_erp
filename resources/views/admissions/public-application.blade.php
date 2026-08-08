<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $config->title }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen py-10 px-4">
  <div class="max-w-2xl mx-auto">
    {{-- Header --}}
    <div class="text-center mb-8">
      <h1 class="text-2xl font-bold text-slate-800">{{ $config->title }}</h1>
      @if($config->description)<p class="text-slate-600 mt-2">{{ $config->description }}</p>@endif
      @if($config->class)<p class="text-sm text-indigo-600 mt-1">Class: {{ $config->class->name }}</p>@endif
      @if($config->academicYear)<p class="text-sm text-slate-400">Academic Year: {{ $config->academicYear->name }}</p>@endif
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-6">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-6">
      <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('apply.submit', $config->link_token) }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-5">
      @csrf

      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Parent/Guardian Mobile <span class="text-red-500">*</span></label>
        <input type="tel" name="parent_mobile" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400" required value="{{ old('parent_mobile') }}">
      </div>

      @foreach($config->fields ?? [] as $field)
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
          {{ $field['label'] }}
          @if($field['required'] ?? false)<span class="text-red-500">*</span>@endif
        </label>
        @if($field['type'] === 'date')
          <input type="date" name="fields[{{ $field['name'] }}]" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm"
                 {{ ($field['required'] ?? false) ? 'required' : '' }} value="{{ old('fields.' . $field['name']) }}">
        @elseif($field['type'] === 'select' && $field['name'] === 'gender')
          <select name="fields[{{ $field['name'] }}]" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm" {{ ($field['required'] ?? false) ? 'required' : '' }}>
            <option value="">Select...</option>
            <option value="male" @selected(old('fields.'.$field['name'])==='male')>Male</option>
            <option value="female" @selected(old('fields.'.$field['name'])==='female')>Female</option>
            <option value="other" @selected(old('fields.'.$field['name'])==='other')>Other</option>
          </select>
        @elseif($field['type'] === 'select' && $field['name'] === 'category')
          <select name="fields[{{ $field['name'] }}]" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm">
            <option value="">Select...</option>
            @foreach(['General','SC','ST','OBC','EWS'] as $cat)
            <option value="{{ $cat }}" @selected(old('fields.'.$field['name'])===$cat)>{{ $cat }}</option>
            @endforeach
          </select>
        @else
          <input type="text" name="fields[{{ $field['name'] }}]" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm"
                 {{ ($field['required'] ?? false) ? 'required' : '' }} value="{{ old('fields.' . $field['name']) }}">
        @endif
      </div>
      @endforeach

      @if(!empty($config->document_fields))
      <div class="border-t border-slate-100 pt-5">
        <h3 class="text-sm font-semibold text-slate-600 mb-4">Document Uploads</h3>
        @foreach($config->document_fields as $doc)
        <div class="mb-4">
          <label class="block text-sm font-medium text-slate-700 mb-1">
            {{ $doc['label'] }}
            @if($doc['required'] ?? false)<span class="text-red-500">*</span>@endif
          </label>
          <input type="file" name="docs[{{ $doc['name'] }}]"
                 accept=".pdf,.jpg,.jpeg,.png"
                 {{ ($doc['required'] ?? false) ? 'required' : '' }}
                 class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700">
          <p class="text-xs text-slate-400 mt-1">PDF, JPG or PNG — max {{ $doc['max_mb'] ?? 5 }}MB</p>
        </div>
        @endforeach
      </div>
      @endif

      <div class="pt-3">
        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl py-3 text-sm transition">
          Submit Application
        </button>
      </div>
    </form>

    <p class="text-center text-xs text-slate-400 mt-6">Your application will be reviewed by the school admissions team.</p>
  </div>
</body>
</html>
