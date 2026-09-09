<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $currentYear = AcademicYear::current() ?? AcademicYear::first();
        
        $query = Expense::with(['creator', 'verifier', 'approver'])
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id));

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('expense_number', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%");
            });
        }

        $expenses = $query->orderByDesc('expense_date')->orderByDesc('id')->paginate(15)->withQueryString();

        // High level stats — single aggregated query
        $statsRow = DB::table('expenses')
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->selectRaw("
                COALESCE(SUM(CASE WHEN approval_status = 'approved' THEN amount ELSE 0 END), 0) as total_amount,
                COALESCE(SUM(CASE WHEN category = 'academic' AND approval_status = 'approved' THEN amount ELSE 0 END), 0) as academic_amount,
                COALESCE(SUM(CASE WHEN category = 'maintenance' AND approval_status = 'approved' THEN amount ELSE 0 END), 0) as maintenance_amount,
                COUNT(CASE WHEN approval_status IN ('pending', 'verified') THEN 1 END) as pending_count,
                COUNT(*) as total_count
            ")->first();

        $stats = [
            'total_amount'       => (float) ($statsRow->total_amount ?? 0),
            'academic_amount'    => (float) ($statsRow->academic_amount ?? 0),
            'maintenance_amount' => (float) ($statsRow->maintenance_amount ?? 0),
            'pending_count'      => (int) ($statsRow->pending_count ?? 0),
            'total_count'        => (int) ($statsRow->total_count ?? 0),
        ];

        return view('expenses.index', compact('expenses', 'stats', 'currentYear'));
    }

    public function academic(Request $request)
    {
        $currentYear = AcademicYear::current() ?? AcademicYear::first();
        $query = Expense::academic()->with(['creator', 'verifier', 'approver'])
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id));

        if ($request->filled('subcategory')) {
            $query->where('subcategory', $request->subcategory);
        }
        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('expense_number', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%");
            });
        }

        $expenses = $query->orderByDesc('expense_date')->paginate(15)->withQueryString();

        // Subcategory counts & sums
        $subcategories = [
            'books_learning_materials'  => 'Books & Learning Materials',
            'lab_consumables'           => 'Lab Chemicals & Consumables',
            'exam_stationary'           => 'Exam Papers & Stationary',
            'sports_equipment'          => 'Sports Equipment & PE',
            'student_events'            => 'Student Events & Competitions',
            'workshops_training'        => 'Faculty Workshops & Training',
            'software_licenses'         => 'Educational Software & IT',
            'other_academic'            => 'Other Academic Expenses',
        ];

        $base = Expense::academic()->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id));
        $approvedSum = (clone $base)->where('approval_status', 'approved')->sum('amount');
        $pendingSum = (clone $base)->whereIn('approval_status', ['pending', 'verified'])->sum('amount');

        return view('expenses.academic', compact('expenses', 'subcategories', 'approvedSum', 'pendingSum', 'currentYear'));
    }

    public function maintenance(Request $request)
    {
        $currentYear = AcademicYear::current() ?? AcademicYear::first();
        $query = Expense::maintenance()->with(['creator', 'verifier', 'approver'])
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id));

        if ($request->filled('subcategory')) {
            $query->where('subcategory', $request->subcategory);
        }
        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('expense_number', 'like', "%{$search}%")
                  ->orWhere('vendor_name', 'like', "%{$search}%");
            });
        }

        $expenses = $query->orderByDesc('expense_date')->paginate(15)->withQueryString();

        $subcategories = [
            'building_repairs_civil'     => 'Building & Civil Maintenance',
            'electrical_power'           => 'Electrical & Generator Fuel',
            'plumbing_water'             => 'Plumbing & RO Drinking Water',
            'bus_fleet_repairs'          => 'School Bus Fleet & Vehicle Maintenance',
            'campus_sanitation'          => 'Campus Housekeeping & Sanitation',
            'security_cctv'              => 'Campus Security & CCTV Systems',
            'furniture_fixtures'         => 'Desks, Benches & Furniture Repairs',
            'other_maintenance'          => 'General Facility Maintenance',
        ];

        $base = Expense::maintenance()->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id));
        $approvedSum = (clone $base)->where('approval_status', 'approved')->sum('amount');
        $pendingSum = (clone $base)->whereIn('approval_status', ['pending', 'verified'])->sum('amount');

        return view('expenses.maintenance', compact('expenses', 'subcategories', 'approvedSum', 'pendingSum', 'currentYear'));
    }

    public function create()
    {
        $academicYear = AcademicYear::current() ?? AcademicYear::first();
        return view('expenses.create', compact('academicYear'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category'          => 'required|in:academic,maintenance',
            'subcategory'       => 'required|string|max:100',
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            'amount'            => 'required|numeric|min:1',
            'expense_date'      => 'required|date',
            'payment_method'    => 'required|string|max:50',
            'vendor_name'       => 'nullable|string|max:255',
            'vendor_invoice_no' => 'nullable|string|max:100',
            'receipt'           => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        $prefix = $request->category === 'academic' ? 'EXP-ACAD-' : 'EXP-MAINT-';
        $expenseNo = $prefix . date('Ymd') . '-' . strtoupper(Str::random(4));

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('expenses/receipts', 'public');
        }

        $currentYear = AcademicYear::current() ?? AcademicYear::first();

        $expense = Expense::create([
            'expense_number'       => $expenseNo,
            'category'             => $request->category,
            'subcategory'          => $request->subcategory,
            'title'                => $request->title,
            'description'          => $request->description,
            'amount'               => $request->amount,
            'expense_date'         => $request->expense_date,
            'payment_method'       => $request->payment_method,
            'vendor_name'          => $request->vendor_name,
            'vendor_invoice_no'    => $request->vendor_invoice_no,
            'invoice_receipt_path' => $receiptPath,
            'academic_year_id'     => $currentYear?->id,
            'created_by'           => auth()->id(),
            'approval_status'      => 'pending',
        ]);

        $redirectRoute = $request->category === 'academic' ? 'expenses.academic' : 'expenses.maintenance';
        return redirect()->route($redirectRoute)->with('success', "Expense voucher {$expense->expense_number} recorded successfully and sent for approval.");
    }

    public function show(int $id)
    {
        $expense = Expense::with(['creator', 'verifier', 'approver', 'academicYear'])->findOrFail($id);
        return view('expenses.show', compact('expense'));
    }

    public function verify(int $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->update([
            'approval_status' => 'verified',
            'verified_by'     => auth()->id(),
            'verified_at'     => now(),
        ]);

        return back()->with('success', "Expense {$expense->expense_number} verified and forwarded to Final Approver.");
    }

    public function approve(int $id)
    {
        $expense = Expense::findOrFail($id);
        $expense->update([
            'approval_status' => 'approved',
            'approved_by'     => auth()->id(),
            'approved_at'     => now(),
        ]);

        return back()->with('success', "Expense {$expense->expense_number} officially approved.");
    }

    public function reject(Request $request, int $id)
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);
        $expense = Expense::findOrFail($id);
        $expense->update([
            'approval_status'  => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
        ]);

        return back()->with('warning', "Expense {$expense->expense_number} marked as rejected.");
    }
}
