@extends('layouts.app')

@section('title', 'Create Expense Voucher — DASA EduERP')

@section('content')
<div class="space-y-6" x-data="{
  category: '{{ request('category', 'academic') }}',
  academicSubs: {
    'books_learning_materials':  'Books & Learning Materials',
    'lab_consumables':           'Lab Chemicals & Consumables',
    'exam_stationary':           'Exam Papers & Stationery',
    'sports_equipment':          'Sports Equipment & Physical Education',
    'student_events':            'Student Events & Competitions',
    'workshops_training':        'Faculty Workshops & Training',
    'software_licenses':         'Educational Software & IT Licenses',
    'other_academic':            'Other Academic Expenses'
  },
  maintenanceSubs: {
    'building_repairs_civil':    'Building & Civil Maintenance',
    'electrical_power':          'Electrical & Generator Fuel',
    'plumbing_water':            'Plumbing & RO Drinking Water',
    'bus_fleet_repairs':         'School Bus Fleet & Vehicle Maintenance',
    'campus_sanitation':         'Campus Housekeeping & Sanitation',
    'security_cctv':             'Campus Security & CCTV Systems',
    'furniture_fixtures':        'Desks, Benches & Furniture Repairs',
    'other_maintenance':         'General Facility Maintenance'
  },
  get currentSubs() { return this.category === 'academic' ? this.academicSubs : this.maintenanceSubs; }
}">

  {{-- Page Header --}}
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
        <a href="{{ route('expenses.index') }}" style="font-size:12px;color:#64748b;text-decoration:none;display:flex;align-items:center;gap:4px">
          <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
          Expenses Hub
        </a>
      </div>
      <h1 style="font-size:26px;font-weight:900;color:#1e293b;letter-spacing:-.01em">New Expense Voucher</h1>
      <p style="font-size:13px;color:#64748b;margin-top:4px">Create an academic or maintenance expense voucher for approval workflow.</p>
    </div>
  </div>

  <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start">

      {{-- Main Form --}}
      <div class="space-y-5">

        {{-- Category Toggle --}}
        <div style="background:#fff;border-radius:20px;padding:24px;border:1px solid #e2e8f0">
          <h2 style="font-size:14px;font-weight:800;color:#1e293b;margin-bottom:14px">1. Expense Category</h2>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <label style="cursor:pointer">
              <input type="radio" name="category" value="academic" x-model="category" style="display:none">
              <div :style="category === 'academic' ? 'border-color:#3b82f6;background:#eff6ff' : 'border-color:#e2e8f0;background:#f8fafc'"
                   style="border:2px solid;border-radius:16px;padding:18px;transition:all .2s">
                <div style="font-size:28px;margin-bottom:8px">📚</div>
                <div style="font-size:15px;font-weight:800;color:#1e293b">Academic</div>
                <div style="font-size:12px;color:#64748b;margin-top:4px;line-height:1.4">Books, Lab, Exams, Sports, Events, Training</div>
                <div style="margin-top:10px;font-size:10px;font-weight:700;color:#3b82f6;text-transform:uppercase">Coordinator → Admin → Principal</div>
              </div>
            </label>
            <label style="cursor:pointer">
              <input type="radio" name="category" value="maintenance" x-model="category" style="display:none">
              <div :style="category === 'maintenance' ? 'border-color:#f97316;background:#fff7ed' : 'border-color:#e2e8f0;background:#f8fafc'"
                   style="border:2px solid;border-radius:16px;padding:18px;transition:all .2s">
                <div style="font-size:28px;margin-bottom:8px">🔧</div>
                <div style="font-size:15px;font-weight:800;color:#1e293b">Maintenance</div>
                <div style="font-size:12px;color:#64748b;margin-top:4px;line-height:1.4">Civil, Electrical, Plumbing, Bus, Sanitation, Security</div>
                <div style="margin-top:10px;font-size:10px;font-weight:700;color:#f97316;text-transform:uppercase">Admin → Principal → Correspondent</div>
              </div>
            </label>
          </div>
        </div>

        {{-- Subcategory --}}
        <div style="background:#fff;border-radius:20px;padding:24px;border:1px solid #e2e8f0">
          <h2 style="font-size:14px;font-weight:800;color:#1e293b;margin-bottom:14px">2. Subcategory</h2>
          <select name="subcategory" required style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'Inter',sans-serif;color:#1e293b;outline:none">
            <option value="">— Select subcategory —</option>
            <template x-for="[key, label] in Object.entries(currentSubs)" :key="key">
              <option :value="key" x-text="label"></option>
            </template>
          </select>
          @error('subcategory')<div style="font-size:12px;color:#ef4444;margin-top:6px">{{ $message }}</div>@enderror
        </div>

        {{-- Title & Description --}}
        <div style="background:#fff;border-radius:20px;padding:24px;border:1px solid #e2e8f0">
          <h2 style="font-size:14px;font-weight:800;color:#1e293b;margin-bottom:14px">3. Expense Details</h2>
          <div style="margin-bottom:14px">
            <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Expense Title / Purpose *</label>
            <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. NCERT Textbooks for Classes VI-X" required style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'Inter',sans-serif;color:#1e293b;outline:none">
            @error('title')<div style="font-size:12px;color:#ef4444;margin-top:4px">{{ $message }}</div>@enderror
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Description / Justification</label>
            <textarea name="description" rows="3" placeholder="Describe what was purchased, why it was needed, and any key specifications…" style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'Inter',sans-serif;color:#1e293b;outline:none;resize:vertical">{{ old('description') }}</textarea>
          </div>
        </div>

        {{-- Amount, Date, Payment --}}
        <div style="background:#fff;border-radius:20px;padding:24px;border:1px solid #e2e8f0">
          <h2 style="font-size:14px;font-weight:800;color:#1e293b;margin-bottom:14px">4. Amount & Payment</h2>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
            <div>
              <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Amount (₹) *</label>
              <div style="position:relative">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:14px;color:#94a3b8;font-weight:700">₹</span>
                <input type="number" name="amount" value="{{ old('amount') }}" min="1" step="0.01" required placeholder="0.00" style="width:100%;padding:11px 14px 11px 28px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'JetBrains Mono',monospace;color:#1e293b;outline:none">
              </div>
              @error('amount')<div style="font-size:12px;color:#ef4444;margin-top:4px">{{ $message }}</div>@enderror
            </div>
            <div>
              <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Expense Date *</label>
              <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'Inter',sans-serif;color:#1e293b;outline:none">
              @error('expense_date')<div style="font-size:12px;color:#ef4444;margin-top:4px">{{ $message }}</div>@enderror
            </div>
          </div>
          <div>
            <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Payment Method *</label>
            <select name="payment_method" required style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'Inter',sans-serif;color:#1e293b;outline:none">
              <option value="">— Select method —</option>
              @foreach(['Bank Transfer (NEFT)','Bank Transfer (RTGS)','Bank Transfer (IMPS)','Cheque','UPI','Cash','Corporate Credit Card'] as $m)
              <option value="{{ $m }}" {{ old('payment_method')===$m?'selected':'' }}>{{ $m }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Vendor Info --}}
        <div style="background:#fff;border-radius:20px;padding:24px;border:1px solid #e2e8f0">
          <h2 style="font-size:14px;font-weight:800;color:#1e293b;margin-bottom:14px">5. Vendor / Supplier</h2>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div>
              <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Vendor Name</label>
              <input type="text" name="vendor_name" value="{{ old('vendor_name') }}" placeholder="e.g. Apex Scientific Pvt Ltd" style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'Inter',sans-serif;color:#1e293b;outline:none">
            </div>
            <div>
              <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Invoice / Receipt No.</label>
              <input type="text" name="vendor_invoice_no" value="{{ old('vendor_invoice_no') }}" placeholder="e.g. INV-2026-4411" style="width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'JetBrains Mono',monospace;color:#1e293b;outline:none">
            </div>
          </div>
        </div>

        {{-- Receipt Upload --}}
        <div style="background:#fff;border-radius:20px;padding:24px;border:1px solid #e2e8f0">
          <h2 style="font-size:14px;font-weight:800;color:#1e293b;margin-bottom:6px">6. Attach Receipt (Optional)</h2>
          <p style="font-size:12px;color:#64748b;margin-bottom:14px">Upload scanned bill, invoice PDF or photo (Max 5 MB — PDF/JPG/PNG/WEBP)</p>
          <div style="border:2px dashed #e2e8f0;border-radius:14px;padding:24px;text-align:center;transition:border-color .2s" id="dropzone">
            <div style="font-size:32px;margin-bottom:8px">📎</div>
            <label for="receipt" style="display:block;font-size:13px;font-weight:700;color:#4f46e5;cursor:pointer;margin-bottom:4px">Browse or drag &amp; drop receipt</label>
            <p style="font-size:12px;color:#94a3b8">PDF, JPG, PNG, WEBP up to 5 MB</p>
            <input type="file" id="receipt" name="receipt" accept=".pdf,.jpg,.jpeg,.png,.webp" style="display:none">
          </div>
        </div>

        <button type="submit" style="width:100%;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:16px;padding:16px;font-size:16px;font-weight:800;cursor:pointer;font-family:'Inter',sans-serif;letter-spacing:.01em;box-shadow:0 4px 16px rgba(79,70,229,0.4)">
          ✓ Submit Expense Voucher for Approval
        </button>
      </div>

      {{-- Sidebar --}}
      <div class="space-y-5">

        {{-- Approval preview --}}
        <div style="background:#fff;border-radius:20px;padding:22px;border:1px solid #e2e8f0">
          <h3 style="font-size:13px;font-weight:800;color:#1e293b;margin-bottom:12px">Approval Workflow</h3>
          <div x-show="category === 'academic'">
            @foreach([['Prepared by','Academic Coordinator','#dbeafe','#1e40af'],['Verified by','Admin Staff','#e0f2fe','#0369a1'],['Approved by','Principal','#dcfce7','#15803d']] as [$role, $who, $bg, $col])
            <div style="display:flex;align-items:center;gap:10px;padding:10px;background:{{ $bg }};border-radius:10px;margin-bottom:8px">
              <div style="width:28px;height:28px;border-radius:8px;background:{{ $col }};display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0">{{ substr($who,0,1) }}</div>
              <div>
                <div style="font-size:10px;color:#6b7280;font-weight:600;text-transform:uppercase">{{ $role }}</div>
                <div style="font-size:12px;font-weight:700;color:#1e293b">{{ $who }}</div>
              </div>
            </div>
            @endforeach
          </div>
          <div x-show="category === 'maintenance'">
            @foreach([['Prepared by','Admin Staff','#fff7ed','#c2410c'],['Verified by','Principal','#dbeafe','#1e40af'],['Approved by','Correspondent / School Owner','#f5f3ff','#6d28d9']] as [$role, $who, $bg, $col])
            <div style="display:flex;align-items:center;gap:10px;padding:10px;background:{{ $bg }};border-radius:10px;margin-bottom:8px">
              <div style="width:28px;height:28px;border-radius:8px;background:{{ $col }};display:flex;align-items:center;justify-content:center;color:#fff;font-size:10px;font-weight:700;flex-shrink:0">{{ substr($who,0,1) }}</div>
              <div>
                <div style="font-size:10px;color:#6b7280;font-weight:600;text-transform:uppercase">{{ $role }}</div>
                <div style="font-size:12px;font-weight:700;color:#1e293b">{{ $who }}</div>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        {{-- Tips --}}
        <div style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe);border:1px solid #bae6fd;border-radius:16px;padding:18px">
          <h3 style="font-size:12px;font-weight:800;color:#0c4a6e;margin-bottom:10px;display:flex;align-items:center;gap:6px"><span>💡</span> Tips</h3>
          <ul style="font-size:12px;color:#0e7490;line-height:1.7;padding-left:14px">
            <li>Attach the original vendor invoice as receipt</li>
            <li>For cash expenses below ₹1,000, only signature of HOD is needed</li>
            <li>NEFT/RTGS transfers auto-link to bank reconciliation</li>
            <li>Rejected vouchers can be re-submitted after correction</li>
          </ul>
        </div>

        {{-- Academic Year --}}
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:14px">
          <div style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:4px">Academic Year</div>
          <div style="font-size:15px;font-weight:800;color:#1e293b">{{ $academicYear?->name ?? '2025–2026' }}</div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
