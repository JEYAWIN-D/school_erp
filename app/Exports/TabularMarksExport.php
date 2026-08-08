<?php
namespace App\Exports;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\StudentEnrollment;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TabularMarksExport implements FromArray, WithHeadings
{
    private array $data = [];
    private array $headers = ['Roll No','Student Name'];

    public function __construct(int $examId, int $classId)
    {
        $exam = Exam::with('schedules.subject')->find($examId);
        if (!$exam) return;

        $schedules = $exam->schedules()->where('class_id', $classId)->with('subject')->get();
        foreach ($schedules as $s) {
            $this->headers[] = $s->subject?->name . "\n(Max:{$s->max_marks})";
        }
        $this->headers[] = 'Total';
        $this->headers[] = 'Percentage';
        $this->headers[] = 'Grade';
        $this->headers[] = 'Result';

        $enrollments = StudentEnrollment::with('student')
            ->where('class_id', $classId)->where('status','active')
            ->orderBy('roll_number')->get();

        foreach ($enrollments as $e) {
            $row = [$e->roll_number, $e->student?->full_name];
            $total = 0; $maxTotal = 0;
            foreach ($schedules as $s) {
                $mark = ExamMark::where('exam_schedule_id',$s->id)->where('student_id',$e->student_id)->first();
                $val = $mark?->is_absent ? 'A' : ($mark?->marks_obtained ?? '-');
                $row[] = $val;
                if (is_numeric($val)) { $total += $val; $maxTotal += $s->max_marks; }
            }
            $pct = $maxTotal > 0 ? round($total / $maxTotal * 100, 1) : 0;
            $row[] = $total;
            $row[] = $pct.'%';
            $row[] = $pct >= 90 ? 'A1' : ($pct >= 80 ? 'A2' : ($pct >= 70 ? 'B1' : ($pct >= 60 ? 'B2' : ($pct >= 50 ? 'C1' : ($pct >= 40 ? 'C2' : 'F')))));
            $row[] = $pct >= 35 ? 'Pass' : 'Fail';
            $this->data[] = $row;
        }
    }

    public function array(): array { return $this->data; }
    public function headings(): array { return $this->headers; }
}
