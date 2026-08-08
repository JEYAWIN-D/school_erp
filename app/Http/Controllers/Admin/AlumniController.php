<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    public function index(Request $request)
    {
        $alumni = Alumni::when($request->search, fn($q, $v) =>
                $q->where(fn($q2) => $q2->where('first_name', 'like', "%$v%")
                    ->orWhere('last_name', 'like', "%$v%")
                    ->orWhere('current_city', 'like', "%$v%")))
            ->when($request->passing_year, fn($q, $v) => $q->where('passing_year', $v))
            ->when($request->occupation, fn($q, $v) => $q->where('current_occupation', 'like', "%$v%"))
            ->orderBy('passing_year', 'desc')->orderBy('first_name')
            ->paginate(24)->withQueryString();

        $years = Alumni::distinct()->orderBy('passing_year', 'desc')->pluck('passing_year');

        $totalAlumni    = Alumni::count();
        $verifiedCount  = Alumni::where('is_verified', true)->count();
        $latestBatch    = Alumni::max('passing_year');
        $citiesCount    = Alumni::distinct('current_city')->whereNotNull('current_city')->count('current_city');

        $stats = compact('totalAlumni', 'verifiedCount', 'latestBatch', 'citiesCount');

        return view('alumni.index', compact('alumni', 'years', 'stats'));
    }

    public function create()
    {
        $students = Student::where('status', 'inactive')
            ->orWhere('status', 'passed_out')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'admission_no']);
        return view('alumni.form', compact('students'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'          => 'nullable|exists:students,id',
            'first_name'          => 'required|string|max:100',
            'last_name'           => 'required|string|max:100',
            'email'               => 'nullable|email|max:150',
            'phone'               => 'nullable|string|max:15',
            'passing_year'        => 'required|integer|min:1990|max:' . (now()->year + 1),
            'last_class'          => 'nullable|string|max:50',
            'current_occupation'  => 'nullable|string|max:150',
            'current_employer'    => 'nullable|string|max:150',
            'current_city'        => 'nullable|string|max:100',
            'achievements'        => 'nullable|string',
            'linkedin_url'        => 'nullable|url|max:255',
            'profile_photo'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('alumni/photos', 'public');
        }

        Alumni::create($data);
        return redirect()->route('alumni.index')->with('success', 'Alumni record added.');
    }

    public function show(int $id)
    {
        $alumni = Alumni::with('student')->findOrFail($id);
        return view('alumni.show', compact('alumni'));
    }

    public function edit(int $id)
    {
        $alumni   = Alumni::findOrFail($id);
        $students = Student::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'admission_no']);
        return view('alumni.form', compact('alumni', 'students'));
    }

    public function update(Request $request, int $id)
    {
        $alumni = Alumni::findOrFail($id);
        $data   = $request->validate([
            'first_name'         => 'required|string|max:100',
            'last_name'          => 'required|string|max:100',
            'email'              => 'nullable|email|max:150',
            'phone'              => 'nullable|string|max:15',
            'passing_year'       => 'required|integer|min:1990',
            'last_class'         => 'nullable|string|max:50',
            'current_occupation' => 'nullable|string|max:150',
            'current_employer'   => 'nullable|string|max:150',
            'current_city'       => 'nullable|string|max:100',
            'achievements'       => 'nullable|string',
            'linkedin_url'       => 'nullable|url|max:255',
            'is_verified'        => 'boolean',
            'is_active'          => 'boolean',
            'profile_photo'      => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($alumni->profile_photo) Storage::disk('public')->delete($alumni->profile_photo);
            $data['profile_photo'] = $request->file('profile_photo')->store('alumni/photos', 'public');
        }

        $alumni->update($data);
        return redirect()->route('alumni.show', $id)->with('success', 'Alumni record updated.');
    }

    public function destroy(int $id)
    {
        abort_unless(auth()->user()->can("manage alumni"), 403);
        $alumni = Alumni::findOrFail($id);
        if ($alumni->profile_photo) Storage::disk('public')->delete($alumni->profile_photo);
        $alumni->delete();
        return redirect()->route('alumni.index')->with('success', 'Alumni record deleted.');
    }

    public function events(Request $request)
    {
        $events = \Illuminate\Support\Facades\DB::table('alumni_events')
            ->orderByDesc('event_date')
            ->paginate(20);
        return view('alumni.events', compact('events'));
    }

    public function eventStore(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:200',
            'event_date'  => 'required|date',
            'description' => 'nullable|string',
            'venue'       => 'nullable|string|max:200',
        ]);
        \Illuminate\Support\Facades\DB::table('alumni_events')->insert(array_merge($data, [
            'is_published' => false,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]));
        return back()->with('success', 'Event created.');
    }

    public function eventTogglePublish(int $id)
    {
        $event = \Illuminate\Support\Facades\DB::table('alumni_events')->find($id);
        if (!$event) abort(404);
        \Illuminate\Support\Facades\DB::table('alumni_events')
            ->where('id', $id)
            ->update(['is_published' => !$event->is_published, 'updated_at' => now()]);
        return back()->with('success', $event->is_published ? 'Event unpublished.' : 'Event published.');
    }

    public function eventDestroy(int $id)
    {
        \Illuminate\Support\Facades\DB::table('alumni_event_rsvps')->where('event_id', $id)->delete();
        \Illuminate\Support\Facades\DB::table('alumni_events')->where('id', $id)->delete();
        return back()->with('success', 'Event deleted.');
    }

    public function directory(Request $request)
    {
        $alumni = Alumni::active()
            ->when($request->search, fn($q, $v) => $q->where(fn($q2) =>
                $q2->where('first_name','like',"%$v%")->orWhere('last_name','like',"%$v%")->orWhere('current_city','like',"%$v%")))
            ->when($request->passing_year, fn($q, $v) => $q->where('passing_year', $v))
            ->when($request->city, fn($q, $v) => $q->where('current_city', 'like', "%$v%"))
            ->orderBy('passing_year', 'desc')->orderBy('first_name')
            ->paginate(24)->withQueryString();

        $years = Alumni::active()->distinct()->orderBy('passing_year', 'desc')->pluck('passing_year');
        return view('alumni.directory', compact('alumni', 'years'));
    }
}
