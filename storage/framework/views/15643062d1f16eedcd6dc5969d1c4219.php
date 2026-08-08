<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
  .container { max-width: 600px; margin: 0 auto; padding: 20px; }
  .header { background: #1e3a5f; color: #fff; padding: 16px 20px; border-radius: 6px 6px 0 0; }
  .content { background: #f9fafb; padding: 24px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; }
  .footer { margin-top: 16px; font-size: 12px; color: #6b7280; text-align: center; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <strong>DASA EduERP</strong>
  </div>
  <div class="content">
    <?php echo nl2br(e($body)); ?>

  </div>
  <div class="footer">This is an automated reminder. Please do not reply to this email.</div>
</div>
</body>
</html>
<?php /**PATH E:\school_erp - Copy\resources\views\emails\fee-reminder.blade.php ENDPATH**/ ?>