<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\StudentEnrollment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamAndMarksSeeder extends Seeder
{
    public function run()
    {
        $ay = AcademicYear::current() ?? AcademicYear::first();
        if (!$ay) return;

        $targetClasses = Classes::whereIn('name', ['IX', 'VIII', 'VII', 'X', 'VI'])
            ->orWhereIn('id', [24, 23, 22])
            ->take(3)
            ->get();

        if ($targetClasses->isEmpty()) {
            $targetClasses = Classes::take(3)->get();
        }

        $examsData = [
            [
                'name' => 'Unit Test 1 (July 2025)',
                'type' => 'unit_test',
                'term_label' => 'Term 1',
                'start_date' => Carbon::create(2025, 7, 15),
                'end_date' => Carbon::create(2025, 7, 22),
                'passing_percentage' => 35,
                'is_published' => true,
                'result_published' => true,
                'weightage_percent' => 20,
                'is_cumulative_component' => true,
            ],
            [
                'name' => 'Quarterly Examination (September 2025)',
                'type' => 'term',
                'term_label' => 'Term 1',
                'start_date' => Carbon::create(2025, 9, 18),
                'end_date' => Carbon::create(2025, 9, 28),
                'passing_percentage' => 35,
                'is_published' => true,
                'result_published' => true,
                'weightage_percent' => 30,
                'is_cumulative_component' => true,
            ],
            [
                'name' => 'Half-Yearly Examination (December 2025)',
                'type' => 'term',
                'term_label' => 'Term 2',
                'start_date' => Carbon::create(2025, 12, 10),
                'end_date' => Carbon::create(2025, 12, 22),
                'passing_percentage' => 35,
                'is_published' => true,
                'result_published' => true,
                'weightage_percent' => 50,
                'is_cumulative_component' => true,
            ],
        ];

        $now = now();
        $allMarksToInsert = [];

        foreach ($examsData as $eData) {
            $exam = Exam::firstOrCreate(
                ['academic_year_id' => $ay->id, 'name' => $eData['name']],
                $eData
            );

            foreach ($targetClasses as $cls) {
                $subjects = $cls->subjects()->take(5)->get();
                if ($subjects->isEmpty()) {
                    $subjects = \App\Models\Subject::take(5)->get();
                }

                $enrollments = StudentEnrollment::where('class_id', $cls->id)
                    ->where('status', 'active')
                    ->where('academic_year_id', $ay->id)
                    ->with('student')
                    ->get();

                if ($enrollments->isEmpty()) {
                    $enrollments = StudentEnrollment::where('class_id', $cls->id)->take(25)->with('student')->get();
                }

                $dayOffset = 0;
                foreach ($subjects as $sub) {
                    $examDate = Carbon::parse($exam->start_date)->addDays($dayOffset++);
                    $schedule = ExamSchedule::firstOrCreate(
                        [
                            'exam_id' => $exam->id,
                            'class_id' => $cls->id,
                            'subject_id' => $sub->id,
                        ],
                        [
                            'exam_date' => $examDate,
                            'start_time' => '09:30:00',
                            'end_time' => '12:30:00',
                            'total_marks' => 100,
                            'passing_marks' => 35,
                            'room' => 'Hall ' . (100 + $cls->id),
                        ]
                    );

                    $isMath = stripos($sub->name, 'math') !== false;
                    $isScience = stripos($sub->name, 'sci') !== false;

                    foreach ($enrollments as $index => $enr) {
                        if (($isMath && ($index === 5 || $index === 12)) || ($isScience && ($index === 7 || $index === 15))) {
                            $mark = rand(20, 32); // Fail in this subject (< 35)
                        } elseif ($index < 3) {
                            $mark = rand(88, 98);
                        } elseif ($index < 10) {
                            $mark = rand(70, 85);
                        } elseif ($index < 20) {
                            $mark = rand(50, 69);
                        } else {
                            $mark = rand(36, 75);
                        }

                        $grade = match(true) {
                            $mark >= 90 => 'A1',
                            $mark >= 80 => 'A2',
                            $mark >= 70 => 'B1',
                            $mark >= 60 => 'B2',
                            $mark >= 50 => 'C1',
                            $mark >= 35 => 'C2',
                            default => 'E',
                        };

                        $allMarksToInsert[] = [
                            'exam_schedule_id' => $schedule->id,
                            'student_id'       => $enr->student_id,
                            'marks_obtained'   => $mark,
                            'is_absent'        => false,
                            'grade'            => $grade,
                            'remarks'          => $mark >= 35 ? 'Good effort' : 'Needs remediation and remedial coaching',
                            'created_at'       => $now,
                            'updated_at'       => $now,
                        ];
                    }
                }
            }
        }

        // Bulk upsert in batches of 200
        foreach (array_chunk($allMarksToInsert, 200) as $chunk) {
            DB::table('exam_marks')->upsert(
                $chunk,
                ['exam_schedule_id', 'student_id'],
                ['marks_obtained', 'is_absent', 'grade', 'remarks', 'updated_at']
            );
        }
    }
}
