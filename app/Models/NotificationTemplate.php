<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'event_type', 'channel', 'subject', 'body',
        'trigger_time', 'is_active', 'updated_by',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public static array $eventTypes = [
        'fee_due'           => 'Fee Payment Reminder',
        'fee_overdue'       => 'Fee Overdue Notice',
        'attendance_absent' => 'Daily Attendance Absent',
        'exam_result'       => 'Exam Result Published',
        'library_overdue'   => 'Library Book Overdue',
        'admission_confirm' => 'Admission Confirmation',
        'birthday_wish'     => 'Student Birthday Wish',
        'discipline_action' => 'Disciplinary Action',
    ];

    public static array $defaultBodies = [
        'fee_due' => "Dear {{parent_name}},\n\nFee of ₹{{amount}} is due for {{student_name}} ({{class}}) on {{due_date}}.\n\nPlease pay at the earliest.\n\n{{school_name}}",
        'fee_overdue' => "Dear {{parent_name}},\n\nFee of ₹{{balance}} for {{student_name}} is overdue. Late charges may apply.\n\nPlease visit the school office.\n\n{{school_name}}",
        'attendance_absent' => "Dear {{parent_name}},\n\n{{student_name}} was marked absent on {{date}}.\n\nIf this is an error, please contact the school.\n\n{{school_name}}",
        'exam_result' => "Dear {{parent_name}},\n\nExam results for {{student_name}} in {{exam_name}} have been published.\nPercentage: {{percentage}}% | Grade: {{grade}} | Result: {{result}}\n\nPlease check with the school for the detailed report card.\n\n{{school_name}}",
        'library_overdue' => "Dear {{member_name}},\n\nYou have overdue library books. Total fine: ₹{{total_fine}}.\n\nPlease return the books at the earliest.\n\n{{school_name}}",
        'discipline_action' => "Dear {{parent_name}},\n\nThis is to inform you that a disciplinary action has been taken regarding {{student_name}}.\n\nAction: {{action_type}}\nDate: {{date}}\n\nPlease contact the school for further details.\n\n{{school_name}}",
    ];

    public static function forEvent(string $eventType, string $channel = 'email'): ?self
    {
        return self::where('event_type', $eventType)->where('channel', $channel)->first();
    }

    public function getRenderedBody(array $vars): string
    {
        $body = $this->body;
        foreach ($vars as $key => $value) {
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }
        return $body;
    }
}
