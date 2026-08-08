@extends('layouts.app')
@section('title', 'Fee Structure')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Fee Structure</h1>
    <button x-data @click="$dispatch('open-modal','add-fee-head')" class="btn btn-primary btn-sm">Add Fee Head</button>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 card space-y-3">
      <h3 class="font-semibold text-slate-700 pb-2 border-b border-slate-100">Fee Heads</h3>
      @forelse($feeHeads as $head)
        <div class="flex items-center justify-between py-1.5">
          <div>
            <p class="text-sm font-medium text-slate-800">{{ $head->name }}</p>
            <p class="text-xs text-slate-400">{{ ucfirst($head->fee_type) }}
              @if($head->gst_applicable) · <span class="text-indigo-500">GST {{ $head->gst_percent }}%</span>@endif
            </p>
          </div>
          <span class="{{ $head->is_active ? 'badge-green' : 'badge-slate' }} text-xs">{{ $head->is_active ? 'Active' : 'Off' }}</span>
        </div>
      @empty
        <p class="text-slate-400 text-sm text-center py-4">No fee heads.</p>
      @endforelse
    </div>
    <div class="lg:col-span-2 card overflow-hidden">
      <h3 class="font-semibold text-slate-700 mb-4 pb-2 border-b border-slate-100">Structures by Class</h3>
      <form method="GET" class="mb-4"><div class="flex gap-3">
        <select name="class_id" class="select w-36">
          <option value="">All Classes</option>
          @foreach($classes as $c)<option value="{{ $c->id }}" @selected(request('class_id')==$c->id)>{{ $c->name }}</option>@endforeach
        </select>
        <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
      </div></form>
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100"><tr>
          @foreach(['Class','Fee Head','Amount','Applies To','Due Date'] as $h)
          <th class="text-left px-4 py-2 text-slate-500 text-xs uppercase tracking-wide font-medium">{{ $h }}</th>
          @endforeach
        </tr></thead>
        <tbody class="divide-y divide-slate-100">
          @php $flat = $structures->flatten(); @endphp
          @forelse($flat as $fs)
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-2 text-slate-800">{{ $fs->class?->name }}</td>
            <td class="px-4 py-2 text-slate-600">{{ $fs->feeHead?->name }}</td>
            <td class="px-4 py-2 font-semibold">₹{{ number_format($fs->amount, 2) }}</td>
            <td class="px-4 py-2">
              @php $at = $fs->applies_to ?? 'all'; @endphp
              <span class="badge-{{ $at === 'all' ? 'green' : ($at === 'new_admission' ? 'indigo' : 'amber') }} text-xs capitalize">
                {{ $at === 'all' ? 'All Students' : str_replace('_',' ', ucfirst($at)) }}
              </span>
            </td>
            <td class="px-4 py-2 text-slate-400 text-xs">{{ $fs->due_date ? \Carbon\Carbon::parse($fs->due_date)->format('d M Y') : '—' }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No fee structures configured.</td></tr>
          @endforelse
        </tbody>
      </table>

      {{-- Quick Add Structure Form --}}
      <div class="border-t border-slate-100 mt-4 pt-4">
        <h4 class="text-sm font-medium text-slate-600 mb-3">Add / Update Fee Structure</h4>
        <form method="POST" action="{{ route('fees.structure.save') }}" class="grid grid-cols-2 md:grid-cols-3 gap-3">
          @csrf
          <div>
            <label class="label text-xs">Class <span class="text-red-500">*</span></label>
            <select name="class_id" class="select text-sm" required>
              <option value="">Select class</option>
              @foreach($classes as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
            </select>
          </div>
          <div>
            <label class="label text-xs">Fee Head <span class="text-red-500">*</span></label>
            <select name="fee_head_id" class="select text-sm" required>
              <option value="">Select fee head</option>
              @foreach($feeHeads as $fh)<option value="{{ $fh->id }}">{{ $fh->name }}</option>@endforeach
            </select>
          </div>
          <div>
            <label class="label text-xs">Amount (₹) <span class="text-red-500">*</span></label>
            <input type="number" name="amount" class="input text-sm" min="0" step="0.01" required>
          </div>
          <div>
            <label class="label text-xs">Applies To</label>
            <select name="applies_to" class="select text-sm">
              <option value="all">All Students</option>
              <option value="new_admission">New Admission Only</option>
              <option value="existing">Existing Students Only</option>
            </select>
          </div>
          <div>
            <label class="label text-xs">Due Date</label>
            <input type="date" name="due_date" class="input text-sm">
          </div>
          <div class="flex items-end">
            <button type="submit" class="btn btn-primary btn-sm w-full">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
{{-- Add Fee Head Modal --}}
<div x-data="{open:false,gst:false}" @open-modal.window="if($event.detail==='add-fee-head')open=true" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display:none">
  <div class="absolute inset-0 bg-black/40" @click="open=false"></div>
  <div class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6 z-10 space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="font-semibold text-slate-800">Add Fee Head</h3>
      <button @click="open=false" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
    </div>
    <form method="POST" action="{{ route('fees.heads.store') }}" class="space-y-4">
      @csrf
      <div class="grid grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="label">Fee Head Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" class="input" required placeholder="e.g. Tuition Fee">
        </div>
        <div>
          <label class="label">Fee Type <span class="text-red-500">*</span></label>
          <select name="fee_type" class="select">
            @foreach(['tuition'=>'Tuition','transport'=>'Transport','hostel'=>'Hostel','exam'=>'Exam','library'=>'Library','lab'=>'Lab','misc'=>'Miscellaneous','fine'=>'Fine'] as $k=>$v)
            <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label">Description</label>
          <input type="text" name="description" class="input" placeholder="Optional notes">
        </div>
      </div>
      <div class="border-t border-slate-100 pt-4">
        <label class="flex items-center gap-2 text-sm font-medium text-slate-700 mb-3">
          <input type="checkbox" name="gst_applicable" value="1" x-model="gst"> GST Applicable
        </label>
        <div x-show="gst" x-transition class="grid grid-cols-3 gap-3">
          <div>
            <label class="label text-xs">GST %</label>
            <input type="number" name="gst_percent" class="input" step="0.01" min="0" max="100" placeholder="e.g. 18">
          </div>
          <div>
            <label class="label text-xs">HSN/SAC Code</label>
            <input type="text" name="hsn_code" class="input" placeholder="e.g. 9992">
          </div>
          <div>
            <label class="label text-xs">GST Type</label>
            <select name="gst_type" class="select">
              <option value="exclusive">Exclusive (added on top)</option>
              <option value="inclusive">Inclusive (included in amount)</option>
            </select>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" @click="open=false" class="btn btn-secondary">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Fee Head</button>
      </div>
    </form>
  </div>
</div>
@endsection
