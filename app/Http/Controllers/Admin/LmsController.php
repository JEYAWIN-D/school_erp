<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmsCourse;
use App\Models\LmsUnit;
use App\Models\LmsLesson;
use App\Models\LmsLessonProgress;
use App\Models\LmsQuiz;
use App\Models\LmsQuizQuestion;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizAnswer;
use App\Models\LmsDiscussion;
use App\Models\LmsDiscussionReply;
use App\Models\LmsAssignment;
use App\Models\LmsAssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LmsController extends Controller
{
    // ── Courses ───────────────────────────────────────────────

    public function index()
    {
        $courses = LmsCourse::withCount(['units', 'lessons'])
            ->with('creator')
            ->latest()
            ->paginate(12);

        $totalCourses   = LmsCourse::count();
        $publishedCount = LmsCourse::where('status', 'published')->count();
        $draftCount     = LmsCourse::where('status', 'draft')->count();
        $totalLessons   = LmsLesson::count();
        $totalQuizzes   = LmsQuiz::count();
        $totalAssignments = LmsAssignment::count();

        $totalEnrollments = 0;
        try {
            $totalEnrollments = DB::table('lms_course_enrollments')->count();
        } catch (\Exception $e) {
            $totalEnrollments = LmsLessonProgress::distinct('student_id')->count('student_id');
        }

        $pendingSubmissions = 0;
        try {
            $pendingSubmissions = LmsAssignmentSubmission::whereNull('evaluated_at')->count();
        } catch (\Exception $e) {}

        return view('lms.index', compact(
            'courses', 'totalCourses', 'publishedCount', 'draftCount',
            'totalLessons', 'totalQuizzes', 'totalAssignments',
            'totalEnrollments', 'pendingSubmissions'
        ));
    }

    public function createCourse()
    {
        $classes  = DB::table('classes')->orderBy('sort_order')->get();
        $subjects = DB::table('subjects')->orderBy('name')->get();
        return view('lms.courses.create', compact('classes', 'subjects'));
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:200',
            'class_id'   => 'nullable|integer',
            'subject_id' => 'nullable|integer',
            'description'=> 'nullable|string',
            'status'     => 'required|in:draft,published,archived',
            'thumbnail'  => 'nullable|image|max:2048',
        ]);

        $data = $request->only('title', 'class_id', 'subject_id', 'description', 'status');
        $data['created_by'] = Auth::id();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('lms/thumbnails', 'public');
        }

        $course = LmsCourse::create($data);

        return redirect()->route('lms.courses.show', $course->id)
            ->with('success', 'Course created. Now add units and lessons.');
    }

    public function showCourse(LmsCourse $course)
    {
        $course->load(['units.lessons', 'quizzes', 'assignments']);
        $classes  = DB::table('classes')->orderBy('sort_order')->get();
        $subjects = DB::table('subjects')->orderBy('name')->get();
        return view('lms.courses.show', compact('course', 'classes', 'subjects'));
    }

    public function updateCourse(Request $request, LmsCourse $course)
    {
        $request->validate([
            'title'      => 'required|string|max:200',
            'status'     => 'required|in:draft,published,archived',
            'thumbnail'  => 'nullable|image|max:2048',
        ]);

        $data = $request->only('title', 'class_id', 'subject_id', 'description', 'status');

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('lms/thumbnails', 'public');
        }

        $course->update($data);
        return back()->with('success', 'Course updated.');
    }

    public function destroyCourse(LmsCourse $course)
    {
        abort_unless(auth()->user()->can('delete lms'), 403);
        if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
        $course->delete();
        return redirect()->route('lms.index')->with('success', 'Course deleted.');
    }

    // ── Units ─────────────────────────────────────────────────

    public function storeUnit(Request $request, LmsCourse $course)
    {
        $request->validate(['title' => 'required|string|max:200']);
        $order = LmsUnit::where('course_id', $course->id)->max('order') + 1;
        LmsUnit::create(['course_id' => $course->id, 'title' => $request->title, 'order' => $order]);
        return back()->with('success', 'Unit added.');
    }

    public function updateUnit(Request $request, LmsUnit $unit)
    {
        $request->validate(['title' => 'required|string|max:200']);
        $unit->update(['title' => $request->title]);
        return back()->with('success', 'Unit updated.');
    }

    public function deleteUnit(LmsUnit $unit)
    {
        $unit->delete();
        return back()->with('success', 'Unit deleted.');
    }

    public function reorderUnits(Request $request, LmsCourse $course)
    {
        foreach ($request->order ?? [] as $position => $unitId) {
            LmsUnit::where('id', $unitId)->where('course_id', $course->id)->update(['order' => $position]);
        }
        return response()->json(['ok' => true]);
    }

    public function reorderLessons(Request $request, LmsUnit $unit)
    {
        foreach ($request->order ?? [] as $position => $lessonId) {
            LmsLesson::where('id', $lessonId)->where('unit_id', $unit->id)->update(['order' => $position]);
        }
        return response()->json(['ok' => true]);
    }

    // ── Lessons ───────────────────────────────────────────────

    public function storeLesson(Request $request, LmsUnit $unit)
    {
        $request->validate([
            'title'     => 'required|string|max:200',
            'type'      => 'required|in:video,pdf,text,quiz,assignment',
            'body'      => 'nullable|string',
            'video_url' => 'nullable|url',
            'file'      => 'nullable|file|max:20480',
        ]);

        $data = $request->only('title', 'type', 'body', 'video_url');
        $data['unit_id']      = $unit->id;
        $data['is_published'] = $request->boolean('is_published');
        $data['order']        = LmsLesson::where('unit_id', $unit->id)->max('order') + 1;

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('lms/lessons', 'public');
        }

        LmsLesson::create($data);
        return back()->with('success', 'Lesson added.');
    }

    public function updateLesson(Request $request, LmsLesson $lesson)
    {
        $request->validate([
            'title'     => 'required|string|max:200',
            'type'      => 'nullable|in:video,pdf,text',
            'body'      => 'nullable|string',
            'video_url' => 'nullable|url',
            'file'      => 'nullable|file|max:20480',
        ]);

        $data = $request->only('title', 'body', 'video_url');
        if ($request->filled('type')) $data['type'] = $request->type;
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('file')) {
            if ($lesson->file_path) Storage::disk('public')->delete($lesson->file_path);
            $data['file_path'] = $request->file('file')->store('lms/lessons', 'public');
        }

        $lesson->update($data);
        return back()->with('success', 'Lesson updated.');
    }

    public function deleteLesson(LmsLesson $lesson)
    {
        if ($lesson->file_path) Storage::disk('public')->delete($lesson->file_path);
        $lesson->delete();
        return back()->with('success', 'Lesson deleted.');
    }

    public function showLesson(LmsLesson $lesson)
    {
        $lesson->load('unit.course');
        $studentId = Auth::user()->student_id;
        $isCompleted = $studentId ? $lesson->isCompletedBy($studentId) : false;
        return view('lms.lessons.show', compact('lesson', 'isCompleted'));
    }

    public function markLessonComplete(Request $request, LmsLesson $lesson)
    {
        $studentId = Auth::user()->student_id;
        if (!$studentId) return response()->json(['ok' => false], 403);

        LmsLessonProgress::firstOrCreate(
            ['student_id' => $studentId, 'lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        return response()->json(['ok' => true]);
    }

    // ── Quiz Builder ──────────────────────────────────────────

    public function quizBuilder(LmsCourse $course)
    {
        $quizzes = LmsQuiz::where('course_id', $course->id)->with('questions')->get();
        return view('lms.quiz.builder', compact('course', 'quizzes'));
    }

    public function storeQuiz(Request $request, LmsCourse $course)
    {
        $request->validate([
            'title'            => 'required|string|max:200',
            'duration_minutes' => 'required|integer|min:1',
            'available_from'   => 'nullable|date',
            'available_to'     => 'nullable|date|after:available_from',
        ]);

        $quiz = LmsQuiz::create([
            'course_id'         => $course->id,
            'title'             => $request->title,
            'duration_minutes'  => $request->duration_minutes,
            'marks_per_question'=> $request->marks_per_question ?? 1,
            'negative_marks'    => $request->negative_marks ?? 0,
            'available_from'    => $request->available_from,
            'available_to'      => $request->available_to,
            'randomise'         => $request->boolean('randomise', true),
        ]);

        return redirect()->route('lms.quiz.questions', $quiz->id)->with('success', 'Quiz created.');
    }

    public function quizQuestions(LmsQuiz $quiz)
    {
        $quiz->load('questions', 'course');
        return view('lms.quiz.questions', compact('quiz'));
    }

    public function storeQuizQuestion(Request $request, LmsQuiz $quiz)
    {
        $request->validate([
            'question'       => 'required|string',
            'type'           => 'required|in:mcq_single,mcq_multi,true_false,fill_blank',
            'correct_answer' => 'required|string',
        ]);

        $options = null;
        if (in_array($request->type, ['mcq_single', 'mcq_multi'])) {
            $options = array_values(array_filter($request->input('options', [])));
        } elseif ($request->type === 'true_false') {
            $options = ['True', 'False'];
        }

        LmsQuizQuestion::create([
            'quiz_id'        => $quiz->id,
            'question'       => $request->question,
            'type'           => $request->type,
            'options'        => $options,
            'correct_answer' => $request->correct_answer,
            'marks'          => $request->marks ?? $quiz->marks_per_question,
            'order'          => LmsQuizQuestion::where('quiz_id', $quiz->id)->max('order') + 1,
        ]);

        return back()->with('success', 'Question added.');
    }

    public function deleteQuizQuestion(LmsQuizQuestion $question)
    {
        $quizId = $question->quiz_id;
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }

    // ── Quiz Attempt (Admin/Teacher view) ─────────────────────

    public function quizAttempts(LmsQuiz $quiz)
    {
        $attempts = LmsQuizAttempt::where('quiz_id', $quiz->id)
            ->join('students', 'students.id', '=', 'lms_quiz_attempts.student_id')
            ->select('lms_quiz_attempts.*',
                DB::raw("CONCAT(students.first_name, ' ', students.last_name) as student_name"),
                'students.admission_no')
            ->orderByDesc('submitted_at')
            ->paginate(20);

        return view('lms.quiz.attempts', compact('quiz', 'attempts'));
    }

    // ── Progress Report ───────────────────────────────────────

    public function progress(LmsCourse $course)
    {
        $course->load(['units.lessons']);
        $totalLessons = $course->lessons()->where('is_published', true)->count();

        $enrollments = DB::table('student_enrollments as se')
            ->join('students as s', 's.id', '=', 'se.student_id')
            ->where('se.class_id', $course->class_id)
            ->where('se.status', 'active')
            ->select('s.id as student_id', DB::raw("CONCAT(s.first_name, ' ', s.last_name) as student_name"), 's.admission_no')
            ->get();

        $lessonIds = $course->lessons()->where('is_published', true)->pluck('lms_lessons.id');

        $completedCounts = LmsLessonProgress::whereIn('lesson_id', $lessonIds)
            ->whereIn('student_id', $enrollments->pluck('student_id'))
            ->select('student_id', DB::raw('COUNT(*) as count'))
            ->groupBy('student_id')
            ->pluck('count', 'student_id');

        return view('lms.progress', compact('course', 'enrollments', 'completedCounts', 'totalLessons'));
    }

    // ── Discussion Forum ──────────────────────────────────────

    public function forum(LmsCourse $course)
    {
        $threads = LmsDiscussion::where('course_id', $course->id)
            ->where('is_hidden', false)
            ->with(['author', 'replies.author'])
            ->withCount('replies')
            ->latest()
            ->paginate(15);

        return view('lms.discussion.forum', compact('course', 'threads'));
    }

    public function storeThread(Request $request, LmsCourse $course)
    {
        $request->validate([
            'title' => 'required|string|max:200',
            'body'  => 'required|string',
        ]);

        LmsDiscussion::create([
            'course_id' => $course->id,
            'user_id'   => Auth::id(),
            'title'     => $request->title,
            'body'       => $request->body,
        ]);

        return back()->with('success', 'Discussion posted.');
    }

    public function storeReply(Request $request, LmsDiscussion $discussion)
    {
        $request->validate(['body' => 'required|string']);

        LmsDiscussionReply::create([
            'discussion_id' => $discussion->id,
            'user_id'       => Auth::id(),
            'body'          => $request->body,
        ]);

        return back()->with('success', 'Reply posted.');
    }

    public function markAsAnswer(LmsDiscussionReply $reply)
    {
        LmsDiscussionReply::where('discussion_id', $reply->discussion_id)->update(['is_answer' => false]);
        $reply->update(['is_answer' => true]);
        $reply->discussion->update(['is_answered' => true]);
        return back()->with('success', 'Marked as accepted answer.');
    }

    public function hideThread(LmsDiscussion $discussion)
    {
        $discussion->update(['is_hidden' => true]);
        return back()->with('success', 'Thread hidden.');
    }

    // ── Assignments ───────────────────────────────────────────

    public function assignments(LmsCourse $course)
    {
        $assignments = LmsAssignment::where('course_id', $course->id)
            ->withCount('submissions')
            ->latest()
            ->get();
        return view('lms.assignments', compact('course', 'assignments'));
    }

    public function storeAssignment(Request $request, LmsCourse $course)
    {
        $request->validate([
            'title'        => 'required|string|max:200',
            'instructions' => 'required|string',
            'due_at'       => 'required|date',
            'max_marks'    => 'required|numeric|min:1',
            'attachment'   => 'nullable|file|max:10240',
        ]);

        $data = $request->only('title', 'instructions', 'due_at', 'max_marks');
        $data['course_id']   = $course->id;
        $data['created_by']  = Auth::id();

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('lms/assignment-files', 'public');
        }

        LmsAssignment::create($data);
        return back()->with('success', 'Assignment created.');
    }

    public function deleteAssignment(int $id)
    {
        $assignment = LmsAssignment::findOrFail($id);
        if ($assignment->attachment) Storage::disk('public')->delete($assignment->attachment);
        $assignment->delete();
        return back()->with('success', 'Assignment deleted.');
    }

    public function submissions(LmsAssignment $assignment)
    {
        $submissions = LmsAssignmentSubmission::where('assignment_id', $assignment->id)
            ->join('students', 'students.id', '=', 'lms_assignment_submissions.student_id')
            ->select('lms_assignment_submissions.*',
                DB::raw("CONCAT(students.first_name, ' ', students.last_name) as student_name"),
                'students.admission_no')
            ->paginate(25);
        return view('lms.submission-list', compact('assignment', 'submissions'));
    }

    public function evaluateSubmission(Request $request, LmsAssignmentSubmission $submission)
    {
        $request->validate([
            'score'    => 'required|numeric|min:0',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score'        => $request->score,
            'feedback'     => $request->feedback,
            'evaluated_at' => now(),
            'evaluated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Submission evaluated.');
    }
}
