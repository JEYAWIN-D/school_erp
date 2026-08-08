@extends('layouts.app')
@section('title','Bulk Admission Import')
@section('content')
<div class="space-y-6">
  <h1 class="page-title">Bulk Admission Import</h1>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card space-y-4">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Upload Student Excel</h3>
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
        <p class="font-medium mb-2">Instructions:</p>
        <ol class="list-decimal ml-4 space-y-1">
          <li>Download the Excel template below</li>
          <li>Fill in all required fields (marked with *)</li>
          <li>Upload the completed file</li>
          <li>Review preview before importing</li>
        </ol>
      </div>
      <a href="{{ route('admissions.import-template') }}" class="btn btn-secondary btn-sm">⬇ Download Template</a>
      <form method="POST" action="{{ route('admissions.import') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div><label class="label">Excel File (.xlsx, .csv) <span class="text-red-500">*</span></label>
          <input type="file" name="file" accept=".xlsx,.xls,.csv" class="input" required>
        </div>
        <div><label class="label">Target Class</label>
          <select name="class_id" class="select">
            <option value="">All (from file)</option>
            @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Upload & Preview</button>
      </form>
    </div>
    @if(isset($preview) && count($preview))
    @php
      $errorCount = collect($preview)->filter(fn($r) => !empty($r['errors']))->count();
      $okCount    = count($preview) - $errorCount;
    @endphp
    <div class="card">
      <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
        <h3 class="font-semibold text-slate-700">Preview ({{ count($preview) }} records)</h3>
        <div class="flex gap-2">
          <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">{{ $okCount }} OK</span>
          @if($errorCount > 0)
            <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-semibold">{{ $errorCount }} errors</span>
          @endif
        </div>
      </div>
      @if($errorCount > 0)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-3 text-xs text-amber-800">
          {{ $errorCount }} row(s) have errors and will be skipped. Fix the source file and re-upload, or proceed to import only the valid {{ $okCount }} row(s).
        </div>
      @endif
      <div class="overflow-x-auto max-h-80 overflow-y-auto">
        <table class="min-w-full text-xs">
          <thead class="bg-slate-50 sticky top-0"><tr>
            @foreach(['Name','Class','Father','Mobile','Status'] as $h)
            <th class="text-left px-3 py-2 text-slate-500 font-medium">{{ $h }}</th>
            @endforeach
          </tr></thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($preview as $row)
            <tr class="{{ $row['errors'] ? 'bg-red-50' : 'hover:bg-slate-50' }}">
              <td class="px-3 py-1.5">{{ $row['name'] }}</td>
              <td class="px-3 py-1.5">{{ $row['class'] }}</td>
              <td class="px-3 py-1.5">{{ $row['father'] }}</td>
              <td class="px-3 py-1.5">{{ $row['mobile'] }}</td>
              <td class="px-3 py-1.5">
                @if($row['errors'])<span class="text-red-600">{{ implode(', ',$row['errors']) }}</span>
                @else<span class="text-green-600">✓ OK</span>@endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <form method="POST" action="{{ route('admissions.import-confirm') }}" class="mt-4">
        @csrf
        <input type="hidden" name="import_key" value="{{ $importKey ?? '' }}">
        <button type="submit" class="btn btn-primary">Confirm Import</button>
      </form>
    </div>
    @endif
  </div>
  @if(session('import_result'))
  <div class="card bg-green-50 border border-green-200">
    <p class="text-green-700 font-medium">Import completed: {{ session('import_result.imported') }} imported, {{ session('import_result.failed') }} failed.</p>
  </div>
  @endif
</div>
@endsection
