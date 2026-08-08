<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
  .container { max-width: 600px; margin: 0 auto; padding: 20px; }
  .header { background: #1e3a5f; color: #fff; padding: 16px 20px; border-radius: 6px 6px 0 0; }
  .content { background: #f9fafb; padding: 24px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; }
  table { width: 100%; border-collapse: collapse; margin: 16px 0; }
  th { background: #f3f4f6; text-align: left; padding: 8px 12px; font-size: 12px; color: #6b7280; text-transform: uppercase; }
  td { padding: 8px 12px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
  .fine { color: #dc2626; font-weight: 700; }
  .footer { margin-top: 16px; font-size: 12px; color: #6b7280; text-align: center; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <strong><?php echo e($schoolName); ?></strong> — Library Overdue Notice
  </div>
  <div class="content">
    <p>Dear <strong><?php echo e($memberName); ?></strong>,</p>
    <p>This is a reminder that the following library book(s) are overdue. Please return them at the earliest to avoid additional fines.</p>
    <table>
      <thead>
        <tr>
          <th>Book Title</th>
          <th>Due Date</th>
          <th>Fine (₹)</th>
        </tr>
      </thead>
      <tbody>
        <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td><?php echo e($book['title']); ?></td>
          <td><?php echo e($book['due_date']); ?></td>
          <td class="fine">₹<?php echo e(number_format($book['fine'], 2)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td colspan="2"><strong>Total Fine</strong></td>
          <td class="fine"><strong>₹<?php echo e(number_format($totalFine, 2)); ?></strong></td>
        </tr>
      </tbody>
    </table>
    <p>Please visit the library counter to return the books and clear the fine.</p>
    <p>Regards,<br><strong>Library, <?php echo e($schoolName); ?></strong></p>
  </div>
  <div class="footer">This is an automated reminder. Please do not reply to this email.</div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\emails\library-overdue.blade.php ENDPATH**/ ?>