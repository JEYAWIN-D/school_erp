<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\OnlineExam;
use App\Models\OnlineExamAttempt;
use App\Models\OnlineExamQuestion;
use App\Models\OnlineExamResponse;
use App\Models\QuestionBank;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OnlineExamController extends Controller
{
    // ── Admin: list & manage online exams ──────────────────

    public function index(Request $request)
    {
        $classes      = Classes::active()->get();
        $academicYear = AcademicYear::current();
        $exams = OnlineExam::with(['class', 'subject'])
            ->when($request->class_id, fn($q, $v) => $q->where('class_id', $v))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderByDesc('start_time')
            ->paginate(20);
        return view('online-exams.index', compact('exams', 'classes', 'academicYear'));
    }

    public function create()
    {
        $classes      = Classes::active()->get();
        $subjects     = Subject::where('is_active', true)->orderBy('name')->get();
        $academicYear = AcademicYear::current();
        return view('online-exams.create', compact('classes', 'subjects', 'academicYear'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:200',
            'class_id'           => 'required|exists:classes,id',
            'subject_id'         => 'nullable|exists:subjects,id',
            'start_time'         => 'required|date',
            'end_time'           => 'required|date|after:start_time',
            'duration_minutes'   => 'required|integer|min:5|max:300',
            'pass_marks'         => 'nullable|numeric|min:0',
            'randomise_questions'=> 'nullable|boolean',
            'negative_marking'   => 'nullable|boolean',
            'negative_marks_per_wrong' => 'nullable|numeric|min:0',
            'instructions'       => 'nullable|string|max:2000',
        ]);

        $currentYear = AcademicYear::current();
        $exam = OnlineExam::create(array_merge($validated, [
            'academic_year_id'    => $currentYear?->id,
            'randomise_questions' => $request->boolean('randomise_questions', true),
            'negative_marking'    => $request->boolean('negative_marking'),
            'status'              => 'draft',
        ]));

        return redirect()->route('online-exams.questions', $exam->id)
            ->with('success', 'Online exam created. Now add questions.');
    }

    public function manageQuestions(Request $request, int $id)
    {
        $exam     = OnlineExam::with(['examQuestions.question', 'class'])->findOrFail($id);
        $subjects = Subject::where('is_active', true)->get();
        $bank     = QuestionBank::where('class_id', $exam->class_id)
            ->when($request->subject_id, fn($q, $v) => $q->where('subject_id', $v))
            ->when($request->question_type, fn($q, $v) => $q->where('question_type', $v))
            ->where('is_active', true)
            ->whereNotIn('id', $exam->examQuestions->pluck('question_bank_id'))
            ->orderBy('subject_id')->orderBy('chapter')
            ->paginate(30);

        $totalMarks = $exam->examQuestions->sum(fn($eq) => $eq->question->marks ?? 0);

        return view('online-exams.questions', compact('exam', 'bank', 'subjects', 'totalMarks'));
    }

    public function addQuestion(Request $request, int $id)
    {
        $exam = OnlineExam::findOrFail($id);
        $qIds = (array) $request->input('question_ids', []);
        $sort = $exam->examQuestions()->max('sort_order') ?? 0;
        foreach ($qIds as $qId) {
            OnlineExamQuestion::firstOrCreate(
                ['online_exam_id' => $exam->id, 'question_bank_id' => $qId],
                ['sort_order' => ++$sort]
            );
        }
        // Recalculate total marks
        $total = DB::table('online_exam_questions as oeq')
            ->join('question_bank as qb', 'qb.id', '=', 'oeq.question_bank_id')
            ->where('oeq.online_exam_id', $exam->id)
            ->sum('qb.marks');
        $exam->update(['total_marks' => $total]);

        return back()->with('success', count($qIds) . ' question(s) added.');
    }

    public function removeQuestion(int $examId, int $qId)
    {
        OnlineExamQuestion::where('online_exam_id', $examId)
            ->where('question_bank_id', $qId)->delete();
        $total = DB::table('online_exam_questions as oeq')
            ->join('question_bank as qb', 'qb.id', '=', 'oeq.question_bank_id')
            ->where('oeq.online_exam_id', $examId)->sum('qb.marks');
        OnlineExam::where('id', $examId)->update(['total_marks' => $total]);
        return back()->with('success', 'Question removed.');
    }

    public function publish(int $id)
    {
        $exam = OnlineExam::findOrFail($id);
        if ($exam->examQuestions()->count() === 0) {
            return back()->with('error', 'Cannot publish — no questions added yet.');
        }
        $exam->update(['status' => 'published']);
        return back()->with('success', 'Exam published. Students can now access it during the scheduled time.');
    }

    public function attempts(Request $request, int $id)
    {
        $exam     = OnlineExam::with('class')->findOrFail($id);
        $attempts = OnlineExamAttempt::with('student')
            ->where('online_exam_id', $id)
            ->orderByDesc('submitted_at')
            ->paginate(30);
        return view('online-exams.attempts', compact('exam', 'attempts'));
    }

    public function evaluateAttempt(int $attemptId)
    {
        $attempt = OnlineExamAttempt::with(['exam', 'student', 'responses.question'])->findOrFail($attemptId);
        $pending = $attempt->responses->whereNull('is_correct');
        return view('online-exams.evaluate', compact('attempt', 'pending'));
    }

    public function saveEvaluation(Request $request, int $attemptId)
    {
        $attempt = OnlineExamAttempt::with('responses.question')->findOrFail($attemptId);
        foreach ($request->input('marks', []) as $responseId => $marks) {
            $response = $attempt->responses->firstWhere('id', $responseId);
            if (!$response) continue;
            $response->update([
                'marks_awarded'    => min((float) $marks, $response->question->marks ?? 0),
                'is_correct'       => (float) $marks > 0,
                'evaluator_remarks'=> $request->input("remarks.{$responseId}"),
                'evaluated_by'     => Auth::id(),
            ]);
        }
        // Recalculate final score
        $this->recalcScore($attempt);
        return back()->with('success', 'Evaluation saved.');
    }

    protected function recalcScore(OnlineExamAttempt $attempt): void
    {
        $attempt->load('responses', 'exam');
        $score    = $attempt->responses->sum('marks_awarded');
        $negative = $attempt->exam->negative_marking
            ? $attempt->responses->where('is_correct', false)->count() * $attempt->exam->negative_marks_per_wrong
            : 0;
        $final  = max(0, $score - $negative);
        $result = $final >= ($attempt->exam->pass_marks ?? 0) ? 'pass' : 'fail';
        $attempt->update([
            'score'          => $score,
            'negative_marks' => $negative,
            'final_score'    => $final,
            'result'         => $result,
        ]);
    }

    // ── Student-facing exam UI ─────────────────────────────

    public function studentExams(Request $request)
    {
        // List exams available to the authenticated student
        $student = $request->user()?->student;
        if (!$student) abort(403, 'No student profile found.');

        $classId = $student->currentEnrollment?->class_id;
        $exams   = OnlineExam::where('class_id', $classId)
            ->where('status', 'published')
            ->orderBy('start_time')
            ->get()
            ->map(function ($exam) use ($student) {
                $exam->attempt = OnlineExamAttempt::where('online_exam_id', $exam->id)
                    ->where('student_id', $student->id)->first();
                return $exam;
            });

        return view('online-exams.student-list', compact('exams', 'student'));
    }

    public function startExam(Request $request, int $id)
    {
        $exam    = OnlineExam::with('examQuestions.question')->findOrFail($id);
        $student = $request->user()?->student;
        if (!$student) abort(403);

        // Validate exam is live
        if (!$exam->isLive()) {
            return redirect()->route('online-exams.student')->with('error', 'This exam is not currently available.');
        }

        // Check student's class
        if ($student->currentEnrollment?->class_id !== $exam->class_id) {
            abort(403, 'You are not enrolled in the exam class.');
        }

        // Get or create attempt
        $attempt = OnlineExamAttempt::firstOrCreate(
            ['online_exam_id' => $exam->id, 'student_id' => $student->id],
            ['started_at' => now(), 'result' => 'pending']
        );

        if ($attempt->submitted_at) {
            return redirect()->route('online-exams.result', $attempt->id)
                ->with('info', 'You have already submitted this exam.');
        }

        // Build randomised question order
        $qIds = $exam->examQuestions->pluck('question_bank_id')->toArray();
        if ($exam->randomise_questions) {
            shuffle($qIds);
        }
        $attempt->update(['question_order' => $qIds, 'started_at' => $attempt->started_at ?? now()]);

        $questions = QuestionBank::whereIn('id', $qIds)->get()->keyBy('id');
        $orderedQs = collect($qIds)->map(fn($qid) => $questions[$qid] ?? null)->filter();

        // Time remaining in seconds
        $elapsed  = now()->diffInSeconds($attempt->started_at);
        $maxSecs  = $exam->duration_minutes * 60;
        $remaining = max(0, $maxSecs - $elapsed);

        // Existing responses
        $responses = OnlineExamResponse::where('attempt_id', $attempt->id)
            ->pluck('answer', 'question_bank_id');

        return view('online-exams.take', compact('exam', 'attempt', 'orderedQs', 'remaining', 'responses'));
    }

    public function submitAnswer(Request $request, int $attemptId)
    {
        $attempt = OnlineExamAttempt::with('exam')->findOrFail($attemptId);
        if ($attempt->submitted_at) {
            return response()->json(['ok' => false, 'message' => 'Already submitted']);
        }

        $qId    = $request->input('question_id');
        $answer = $request->input('answer');
        $q      = QuestionBank::findOrFail($qId);

        $isCorrect = null;
        $marks     = 0;
        if (in_array($q->question_type, ['mcq', 'true_false'])) {
            $isCorrect = strtolower(trim($answer)) === strtolower(trim($q->correct_answer ?? ''));
            $marks     = $isCorrect ? ($q->marks ?? 1) : 0;
        }

        OnlineExamResponse::updateOrCreate(
            ['attempt_id' => $attemptId, 'question_bank_id' => $qId],
            ['answer' => $answer, 'is_correct' => $isCorrect, 'marks_awarded' => $marks]
        );

        return response()->json(['ok' => true]);
    }

    public function submitExam(Request $request, int $attemptId)
    {
        $attempt = OnlineExamAttempt::with('exam')->findOrFail($attemptId);
        if ($attempt->submitted_at) {
            return redirect()->route('online-exams.result', $attemptId);
        }

        $attempt->update([
            'submitted_at'   => now(),
            'auto_submitted' => $request->boolean('auto_submit'),
        ]);

        $this->recalcScore($attempt);

        return redirect()->route('online-exams.result', $attemptId);
    }

    public function result(int $attemptId)
    {
        $attempt = OnlineExamAttempt::with(['exam', 'student', 'responses.question'])->findOrFail($attemptId);
        return view('online-exams.result', compact('attempt'));
    }

    public function destroy(int $id)
    {
        abort_unless(auth()->user()->can("delete examinations"), 403);
        OnlineExam::findOrFail($id)->delete();
        return redirect()->route('online-exams.index')->with('success', 'Exam deleted.');
    }
}
