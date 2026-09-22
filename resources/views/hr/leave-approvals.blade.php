@extends('layouts.app')
@section('title', 'Leave Approvals')

@section('content')
<div class="space-y-6" x-data="{ viewModalOpen: false, currentLeave: null }">

  {{-- Page Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-2xs">
    <div class="flex items-center gap-3.5">
      <a href="{{ route('hr.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition" title="Back to HR & Payroll">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      </a>
      <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-white shadow-md shadow-amber-100 flex-shrink-0" style="background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%); color: #FFFFFF;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
      </div>
      <div>
        <h1 class="page-title text-xl font-black text-slate-800" style="font-family:'Plus Jakarta Sans',sans-serif;">Leave Approvals</h1>
        <p class="text-xs font-semibold text-slate-500 mt-0.5">Staff leave requests register &bull; View Only (Principal Approval)</p>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      <a href="{{ route('hr.leaves.apply') }}" class="btn btn-primary btn-sm flex items-center gap-1.5 text-xs font-bold shadow-xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Apply Leave on Behalf
      </a>
    </div>
  </div>

  {{-- Filter Toolbar & Status Tabs --}}
  <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-2xs space-y-3">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      {{-- Status Filter Tabs --}}
      <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
        @foreach([
          'pending'  => ['label' => 'Pending Review', 'count' => $counts['pending'], 'active_class' => 'bg-amber-500 text-white'],
          'approved' => ['label' => 'Approved',       'count' => $counts['approved'], 'active_class' => 'bg-emerald-600 text-white'],
          'rejected' => ['label' => 'Rejected',       'count' => $counts['rejected'], 'active_class' => 'bg-rose-600 text-white'],
          'all'      => ['label' => 'All Requests',   'count' => $counts['all'],      'active_class' => 'bg-slate-800 text-white'],
        ] as $key => $tab)
          @php $isActive = ($status === $key); @endphp
          <a href="{{ route('hr.leave-approvals', array_merge(request()->query(), ['status' => $key, 'page' => 1])) }}"
             class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap {{ $isActive ? $tab['active_class'] . ' shadow-xs' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            <span>{{ $tab['label'] }}</span>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}">
              {{ $tab['count'] }}
            </span>
          </a>
        @endforeach
      </div>

      {{-- Search --}}
      <form method="GET" action="{{ route('hr.leave-approvals') }}" class="flex items-center gap-2">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="relative w-full sm:w-64">
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Search staff name or code..."
                 class="input input-sm w-full bg-slate-50 border-slate-200 text-xs rounded-xl focus:bg-white">
        </div>
        <button type="submit" class="btn btn-secondary btn-sm text-xs font-bold">Search</button>
        @if(request('search'))
          <a href="{{ route('hr.leave-approvals', ['status' => $status]) }}" class="text-xs text-slate-400 hover:text-slate-600">Clear</a>
        @endif
      </form>
    </div>
  </div>

  {{-- Leave Requests Table --}}
  <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-black uppercase tracking-wider text-slate-400">
            <th class="py-3 px-4">Staff Member</th>
            <th class="py-3 px-3">Category</th>
            <th class="py-3 px-3">Leave Type</th>
            <th class="py-3 px-3">From Date</th>
            <th class="py-3 px-3">To Date</th>
            <th class="py-3 px-3 text-center">Days</th>
            <th class="py-3 px-3">Reason</th>
            <th class="py-3 px-3">Applied Date</th>
            <th class="py-3 px-3 text-center">Status</th>
            <th class="py-3 px-4 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-xs">
          @forelse($leaves as $l)
            @php
              $emp = $l->employee;
            @endphp
            <tr class="hover:bg-slate-50/60 transition-colors">
              {{-- Staff Name --}}
              <td class="py-3 px-4">
                <div class="font-bold text-slate-900">
                  {{ $emp?->full_name ?? '—' }}
                </div>
                <span class="text-[10px] font-mono text-slate-400">{{ $emp?->employee_code ?? 'EMP-' . $emp?->id }}</span>
              </td>

              {{-- Category --}}
              <td class="py-3 px-3">
                <span class="{{ $emp?->category_badge_class ?? 'badge-blue' }} text-[9px] px-2 py-0.5 font-bold uppercase rounded-md">
                  {{ $emp?->category_label ?? 'Staff' }}
                </span>
              </td>

              {{-- Leave Type --}}
              <td class="py-3 px-3 font-semibold text-slate-800">
                {{ $l->leaveType?->name ?? 'General Leave' }}
              </td>

              {{-- From Date --}}
              <td class="py-3 px-3 font-mono text-slate-600">
                {{ \Carbon\Carbon::parse($l->from_date)->format('d M Y') }}
              </td>

              {{-- To Date --}}
              <td class="py-3 px-3 font-mono text-slate-600">
                {{ \Carbon\Carbon::parse($l->to_date)->format('d M Y') }}
              </td>

              {{-- Days --}}
              <td class="py-3 px-3 text-center">
                <span class="font-mono font-black text-slate-800 bg-slate-100 px-2 py-0.5 rounded">
                  {{ $l->total_days }}
                </span>
              </td>

              {{-- Reason --}}
              <td class="py-3 px-3 max-w-[200px]">
                <p class="truncate text-slate-600" title="{{ $l->reason }}">{{ $l->reason }}</p>
              </td>

              {{-- Applied Date --}}
              <td class="py-3 px-3 font-mono text-slate-400 text-[11px]">
                {{ $l->created_at ? $l->created_at->format('d M Y') : '—' }}
                @if($l->applied_on_behalf)
                  <span class="block text-[9px] text-indigo-600 font-sans font-bold">On Behalf</span>
                @endif
              </td>

              {{-- Status --}}
              <td class="py-3 px-3 text-center">
                @if($l->status === 'approved')
                  <span class="badge-green text-[10px] font-bold px-2 py-0.5 rounded-full">Approved</span>
                @elseif($l->status === 'rejected')
                  <span class="badge-red text-[10px] font-bold px-2 py-0.5 rounded-full">Rejected</span>
                @elseif($l->status === 'cancelled')
                  <span class="badge-slate text-[10px] font-bold px-2 py-0.5 rounded-full">Cancelled</span>
                @else
                  <span class="badge-amber text-[10px] font-bold px-2 py-0.5 rounded-full">Pending</span>
                @endif
              </td>

              {{-- Actions --}}
              <td class="py-3 px-4 text-right">
                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                  {{-- View Modal Trigger --}}
                  <button type="button"
                          @click="currentLeave = {
                            staffName: '{{ addslashes($emp?->full_name ?? '') }}',
                            staffId: '{{ $emp?->employee_code ?? '' }}',
                            category: '{{ $emp?->category_label ?? '' }}',
                            leaveType: '{{ addslashes($l->leaveType?->name ?? '') }}',
                            fromDate: '{{ \Carbon\Carbon::parse($l->from_date)->format('d M Y') }}',
                            toDate: '{{ \Carbon\Carbon::parse($l->to_date)->format('d M Y') }}',
                            days: '{{ $l->total_days }}',
                            reason: '{{ addslashes($l->reason ?? '') }}',
                            appliedDate: '{{ $l->created_at ? $l->created_at->format('d M Y, h:i A') : '—' }}',
                            appliedBy: '{{ addslashes($l->appliedBy?->name ?? ($l->applied_on_behalf ? "Admin" : ($emp?->full_name ?? "Self"))) }}',
                            appliedOnBehalf: {{ $l->applied_on_behalf ? 'true' : 'false' }},
                            status: '{{ ucfirst($l->status) }}',
                            attachment: '{{ $l->attachment ? asset('storage/' . $l->attachment) : '' }}'
                          }; viewModalOpen = true"
                          class="btn btn-secondary btn-xs font-bold">
                    View
                  </button>

                  @if($l->attachment)
                    <a href="{{ asset('storage/' . $l->attachment) }}" target="_blank" class="btn btn-secondary btn-xs text-indigo-600" title="Download Document">
                      Doc
                    </a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="10" class="py-12 text-center text-slate-400">
                <p class="text-sm font-semibold text-slate-500">
                  @if($status === 'pending')
                    No pending leave requests.
                  @else
                    No leave requests found.
                  @endif
                </p>
                <p class="text-xs text-slate-400 mt-1">Leave applications submitted by staff will appear here for review.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($leaves->hasPages())
      <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $leaves->links() }}
      </div>
    @endif
  </div>

  {{-- View Leave Details Modal --}}
  <div x-show="viewModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-200 space-y-4" @click.away="viewModalOpen = false">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <h3 class="font-extrabold text-base text-slate-900">Leave Request Details</h3>
        <button type="button" @click="viewModalOpen = false" class="text-slate-400 hover:text-slate-600">&times;</button>
      </div>

      <div class="space-y-3 text-xs" x-show="currentLeave">
        <div class="grid grid-cols-2 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
          <div>
            <span class="text-slate-400 block font-bold uppercase text-[10px]">Staff Member</span>
            <span class="font-bold text-slate-900 text-sm" x-text="currentLeave?.staffName"></span>
            <span class="text-[11px] text-slate-500 block font-mono" x-text="currentLeave?.staffId"></span>
          </div>
          <div>
            <span class="text-slate-400 block font-bold uppercase text-[10px]">Category</span>
            <span class="font-bold text-slate-800" x-text="currentLeave?.category"></span>
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <span class="text-slate-400 block font-bold uppercase text-[10px]">Leave Type</span>
            <span class="font-semibold text-slate-800" x-text="currentLeave?.leaveType"></span>
          </div>
          <div>
            <span class="text-slate-400 block font-bold uppercase text-[10px]">Duration</span>
            <span class="font-bold text-slate-900" x-text="currentLeave?.days + ' day(s)'"></span>
          </div>
          <div>
            <span class="text-slate-400 block font-bold uppercase text-[10px]">Status</span>
            <span class="font-bold" x-text="currentLeave?.status"></span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <span class="text-slate-400 block font-bold uppercase text-[10px]">From Date</span>
            <span class="font-mono text-slate-700" x-text="currentLeave?.fromDate"></span>
          </div>
          <div>
            <span class="text-slate-400 block font-bold uppercase text-[10px]">To Date</span>
            <span class="font-mono text-slate-700" x-text="currentLeave?.toDate"></span>
          </div>
        </div>

        <div>
          <span class="text-slate-400 block font-bold uppercase text-[10px] mb-1">Reason for Leave</span>
          <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 leading-relaxed" x-text="currentLeave?.reason"></div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <span class="text-slate-400 block font-bold uppercase text-[10px]">Applied On</span>
            <span class="text-slate-600 font-mono" x-text="currentLeave?.appliedDate"></span>
          </div>
          <div>
            <span class="text-slate-400 block font-bold uppercase text-[10px]">Applied By</span>
            <span class="text-slate-700 font-medium" x-text="currentLeave?.appliedOnBehalf ? (currentLeave?.appliedBy + ' (On Behalf)') : currentLeave?.appliedBy"></span>
          </div>
        </div>

        <template x-if="currentLeave?.attachment">
          <div class="pt-2">
            <a :href="currentLeave?.attachment" target="_blank" class="btn btn-secondary btn-sm text-xs font-bold text-indigo-600 flex items-center gap-1.5 w-fit">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              View Uploaded Document
            </a>
          </div>
        </template>
      </div>

      <div class="flex justify-end pt-3 border-t border-slate-100">
        <button type="button" @click="viewModalOpen = false" class="btn btn-secondary btn-sm">Close</button>
      </div>
    </div>
  </div>

</div>
@endsection
