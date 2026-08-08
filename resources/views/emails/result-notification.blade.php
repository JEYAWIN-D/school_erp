<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
  .container { max-width: 600px; margin: 0 auto; padding: 20px; }
  .header { background: #1e3a5f; color: #fff; padding: 16px 20px; border-radius: 6px 6px 0 0; }
  .content { background: #f9fafb; padding: 24px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; }
  .result-box { background: #fff; border: 1px solid #d1d5db; border-radius: 8px; padding: 16px; margin: 16px 0; }
  .stat { display: inline-block; text-align: center; padding: 8px 20px; margin: 4px; }
  .stat-value { font-size: 22px; font-weight: 700; }
  .pass { color: #16a34a; } .fail { color: #dc2626; }
  .footer { margin-top: 16px; font-size: 12px; color: #6b7280; text-align: center; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <strong>{{ $schoolName }}</strong> — Exam Result Notification
  </div>
  <div class="content">
    <p>Dear Parent/Guardian,</p>
    <p>We are pleased to share the exam result for <strong>{{ $studentName }}</strong> for <strong>{{ $examName }}</strong>.</p>
    <div class="result-box">
      <div class="stat">
        <div class="stat-value">{{ $percentage }}%</div>
        <div style="font-size:12px;color:#6b7280;">Percentage</div>
      </div>
      <div class="stat">
        <div class="stat-value">{{ $grade }}</div>
        <div style="font-size:12px;color:#6b7280;">Grade</div>
      </div>
      <div class="stat">
        <div class="stat-value {{ strtolower($result) }}">{{ $result }}</div>
        <div style="font-size:12px;color:#6b7280;">Result</div>
      </div>
    </div>
    <p>The detailed report card is attached as a PDF to this email.</p>
    <p>For any queries, please contact the school office.</p>
    <br>
    <p>Regards,<br><strong>{{ $schoolName }}</strong></p>
  </div>
  <div class="footer">This is an automated message. Please do not reply to this email.</div>
</div>
</body>
</html>
