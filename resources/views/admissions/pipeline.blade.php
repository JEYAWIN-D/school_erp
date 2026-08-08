@extends('layouts.app')
@section('title','Admission Pipeline')
@section('content')
<div class="space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Admission Pipeline</h1>
  </div>
  @php $stages = ['enquiry'=>'Enquiry','application'=>'Application','entrance_test'=>'Entrance Test','interview'=>'Interview','document_verification'=>'Docs Verified','confirmed'=>'Confirmed','enrolled'=>'Enrolled']; @endphp
  <div class="flex gap-2 overflow-x-auto pb-2">
    @foreach($stages as $key=>$label)
    @php $count = $pipeline[$key] ?? 0; @endphp
    <a href="?stage={{ $key }}" class="flex-shrink-0 card text-center py-4 px-6 min-w-[130px] {{ request('stage')===$key ? 'ring-2 ring-indigo-500' : '' }} hover:ring-1 hover:ring-indigo-300">
      <p class="text-2xl font-bold text-slate-800">{{ $count }}</p>
      <p class="text-xs text-slate-500 mt-1">{{ $label }}</p>
    </a>
    @endforeach
  </div>
  <div class="card overflow-hidden">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
      <h3 class="font-semibold text-slate-700">{{ $stages[request('stage','enquiry')] ?? 'All Enquiries' }}</h3>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-100"><tr>
        @foreach(['Enquiry ID','Student','Class','Source','Status','Date','Actions'] as $h)
        <th class="text-left px-4 py-3 text-slate-500 text-xs uppercase tracking-wide font-medium">{{ $h }}</th>
        @endforeach
      </tr></thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($enquiries as $e)
        <tr class="hover:bg-slate-50">
          <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $e->enquiry_number }}</td>
          <td class="px-4 py-3 font-medium text-slate-800">{{ $e->student_name }}</td>
          <td class="px-4 py-3 text-slate-500">{{ $e->class?->name }}</td>
          <td class="px-4 py-3 text-slate-500 capitalize">{{ $e->source }}</td>
          <td class="px-4 py-3"><span class="badge-{{ $e->status_color }}">{{ $e->status_label }}</span></td>
          <td class="px-4 py-3 text-slate-400 text-xs">{{ $e->created_at->format('d M Y') }}</td>
          <td class="px-4 py-3">
            <a href="{{ route('admissions.show',$e->id) }}" class="text-indigo-600 hover:underline text-xs">View</a>
            <form method="POST" action="{{ route('admissions.advance-stage',$e->id) }}" class="inline ml-2">
              @csrf <button type="submit" class="text-green-600 hover:underline text-xs">Advance →</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No records in this stage.</td></tr>
        @endforelse
      </tbody>
    </table>
    @if($enquiries->hasPages())<div class="px-4 pb-3">{{ $enquiries->links() }}</div>@endif
  </div>
</div>
@endsection
