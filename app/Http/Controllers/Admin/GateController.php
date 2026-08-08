<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GateVisitor;
use App\Models\GateBlacklist;
use App\Models\StudentOutpass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GateController extends Controller
{
    public function index(Request $request)
    {
        $visitors = GateVisitor::with('loggedBy')
            ->when($request->date, fn($q, $v) => $q->whereDate('in_time', $v))
            ->when($request->search, fn($q, $v) => $q->where(fn($q2) =>
                $q2->where('visitor_name', 'like', "%$v%")->orWhere('visitor_phone', 'like', "%$v%")))
            ->when($request->inside_only, fn($q) => $q->whereNull('out_time'))
            ->latest('in_time')->paginate(30)->withQueryString();

        $insideCount    = GateVisitor::whereNull('out_time')->count();
        $todayTotal     = GateVisitor::whereDate('in_time', today())->count();
        $blacklistCount = GateBlacklist::where('is_active', true)->count();

        $pendingOutpasses = 0;
        try {
            $pendingOutpasses = StudentOutpass::where('status', 'overdue')->count();
        } catch (\Exception $e) {}

        return view('gate.index', compact('visitors', 'insideCount', 'todayTotal', 'blacklistCount', 'pendingOutpasses'));
    }

    public function create()
    {
        $staff = \App\Models\Employee::where('is_active', true)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'designation']);
        return view('gate.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'visitor_name'    => 'required|string|max:150',
            'visitor_phone'   => 'nullable|string|max:15',
            'visitor_id_type' => 'nullable|string|max:30',
            'visitor_id_number'=> 'nullable|string|max:30',
            'purpose'         => 'required|string|max:200',
            'whom_to_meet'    => 'nullable|string|max:100',
            'department'      => 'nullable|string|max:100',
            'vehicle_number'  => 'nullable|string|max:20',
            'remarks'         => 'nullable|string',
            'photo_data'      => 'nullable|string',
        ]);

        // Blacklist check
        if (GateBlacklist::isBlacklisted($data['visitor_phone'] ?? null, $data['visitor_id_number'] ?? null)) {
            return back()->withErrors(['visitor_phone' => 'This visitor is blacklisted.'])->withInput();
        }

        // Save webcam photo
        if (!empty($data['photo_data']) && str_starts_with($data['photo_data'], 'data:image')) {
            $base64 = explode(',', $data['photo_data'])[1];
            $filename = 'gate/visitors/' . uniqid() . '.jpg';
            Storage::disk('public')->put($filename, base64_decode($base64));
            $data['visitor_photo'] = $filename;
        }
        unset($data['photo_data']);

        $data['logged_by'] = Auth::id();
        $visitor = GateVisitor::create($data);

        return redirect()->route('gate.pass', $visitor->id)->with('success', 'Visitor logged. Pass generated.');
    }

    public function pass(int $id)
    {
        $visitor = GateVisitor::with('loggedBy')->findOrFail($id);
        return view('gate.pass', compact('visitor'));
    }

    public function checkout(int $id)
    {
        $visitor = GateVisitor::findOrFail($id);
        $visitor->update(['out_time' => now()]);
        return back()->with('success', 'Visitor checked out at ' . now()->format('h:i A'));
    }

    public function report(Request $request)
    {
        $from = $request->from ?? today()->toDateString();
        $to   = $request->to   ?? today()->toDateString();

        $visitors    = GateVisitor::whereBetween(\DB::raw('DATE(in_time)'), [$from, $to])
            ->orderBy('in_time')->get();
        $totalIn     = $visitors->count();
        $totalOut    = $visitors->whereNotNull('out_time')->count();
        $stillInside = $visitors->whereNull('out_time')->count();

        if ($request->export === 'csv') {
            $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=gate-report-$from-to-$to.csv"];
            $callback = function () use ($visitors) {
                $f = fopen('php://output', 'w');
                fputcsv($f, ['#', 'Name', 'Phone', 'Purpose', 'Whom to Meet', 'Vehicle', 'In Time', 'Out Time']);
                foreach ($visitors as $i => $v) {
                    fputcsv($f, [$i+1, $v->visitor_name, $v->visitor_phone, $v->purpose, $v->whom_to_meet, $v->vehicle_number, $v->in_time->format('d/m/Y H:i'), $v->out_time?->format('H:i') ?? '—']);
                }
                fclose($f);
            };
            return response()->stream($callback, 200, $headers);
        }

        return view('gate.report', compact('visitors', 'from', 'to', 'totalIn', 'totalOut', 'stillInside'));
    }

    // ── Blacklist ────────────────────────────────────────────

    public function blacklist(Request $request)
    {
        $list = GateBlacklist::with('addedBy')
            ->when($request->search, fn($q, $v) => $q->where(fn($q2) => $q2->where('name', 'like', "%$v%")->orWhere('phone', 'like', "%$v%")->orWhere('id_number', 'like', "%$v%")))
            ->latest()->paginate(20)->withQueryString();
        return view('gate.blacklist', compact('list'));
    }

    public function storeBlacklist(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:150',
            'phone'     => 'nullable|string|max:15',
            'id_number' => 'nullable|string|max:30',
            'reason'    => 'required|string',
        ]);
        $data['added_by'] = Auth::id();
        GateBlacklist::create($data);
        return back()->with('success', 'Added to blacklist.');
    }

    public function removeBlacklist(int $id)
    {
        GateBlacklist::findOrFail($id)->update(['is_active' => false]);
        return back()->with('success', 'Removed from blacklist.');
    }

    // ── Outpass ──────────────────────────────────────────────

    public function outpass(Request $request)
    {
        $outpasses = StudentOutpass::with('student')
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->latest('out_time')->paginate(20)->withQueryString();

        // Auto-mark overdue
        StudentOutpass::where('status', 'active')
            ->where('expected_return', '<', now())
            ->update(['status' => 'overdue']);

        return view('gate.outpass', compact('outpasses'));
    }

    public function createOutpass()
    {
        $students = Student::where('status', 'active')->orderBy('first_name')->get(['id','first_name','last_name','admission_no']);
        $passNumber = 'OP-' . now()->format('Ymd') . '-' . str_pad(
            StudentOutpass::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        return view('gate.outpass-form', compact('students', 'passNumber'));
    }

    public function storeOutpass(Request $request)
    {
        $data = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'pass_number'     => 'required|unique:student_outpasses,pass_number',
            'out_time'        => 'required|date',
            'expected_return' => 'nullable|date|after:out_time',
            'reason'          => 'required|string|max:200',
            'authorized_by'   => 'required|string|max:100',
        ]);
        $data['issued_by'] = Auth::id();
        $data['status']    = 'active';
        StudentOutpass::create($data);
        return redirect()->route('gate.outpass')->with('success', 'Outpass issued.');
    }

    public function returnOutpass(int $id)
    {
        StudentOutpass::findOrFail($id)->update([
            'actual_return' => now(),
            'status'        => 'returned',
        ]);
        return back()->with('success', 'Student returned.');
    }

    public function verifyPass(string $token)
    {
        $visitor = GateVisitor::where('pass_token', $token)->first();
        if (!$visitor) {
            return response()->json(['valid' => false, 'message' => 'Pass not found.'], 404);
        }
        return response()->json([
            'valid'        => true,
            'name'         => $visitor->visitor_name,
            'phone'        => $visitor->visitor_phone,
            'purpose'      => $visitor->purpose,
            'whom_to_meet' => $visitor->whom_to_meet,
            'in_time'      => $visitor->in_time?->format('d M Y H:i'),
            'out_time'     => $visitor->out_time?->format('H:i'),
            'status'       => $visitor->isInside() ? 'inside' : 'exited',
        ]);
    }
}
