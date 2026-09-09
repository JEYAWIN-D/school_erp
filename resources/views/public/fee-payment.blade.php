<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Fee Payment — Class {{ $class->name }} | DASA EduGroup</title>
<meta name="description" content="Secure online fee payment for Class {{ $class->name }} students at DASA EduGroup.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 40%,#0f172a 100%);min-height:100vh;padding:24px 16px}
.container{width:100%;max-width:640px;margin:0 auto}
.card{background:rgba(255,255,255,0.04);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.1);border-radius:24px;padding:28px;margin-bottom:16px}
.card-white{background:#fff;border-radius:20px;padding:28px;margin-bottom:14px;box-shadow:0 4px 24px rgba(0,0,0,0.08)}
h2{font-size:16px;font-weight:800;color:#1e293b;margin-bottom:4px}
label-s{display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px}
.inp{width:100%;padding:11px 14px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'Inter',sans-serif;color:#1e293b;outline:none;transition:border-color .2s}
.inp:focus{border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.12)}
.btn-submit{width:100%;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;border:none;border-radius:16px;padding:16px;font-size:16px;font-weight:800;cursor:pointer;font-family:'Inter',sans-serif;letter-spacing:.01em;box-shadow:0 4px 16px rgba(79,70,229,.4);transition:opacity .2s,transform .15s}
.btn-submit:hover{opacity:.92;transform:translateY(-1px)}
@media print{.no-print{display:none!important}body{background:#fff;padding:0}.card{background:#fff;border:1px solid #e5e7eb}.card-white{box-shadow:none;border:1px solid #e5e7eb}}
</style>
</head>
<body>
<div class="container">

  {{-- Brand Header --}}
  <div class="card" style="text-align:center;margin-bottom:16px">
    <div style="width:56px;height:56px;border-radius:18px;background:linear-gradient(135deg,#4f46e5,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 14px">🏫</div>
    <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">DASA EduGroup — Secure Payment Portal</div>
    <h1 style="font-size:26px;font-weight:900;color:#fff;letter-spacing:-.02em">Class {{ $class->name }}</h1>
    <p style="font-size:13px;color:rgba(255,255,255,0.55);margin-top:6px">{{ $feeBreakdown['tier'] }} Fee Structure &bull; {{ $academicYear?->name ?? '2025–2026' }}</p>
    <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;background:rgba(34,197,94,0.15);border:1px solid rgba(34,197,94,0.3);border-radius:100px;font-size:11px;font-weight:700;color:#4ade80;margin-top:10px">🔒 SSL SECURED</span>
  </div>

  {{-- Receipt --}}
  @if(session('payment_success'))
  @php $receipt = session('payment_success'); @endphp
  <div class="card" style="background:linear-gradient(135deg,#052e16,#064e3b);border:1.5px solid #4ade80;text-align:center">
    <div style="font-size:56px;margin-bottom:8px">✅</div>
    <h2 style="font-size:22px;font-weight:900;color:#4ade80;margin-bottom:4px">Payment Successful!</h2>
    <p style="font-size:14px;color:#6ee7b7;margin-bottom:24px">Fee receipt generated below.</p>
    <div style="background:rgba(255,255,255,0.07);border-radius:16px;padding:20px;text-align:left;margin-bottom:16px">
      <div style="font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:700;color:#4ade80;background:rgba(0,0,0,0.3);padding:5px 14px;border-radius:7px;border:1px dashed #4ade80;display:inline-block;margin-bottom:14px">{{ $receipt['receipt_number'] }}</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div><div style="font-size:10px;color:#6ee7b7;font-weight:700;text-transform:uppercase">Student</div><div style="font-size:13px;font-weight:700;color:#fff;margin-top:2px">{{ $receipt['student_name'] }}</div></div>
        <div><div style="font-size:10px;color:#6ee7b7;font-weight:700;text-transform:uppercase">Amount</div><div style="font-size:18px;font-weight:900;color:#4ade80;margin-top:2px;font-family:'JetBrains Mono',monospace">₹{{ number_format($receipt['amount'], 2) }}</div></div>
        <div><div style="font-size:10px;color:#6ee7b7;font-weight:700;text-transform:uppercase">Class</div><div style="font-size:13px;font-weight:700;color:#fff;margin-top:2px">{{ $receipt['class_name'] }}</div></div>
        <div><div style="font-size:10px;color:#6ee7b7;font-weight:700;text-transform:uppercase">Mode</div><div style="font-size:13px;font-weight:700;color:#fff;margin-top:2px">{{ $receipt['payment_mode'] }}</div></div>
        <div><div style="font-size:10px;color:#6ee7b7;font-weight:700;text-transform:uppercase">Txn ID</div><div style="font-size:10px;font-weight:700;color:#a7f3d0;margin-top:2px;font-family:'JetBrains Mono',monospace;word-break:break-all">{{ $receipt['transaction_id'] }}</div></div>
        <div><div style="font-size:10px;color:#6ee7b7;font-weight:700;text-transform:uppercase">Paid On</div><div style="font-size:12px;font-weight:700;color:#fff;margin-top:2px">{{ $receipt['paid_at'] }}</div></div>
      </div>
    </div>
    <button onclick="window.print()" class="no-print" style="width:100%;background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.2);border-radius:12px;padding:12px;font-size:14px;font-weight:700;cursor:pointer;font-family:'Inter',sans-serif">🖨️ Print Receipt</button>
  </div>
  @endif

  {{-- Fee Breakdown --}}
  <div class="card-white">
    <h2 style="display:flex;align-items:center;gap:8px;margin-bottom:16px"><span>📋</span> Annual Fee Breakdown <span style="margin-left:auto;font-size:11px;padding:3px 10px;background:#eef2ff;color:#4f46e5;border-radius:8px;font-weight:700">{{ strtoupper($feeBreakdown['tier']) }}</span></h2>
    <div style="border:1px solid #f1f5f9;border-radius:14px;overflow:hidden">
      @foreach([['Tuition Fee','tuition_fee'],['Book Fee','book_fee'],['Exam Fee','exam_fee'],['Lab / Computer Fee','lab_fee']] as [$lbl,$key])
      @if($feeBreakdown[$key] > 0)
      <div style="display:flex;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #f8fafc">
        <span style="font-size:13px;color:#64748b;font-weight:500">{{ $lbl }}</span>
        <span style="font-size:14px;font-weight:700;color:#1e293b;font-family:'JetBrains Mono',monospace">₹{{ number_format($feeBreakdown[$key]) }}</span>
      </div>
      @endif
      @endforeach
    </div>
    <div style="background:linear-gradient(135deg,#4f46e5,#7c3aed);border-radius:14px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;margin-top:14px">
      <div>
        <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,.7);text-transform:uppercase">Total Annual Basic Fee</div>
        <div style="font-size:11px;color:rgba(255,255,255,.5);margin-top:1px">Excl. Hostel & Transport</div>
      </div>
      <div style="font-size:26px;font-weight:900;color:#fff;font-family:'JetBrains Mono',monospace">₹{{ number_format($feeBreakdown['total_basic']) }}</div>
    </div>
  </div>

  {{-- UPI QR --}}
  <div class="card-white">
    <h2 style="display:flex;align-items:center;gap:8px;margin-bottom:4px"><span>📱</span> Pay via UPI</h2>
    <p style="font-size:13px;color:#64748b;margin-bottom:18px">Scan QR or use UPI ID below to pay from any banking app.</p>
    <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #86efac;border-radius:16px;padding:22px;text-align:center;margin-bottom:16px">
      <img src="https://chart.googleapis.com/chart?cht=qr&chl={{ urlencode($upiUrl) }}&chs=180x180&choe=UTF-8&chld=L|2"
           alt="UPI QR Code" loading="lazy"
           style="width:180px;height:180px;border-radius:12px;border:4px solid #fff;box-shadow:0 4px 16px rgba(0,0,0,.1);display:block;margin:0 auto 14px">
      <div style="font-family:'JetBrains Mono',monospace;font-size:16px;font-weight:700;color:#166534;padding:8px 18px;background:#fff;border-radius:10px;display:inline-block;border:1px dashed #4ade80;margin-bottom:6px">{{ $upiId }}</div>
      <p style="font-size:12px;color:#6b7280">Pay to <strong>{{ $schoolName }}</strong></p>
    </div>
    <div style="border:1px solid #e2e8f0;border-radius:12px;padding:12px;margin-bottom:12px">
      <div style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;margin-bottom:6px">Share this payment link</div>
      <div style="display:flex;align-items:center;gap:8px">
        <div id="payLink" style="flex:1;font-size:11px;color:#374151;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:7px 10px;word-break:break-all;font-family:'JetBrains Mono',monospace">{{ url()->current() }}</div>
        <button onclick="navigator.clipboard.writeText('{{ url()->current() }}');this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)"
                style="background:none;border:1px solid #d1d5db;border-radius:8px;padding:7px 12px;font-size:11px;font-weight:700;color:#374151;cursor:pointer;white-space:nowrap;font-family:'Inter',sans-serif">Copy</button>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
      <a href="https://wa.me/?text={{ urlencode('Pay Class ' . $class->name . ' fees via this secure link: ' . url()->current()) }}"
         target="_blank" rel="noopener"
         style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;border-radius:12px;background:#22c55e;color:#fff;text-decoration:none;font-size:13px;font-weight:700;font-family:'Inter',sans-serif">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        WhatsApp
      </a>
      <a href="sms:?body={{ urlencode('Class ' . $class->name . ' fee payment: ' . url()->current()) }}"
         style="display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;border-radius:12px;background:#3b82f6;color:#fff;text-decoration:none;font-size:13px;font-weight:700;font-family:'Inter',sans-serif">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
        SMS
      </a>
    </div>
  </div>

  {{-- Payment Form --}}
  <div class="card-white">
    <h2 style="display:flex;align-items:center;gap:8px;margin-bottom:4px"><span>💳</span> Confirm Your Payment</h2>
    <p style="font-size:13px;color:#64748b;margin-bottom:20px">After completing UPI payment, enter details below to generate your receipt.</p>
    <form action="{{ route('public.fee.process', $class->id) }}" method="POST">
      @csrf
      <div style="margin-bottom:14px">
        <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Student Full Name *</label>
        <input type="text" name="student_name" class="inp" placeholder="e.g. Arjun Sharma" required>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
        <div>
          <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Admission No.</label>
          <input type="text" name="admission_no" class="inp" placeholder="ADM-24-001 (optional)">
        </div>
        <div>
          <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Parent Phone *</label>
          <input type="tel" name="parent_phone" class="inp" placeholder="9876543210" required>
        </div>
      </div>
      <div style="margin-bottom:14px">
        <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Payment Type *</label>
        <select name="payment_type" class="inp" required>
          <option value="term_1">Term 1 Fee Only (50%)</option>
          <option value="full_year" selected>Full Year Fee</option>
          <option value="custom">Custom Amount</option>
        </select>
      </div>
      <div style="margin-bottom:14px">
        <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Amount Paid (₹) *</label>
        <input type="number" name="amount" class="inp" value="{{ $feeBreakdown['total_basic'] }}" min="1" step="1" required style="font-family:'JetBrains Mono',monospace">
      </div>
      <div style="margin-bottom:14px">
        <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px">Payment Mode *</label>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px">
          @foreach(['upi'=>['📱','UPI'],'qr'=>['📷','QR Scan'],'net_banking'=>['🏦','Net Banking'],'card'=>['💳','Card']] as $mode=>[$icon,$lbl])
          <label style="border:2px solid #e2e8f0;border-radius:12px;padding:12px;text-align:center;cursor:pointer;display:block;transition:all .15s" id="mode-{{ $mode }}">
            <input type="radio" name="payment_mode" value="{{ $mode }}" {{ $mode==='upi'?'checked':'' }} style="display:none" onchange="document.querySelectorAll('[id^=mode-]').forEach(el=>el.style.borderColor='#e2e8f0');this.parentElement.style.borderColor='#4f46e5'">
            <span style="font-size:22px;display:block;margin-bottom:4px">{{ $icon }}</span>
            <span style="font-size:11px;font-weight:700;color:#374151">{{ $lbl }}</span>
          </label>
          @endforeach
        </div>
      </div>
      <div style="margin-bottom:20px">
        <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">UPI Transaction ID (from your banking app)</label>
        <input type="text" name="transaction_ref" class="inp" placeholder="e.g. 329818001929" style="font-family:'JetBrains Mono',monospace">
      </div>
      <button type="submit" class="btn-submit">✓ Confirm &amp; Generate Receipt</button>
    </form>
  </div>

  <div style="text-align:center;padding:20px 0;color:rgba(255,255,255,.4);font-size:12px">
    🔒 Secure payment page by DASA EduGroup &bull; Data encrypted in transit
  </div>

</div>
</body>
</html>
