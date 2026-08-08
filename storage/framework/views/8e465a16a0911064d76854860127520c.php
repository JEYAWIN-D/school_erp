<?php $__env->startSection('title', 'Log Visitor'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl space-y-6" x-data="gateForm()">
  <div class="flex items-center justify-between">
    <h1 class="page-title">Log Visitor</h1>
    <a href="<?php echo e(route('gate.index')); ?>" class="btn-sm btn-secondary">← Gate</a>
  </div>

  <?php if($errors->any()): ?> <div class="alert-danger"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($e); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div> <?php endif; ?>

  <form method="POST" action="<?php echo e(route('gate.store')); ?>" class="card space-y-4" @submit="capturePhoto">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="photo_data" x-ref="photoData">

    <div class="grid grid-cols-2 gap-4">
      <div class="col-span-2">
        <label class="label">Visitor Name <span class="text-red-500">*</span></label>
        <input type="text" name="visitor_name" class="input" required autofocus>
      </div>
      <div>
        <label class="label">Phone</label>
        <input type="tel" name="visitor_phone" class="input">
      </div>
      <div>
        <label class="label">Vehicle Number</label>
        <input type="text" name="vehicle_number" class="input" placeholder="e.g. MH12AB1234">
      </div>
      <div>
        <label class="label">ID Type</label>
        <select name="visitor_id_type" class="select">
          <option value="">Select</option>
          <option>Aadhaar</option><option>PAN</option><option>Driving Licence</option><option>Passport</option><option>Voter ID</option>
        </select>
      </div>
      <div>
        <label class="label">ID Number</label>
        <input type="text" name="visitor_id_number" class="input">
      </div>
      <div class="col-span-2">
        <label class="label">Purpose <span class="text-red-500">*</span></label>
        <input type="text" name="purpose" class="input" required>
      </div>
      <div>
        <label class="label">Whom to Meet</label>
        <input type="text" name="whom_to_meet" class="input" list="staff-list" autocomplete="off" placeholder="Search staff…">
        <datalist id="staff-list">
          <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($s->first_name); ?> <?php echo e($s->last_name); ?><?php echo e($s->designation ? ' ('.$s->designation.')' : ''); ?>">
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </datalist>
      </div>
      <div>
        <label class="label">Department</label>
        <input type="text" name="department" class="input">
      </div>
      <div class="col-span-2">
        <label class="label">Remarks</label>
        <textarea name="remarks" rows="2" class="input"></textarea>
      </div>
    </div>

    
    <div>
      <label class="label">Visitor Photo (optional)</label>
      <div class="flex gap-4 items-start">
        <video x-ref="video" class="rounded-xl w-40 h-32 object-cover bg-slate-100" autoplay playsinline x-show="streaming"></video>
        <canvas x-ref="canvas" class="rounded-xl w-40 h-32 object-cover hidden"></canvas>
        <img x-ref="preview" class="rounded-xl w-40 h-32 object-cover" x-show="captured" alt="Preview">
        <div class="space-y-2">
          <button type="button" @click="startCamera" x-show="!streaming && !captured" class="btn-sm btn-secondary">Start Camera</button>
          <button type="button" @click="snap" x-show="streaming" class="btn-sm btn-primary">📸 Capture</button>
          <button type="button" @click="retake" x-show="captured" class="btn-sm btn-secondary">Retake</button>
        </div>
      </div>
    </div>

    <div class="flex gap-2">
      <button type="submit" class="btn-primary">Log Visitor &amp; Print Pass</button>
      <a href="<?php echo e(route('gate.index')); ?>" class="btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function gateForm() {
  return {
    streaming: false, captured: false, stream: null,
    async startCamera() {
      try {
        this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        this.$refs.video.srcObject = this.stream;
        this.streaming = true;
      } catch(e) { alert('Camera not available: ' + e.message); }
    },
    snap() {
      const v = this.$refs.video, c = this.$refs.canvas;
      c.width = v.videoWidth; c.height = v.videoHeight;
      c.getContext('2d').drawImage(v, 0, 0);
      const data = c.toDataURL('image/jpeg', 0.8);
      this.$refs.photoData.value = data;
      this.$refs.preview.src = data;
      this.captured = true; this.streaming = false;
      if (this.stream) this.stream.getTracks().forEach(t => t.stop());
    },
    retake() { this.captured = false; this.$refs.photoData.value = ''; this.startCamera(); },
    capturePhoto() { /* already set */ },
  };
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_erp - Copy\resources\views\gate\create.blade.php ENDPATH**/ ?>