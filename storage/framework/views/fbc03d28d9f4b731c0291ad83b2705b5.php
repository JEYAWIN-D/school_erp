<?php $__env->startSection('title', 'Issue Book'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto space-y-6">
  <div class="flex items-center gap-4">
    <a href="<?php echo e(route('library.index')); ?>" class="btn-icon">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    </a>
    <h1 class="page-title">Issue Book</h1>
  </div>

  <?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  
  <div class="card" x-data="barcodeScanner()" x-init="init()">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-sm font-semibold text-slate-700">Barcode / QR Scanner</h2>
      <button type="button" @click="toggleScanner()" class="btn-sm btn-secondary" x-text="scanning ? 'Stop Scanner' : 'Start Camera Scan'"></button>
    </div>
    <p class="text-xs text-slate-500 mb-3">Scan a book's barcode or a member's QR code to auto-fill the form below.</p>
    <div x-show="scanning" x-transition class="space-y-3">
      <div class="relative bg-black rounded-lg overflow-hidden" style="max-height:260px;">
        <video id="scanner-video" class="w-full" autoplay playsinline></video>
        <canvas id="scanner-canvas" class="hidden"></canvas>
        <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
          <div class="border-2 border-green-400 w-48 h-24 rounded opacity-80"></div>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div x-show="lastScan" class="flex-1 bg-green-50 border border-green-300 rounded px-3 py-2">
          <p class="text-xs text-green-700 font-medium">Last scan: <span x-text="lastScan" class="font-mono"></span></p>
        </div>
        <div x-show="scanStatus" class="text-xs" :class="scanStatus === 'ok' ? 'text-green-600' : 'text-red-600'" x-text="scanMessage"></div>
      </div>
    </div>

    
    <div class="mt-3 flex gap-2">
      <input type="text" x-model="manualBarcode" @keydown.enter.prevent="lookupBarcode(manualBarcode)"
        placeholder="Or type / paste barcode and press Enter" class="input flex-1 font-mono text-sm">
      <button type="button" @click="lookupBarcode(manualBarcode)" class="btn-sm btn-primary">Lookup</button>
    </div>
    <div x-show="lookupResult" x-transition class="mt-2 bg-blue-50 border border-blue-200 rounded px-3 py-2 text-sm text-blue-800" x-html="lookupResult"></div>
  </div>

  <form method="POST" action="<?php echo e(route('library.issue.store')); ?>" class="card space-y-4"
    id="issue-form"
    x-data="{
      memberType: '<?php echo e(old('member_type','student')); ?>',
      studentLoanDays: <?php echo e($studentLoanDays); ?>,
      staffLoanDays: <?php echo e($staffLoanDays); ?>,
      get defaultDueDate() {
        const days = this.memberType === 'staff' ? this.staffLoanDays : this.studentLoanDays;
        const d = new Date(); d.setDate(d.getDate() + days);
        return d.toISOString().split('T')[0];
      }
    }">
    <?php echo csrf_field(); ?>
    <div><label class="label">Book <span class="text-red-500">*</span></label>
      <input type="text" id="book-search" placeholder="Search by title, ISBN or accession…" class="input mb-1 text-sm"
             oninput="filterSelect('book-select', this.value)">
      <select name="book_id" id="book-select" class="select <?php $__errorArgs = ['book_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" size="1">
        <option value="">Select a book</option>
        <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($book->id); ?>" <?php if(old('book_id') == $book->id): echo 'selected'; endif; ?>
            data-isbn="<?php echo e($book->isbn); ?>" data-accession="<?php echo e($book->accession_number ?? ''); ?>">
            <?php echo e($book->title); ?> (<?php echo e($book->available_copies); ?> available)
          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <?php $__errorArgs = ['book_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
      <label class="label">Issue To</label>
      <div class="flex gap-3 mb-3">
        <label class="flex items-center gap-2 text-sm cursor-pointer">
          <input type="radio" name="member_type" value="student" x-model="memberType" class="w-4 h-4"> Student
        </label>
        <label class="flex items-center gap-2 text-sm cursor-pointer">
          <input type="radio" name="member_type" value="staff" x-model="memberType" class="w-4 h-4"> Staff (HR)
        </label>
      </div>
    </div>

    <div x-show="memberType === 'student'">
      <label class="label">Student <span class="text-red-500">*</span></label>
      <input type="text" id="student-search" placeholder="Search by name or admission no…" class="input mb-1 text-sm"
             oninput="filterSelect('student-select', this.value)">
      <select name="student_id" id="student-select" class="select <?php $__errorArgs = ['student_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        <option value="">Select student</option>
        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($s->id); ?>" <?php if(old('student_id') == $s->id): echo 'selected'; endif; ?>
            data-adm="<?php echo e($s->admission_number); ?>"><?php echo e($s->full_name); ?> (<?php echo e($s->admission_number); ?>)</option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <?php $__errorArgs = ['student_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div x-show="memberType === 'staff'">
      <label class="label">Staff Member <span class="text-red-500">*</span></label>
      <select name="employee_id" id="employee-select" class="select <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        <option value="">Select staff</option>
        <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($emp->id); ?>" <?php if(old('employee_id') == $emp->id): echo 'selected'; endif; ?>
            data-empno="<?php echo e($emp->employee_number); ?>">
            <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?> (<?php echo e($emp->employee_number); ?>) — <?php echo e($emp->designation ?? $emp->department ?? ''); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <?php $__errorArgs = ['employee_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div><label class="label">Due Date <span class="text-red-500">*</span></label>
      <input type="date" name="due_date" :value="'<?php echo e(old('due_date')); ?>' || defaultDueDate"
             class="input <?php $__errorArgs = ['due_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> input-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" min="<?php echo e(now()->addDay()->toDateString()); ?>">
    </div>
    <div class="flex justify-end gap-3 pt-2">
      <a href="<?php echo e(route('library.index')); ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Issue Book</button>
    </div>
  </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/zxing-js/0.20.0/zxing.min.js" crossorigin="anonymous"></script>
<script>
function barcodeScanner() {
  return {
    scanning: false,
    lastScan: '',
    scanStatus: '',
    scanMessage: '',
    manualBarcode: '',
    lookupResult: '',
    codeReader: null,
    videoStream: null,

    init() {
      // ZXing loads asynchronously; check availability
    },

    async toggleScanner() {
      if (this.scanning) {
        this.stopScanner();
      } else {
        await this.startScanner();
      }
    },

    async startScanner() {
      try {
        if (typeof ZXing === 'undefined') {
          this.scanMessage = 'ZXing library not loaded. Use manual entry.';
          this.scanStatus = 'err';
          return;
        }
        this.codeReader = new ZXing.BrowserMultiFormatReader();
        const videoEl = document.getElementById('scanner-video');
        const devices = await this.codeReader.listVideoInputDevices();
        if (!devices.length) {
          this.scanMessage = 'No camera found.';
          this.scanStatus = 'err';
          return;
        }
        // Prefer back camera
        const device = devices.find(d => /back|rear|environment/i.test(d.label)) || devices[devices.length - 1];
        this.scanning = true;
        this.codeReader.decodeFromVideoDevice(device.deviceId, videoEl, (result, err) => {
          if (result) {
            const code = result.getText();
            this.lastScan = code;
            this.lookupBarcode(code);
          }
        });
      } catch (e) {
        this.scanMessage = 'Camera error: ' + e.message;
        this.scanStatus = 'err';
      }
    },

    stopScanner() {
      if (this.codeReader) {
        this.codeReader.reset();
        this.codeReader = null;
      }
      this.scanning = false;
    },

    lookupBarcode(code) {
      if (!code) return;
      const trimmed = code.trim();

      // Try to match book by ISBN or accession number
      const bookSelect = document.getElementById('book-select');
      let bookFound = false;
      for (const opt of bookSelect.options) {
        if (opt.dataset.isbn === trimmed || opt.dataset.accession === trimmed || opt.value === trimmed) {
          bookSelect.value = opt.value;
          bookFound = true;
          this.lookupResult = `<strong>Book found:</strong> ${opt.text}`;
          this.scanStatus = 'ok'; this.scanMessage = 'Book matched!';
          break;
        }
      }

      if (!bookFound) {
        // Try student admission number
        const stuSelect = document.getElementById('student-select');
        let stuFound = false;
        for (const opt of stuSelect.options) {
          if (opt.dataset.adm === trimmed || opt.value === trimmed) {
            stuSelect.value = opt.value;
            stuFound = true;
            // Set radio to student
            document.querySelector('input[name=member_type][value=student]').click();
            this.lookupResult = `<strong>Student found:</strong> ${opt.text}`;
            this.scanStatus = 'ok'; this.scanMessage = 'Member matched!';
            break;
          }
        }

        if (!stuFound) {
          // Try employee number
          const empSelect = document.getElementById('employee-select');
          for (const opt of empSelect.options) {
            if (opt.dataset.empno === trimmed || opt.value === trimmed) {
              empSelect.value = opt.value;
              document.querySelector('input[name=member_type][value=staff]').click();
              this.lookupResult = `<strong>Staff found:</strong> ${opt.text}`;
              this.scanStatus = 'ok'; this.scanMessage = 'Member matched!';
              return;
            }
          }
          this.lookupResult = `<span class="text-red-600">No book, student, or staff found for barcode: <strong>${trimmed}</strong></span>`;
          this.scanStatus = 'err';
          this.scanMessage = '';
        }
      }

      this.manualBarcode = '';
    },
  };
}

function filterSelect(selectId, query) {
  const sel = document.getElementById(selectId);
  const q = query.toLowerCase();
  Array.from(sel.options).forEach(opt => {
    if (!opt.value) { opt.hidden = false; return; }
    opt.hidden = !opt.text.toLowerCase().includes(q);
  });
  if (q && !sel.value) sel.value = '';
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\library\issue.blade.php ENDPATH**/ ?>