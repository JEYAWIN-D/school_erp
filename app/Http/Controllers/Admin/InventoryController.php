<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\Vendor;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GrnRecord;
use App\Models\GrnItem;
use App\Models\StockIssuance;
use App\Models\StockIssuanceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    // ── Item Master ──────────────────────────────────────────

    public function index(Request $request)
    {
        $categories = InventoryCategory::active()->orderBy('name')->get();
        $items = InventoryItem::with('category')
            ->when(!$request->show_inactive, fn($q) => $q->where('is_active', true))
            ->when($request->category_id, fn($q, $v) => $q->where('category_id', $v))
            ->when($request->search, fn($q, $v) => $q->where(fn($q2) =>
                $q2->where('name', 'like', "%$v%")->orWhere('item_code', 'like', "%$v%")))
            ->when($request->low_stock, fn($q) => $q->where('reorder_level', '>', 0)->whereColumn('current_stock', '<=', 'reorder_level'))
            ->orderBy('name')
            ->paginate(30)->withQueryString();

        $lowStockCount    = InventoryItem::where('is_active', true)->where('reorder_level', '>', 0)->whereColumn('current_stock', '<=', 'reorder_level')->count();
        $totalItems       = InventoryItem::where('is_active', true)->count();
        $totalCategories  = InventoryCategory::active()->count();
        $totalStockValue  = InventoryItem::where('is_active', true)->sum(DB::raw('current_stock * unit_price'));

        $pendingRequisitions = 0;
        try { $pendingRequisitions = PurchaseRequisition::where('status', 'pending')->count(); } catch (\Exception $e) {}

        $pendingPOs = 0;
        try { $pendingPOs = PurchaseOrder::whereIn('status', ['sent', 'partial'])->count(); } catch (\Exception $e) {}

        $stats = compact('totalItems', 'totalCategories', 'lowStockCount', 'totalStockValue', 'pendingRequisitions', 'pendingPOs');

        return view('inventory.index', compact('items', 'categories', 'lowStockCount', 'stats'));
    }

    public function createItem()
    {
        $categories = InventoryCategory::active()->orderBy('name')->get();
        return view('inventory.item-form', compact('categories'));
    }

    public function storeItem(Request $request)
    {
        $data = $request->validate([
            'category_id'   => 'required|exists:inventory_categories,id',
            'name'          => 'required|string|max:255',
            'item_code'     => 'required|string|max:50|unique:inventory_items,item_code',
            'unit'          => 'required|string|max:30',
            'unit_price'    => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'location'      => 'nullable|string|max:100',
            'description'   => 'nullable|string',
        ]);

        InventoryItem::create($data);
        return redirect()->route('inventory.index')->with('success', 'Item added successfully.');
    }

    public function editItem(int $id)
    {
        $item       = InventoryItem::findOrFail($id);
        $categories = InventoryCategory::active()->orderBy('name')->get();
        return view('inventory.item-form', compact('item', 'categories'));
    }

    public function updateItem(Request $request, int $id)
    {
        $item = InventoryItem::findOrFail($id);
        $data = $request->validate([
            'category_id'   => 'required|exists:inventory_categories,id',
            'name'          => 'required|string|max:255',
            'item_code'     => "required|string|max:50|unique:inventory_items,item_code,$id",
            'unit'          => 'required|string|max:30',
            'unit_price'    => 'required|numeric|min:0',
            'reorder_level' => 'required|integer|min:0',
            'location'      => 'nullable|string|max:100',
            'description'   => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $item->update($data);
        return redirect()->route('inventory.index')->with('success', 'Item updated.');
    }

    // ── Categories ───────────────────────────────────────────

    public function categories()
    {
        $categories = InventoryCategory::withCount('items')->orderBy('name')->get();
        return view('inventory.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'nullable|string|max:20|unique:inventory_categories,code',
            'description' => 'nullable|string',
        ]);
        InventoryCategory::create($data);
        return back()->with('success', 'Category created.');
    }

    public function updateCategory(Request $request, int $id)
    {
        $cat  = InventoryCategory::findOrFail($id);
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => "nullable|string|max:20|unique:inventory_categories,code,$id",
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);
        $cat->update($data);
        return back()->with('success', 'Category updated.');
    }

    // ── Vendors ──────────────────────────────────────────────

    public function vendors(Request $request)
    {
        $vendors = Vendor::when($request->search, fn($q, $v) =>
                $q->where('name', 'like', "%$v%")->orWhere('phone', 'like', "%$v%"))
            ->orderBy('name')->paginate(20)->withQueryString();
        return view('inventory.vendors', compact('vendors'));
    }

    public function storeVendor(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:150',
            'code'           => 'nullable|string|max:20|unique:vendors,code',
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string',
            'gstin'          => 'nullable|string|max:15',
            'pan'            => 'nullable|string|max:10',
        ]);
        Vendor::create($data);
        return back()->with('success', 'Vendor added.');
    }

    public function updateVendor(Request $request, int $id)
    {
        $vendor = Vendor::findOrFail($id);
        $data   = $request->validate([
            'name'           => 'required|string|max:150',
            'code'           => "nullable|string|max:20|unique:vendors,code,$id",
            'contact_person' => 'nullable|string|max:100',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:100',
            'address'        => 'nullable|string',
            'gstin'          => 'nullable|string|max:15',
            'pan'            => 'nullable|string|max:10',
            'is_active'      => 'boolean',
        ]);
        $vendor->update($data);
        return back()->with('success', 'Vendor updated.');
    }

    // ── Purchase Requisitions ────────────────────────────────

    public function requisitions(Request $request)
    {
        $requisitions = PurchaseRequisition::with(['requestedBy', 'items.inventoryItem'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->latest()->paginate(20)->withQueryString();
        return view('inventory.requisitions', compact('requisitions'));
    }

    public function printRequisition(int $id)
    {
        $pr = PurchaseRequisition::with(['requestedBy', 'items.inventoryItem'])->findOrFail($id);
        return view('inventory.requisition-print', compact('pr'));
    }

    public function createRequisition()
    {
        $items = InventoryItem::active()->orderBy('name')->get();
        $prNumber = 'PR-' . now()->format('Ymd') . '-' . str_pad(
            PurchaseRequisition::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        return view('inventory.requisition-form', compact('items', 'prNumber'));
    }

    public function storeRequisition(Request $request)
    {
        $data = $request->validate([
            'pr_number'   => 'required|unique:purchase_requisitions,pr_number',
            'required_by' => 'required|date',
            'purpose'     => 'nullable|string',
            'items'       => 'required|array|min:1',
            'items.*.item_id'        => 'required|exists:inventory_items,id',
            'items.*.quantity'       => 'required|integer|min:1',
            'items.*.estimated_price'=> 'nullable|numeric|min:0',
            'items.*.remark'         => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $pr = PurchaseRequisition::create([
                'pr_number'    => $data['pr_number'],
                'required_by'  => $data['required_by'],
                'purpose'      => $data['purpose'] ?? null,
                'status'       => 'pending',
                'requested_by' => Auth::id(),
            ]);
            foreach ($data['items'] as $item) {
                PurchaseRequisitionItem::create(array_merge($item, ['requisition_id' => $pr->id]));
            }
        });

        return redirect()->route('inventory.requisitions')->with('success', 'Purchase requisition submitted.');
    }

    public function approveRequisition(Request $request, int $id)
    {
        $pr = PurchaseRequisition::findOrFail($id);
        $pr->update([
            'status'      => $request->action === 'approve' ? 'approved' : 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'remarks'     => $request->remarks,
        ]);
        return back()->with('success', 'Requisition ' . $request->action . 'd.');
    }

    // ── Purchase Orders ──────────────────────────────────────

    public function purchaseOrders(Request $request)
    {
        $orders = PurchaseOrder::with(['vendor', 'items.inventoryItem'])
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->latest('order_date')->paginate(20)->withQueryString();
        return view('inventory.purchase-orders', compact('orders'));
    }

    public function printPurchaseOrder(int $id)
    {
        $po = PurchaseOrder::with(['vendor', 'items.inventoryItem'])->findOrFail($id);
        return view('inventory.po-print', compact('po'));
    }

    public function createPurchaseOrder()
    {
        $vendors      = Vendor::active()->orderBy('name')->get();
        $items        = InventoryItem::active()->orderBy('name')->get();
        $requisitions = PurchaseRequisition::where('status', 'approved')->orderBy('pr_number')->get();
        $poNumber     = 'PO-' . now()->format('Ymd') . '-' . str_pad(
            PurchaseOrder::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        return view('inventory.po-form', compact('vendors', 'items', 'requisitions', 'poNumber'));
    }

    public function storePurchaseOrder(Request $request)
    {
        $data = $request->validate([
            'po_number'          => 'required|unique:purchase_orders,po_number',
            'vendor_id'          => 'required|exists:vendors,id',
            'requisition_id'     => 'nullable|exists:purchase_requisitions,id',
            'order_date'         => 'required|date',
            'expected_delivery'  => 'nullable|date|after_or_equal:order_date',
            'terms'              => 'nullable|string',
            'items'              => 'required|array|min:1',
            'items.*.item_id'    => 'required|exists:inventory_items,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($data, $request) {
            $total = collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']);
            $po = PurchaseOrder::create([
                'po_number'         => $data['po_number'],
                'vendor_id'         => $data['vendor_id'],
                'requisition_id'    => $data['requisition_id'] ?? null,
                'order_date'        => $data['order_date'],
                'expected_delivery' => $data['expected_delivery'] ?? null,
                'total_amount'      => $total,
                'status'            => 'draft',
                'terms'             => $data['terms'] ?? null,
                'created_by'        => Auth::id(),
            ]);
            foreach ($data['items'] as $item) {
                PurchaseOrderItem::create(array_merge($item, ['po_id' => $po->id, 'received_qty' => 0]));
            }
            if ($data['requisition_id'] ?? null) {
                PurchaseRequisition::find($data['requisition_id'])->update(['status' => 'ordered']);
            }
        });

        return redirect()->route('inventory.purchase-orders')->with('success', 'Purchase order created.');
    }

    public function markPoSent(int $id)
    {
        $po = PurchaseOrder::findOrFail($id);
        if (!in_array($po->status, ['draft', 'pending', 'approved'])) {
            return back()->with('error', 'Only draft/pending/approved POs can be marked as sent.');
        }
        $po->update(['status' => 'sent']);
        return back()->with('success', 'Purchase order #' . $po->po_number . ' marked as sent.');
    }

    // ── GRN ─────────────────────────────────────────────────

    public function grn(Request $request)
    {
        $records = GrnRecord::with(['purchaseOrder.vendor', 'items.inventoryItem'])
            ->latest('received_date')->paginate(20)->withQueryString();
        return view('inventory.grn', compact('records'));
    }

    public function createGrn(Request $request)
    {
        $orders   = PurchaseOrder::with('vendor', 'items.item')
            ->whereIn('status', ['sent', 'partial'])->orderBy('po_number')->get();
        $poId     = $request->po_id;
        $po       = $poId ? PurchaseOrder::with('items.item')->find($poId) : null;
        $grnNumber = 'GRN-' . now()->format('Ymd') . '-' . str_pad(
            GrnRecord::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        return view('inventory.grn-form', compact('orders', 'po', 'grnNumber'));
    }

    public function storeGrn(Request $request)
    {
        $data = $request->validate([
            'grn_number'     => 'required|unique:grn_records,grn_number',
            'po_id'          => 'required|exists:purchase_orders,id',
            'received_date'  => 'required|date',
            'invoice_number' => 'nullable|string|max:50',
            'invoice_date'   => 'nullable|date',
            'invoice_amount' => 'nullable|numeric',
            'remarks'        => 'nullable|string',
            'items'          => 'required|array|min:1',
            'items.*.item_id'           => 'required|exists:inventory_items,id',
            'items.*.po_item_id'        => 'nullable|exists:purchase_order_items,id',
            'items.*.received_qty'      => 'required|integer|min:0',
            'items.*.accepted_qty'      => 'required|integer|min:0',
            'items.*.rejected_qty'      => 'required|integer|min:0',
            'items.*.rejection_reason'  => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $grn = GrnRecord::create([
                'grn_number'     => $data['grn_number'],
                'po_id'          => $data['po_id'],
                'received_date'  => $data['received_date'],
                'invoice_number' => $data['invoice_number'] ?? null,
                'invoice_date'   => $data['invoice_date'] ?? null,
                'invoice_amount' => $data['invoice_amount'] ?? null,
                'remarks'        => $data['remarks'] ?? null,
                'received_by'    => Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                GrnItem::create(array_merge($item, ['grn_id' => $grn->id]));
                // Update stock
                InventoryItem::where('id', $item['item_id'])
                    ->increment('current_stock', $item['accepted_qty']);
                // Update PO item received qty
                if (!empty($item['po_item_id'])) {
                    PurchaseOrderItem::where('id', $item['po_item_id'])
                        ->increment('received_qty', $item['received_qty']);
                }
            }

            // Update PO status
            $po = PurchaseOrder::with('items')->find($data['po_id']);
            $allReceived = $po->items->every(fn($i) => $i->received_qty >= $i->quantity);
            $po->update(['status' => $allReceived ? 'received' : 'partial']);
        });

        return redirect()->route('inventory.grn')->with('success', 'GRN recorded. Stock updated.');
    }

    // ── Stock Issuance ───────────────────────────────────────

    public function issuances(Request $request)
    {
        $issuances = StockIssuance::with(['issuedBy', 'items.item'])
            ->latest('issue_date')->paginate(20)->withQueryString();
        return view('inventory.issuances', compact('issuances'));
    }

    public function createIssuance()
    {
        $items = InventoryItem::active()->where('current_stock', '>', 0)->orderBy('name')->get();
        $issueNumber = 'ISS-' . now()->format('Ymd') . '-' . str_pad(
            StockIssuance::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        return view('inventory.issuance-form', compact('items', 'issueNumber'));
    }

    public function storeIssuance(Request $request)
    {
        $data = $request->validate([
            'issue_number' => 'required|unique:stock_issuances,issue_number',
            'issue_date'   => 'required|date',
            'issued_to'    => 'required|string|max:150',
            'purpose'      => 'nullable|string',
            'items'        => 'required|array|min:1',
            'items.*.item_id'  => 'required|exists:inventory_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.remark'   => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $issuance = StockIssuance::create([
                    'issue_number' => $data['issue_number'],
                    'issue_date'   => $data['issue_date'],
                    'issued_to'    => $data['issued_to'],
                    'issued_by'    => Auth::id(),
                    'purpose'      => $data['purpose'] ?? null,
                ]);

                foreach ($data['items'] as $item) {
                    $inv = InventoryItem::findOrFail($item['item_id']);
                    if ($inv->current_stock < $item['quantity']) {
                        throw new \Exception("Insufficient stock for: {$inv->name}. Available: {$inv->current_stock}");
                    }
                    StockIssuanceItem::create(array_merge($item, ['issuance_id' => $issuance->id]));
                    $inv->decrement('current_stock', $item['quantity']);
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('inventory.issuances')->with('success', 'Stock issued successfully.');
    }

    // ── Stock Report ─────────────────────────────────────────

    public function stockReport(Request $request)
    {
        $categories = InventoryCategory::active()->orderBy('name')->get();
        $items = InventoryItem::with('category')
            ->when($request->category_id, fn($q, $v) => $q->where('category_id', $v))
            ->when($request->low_stock, fn($q) => $q->whereColumn('current_stock', '<=', 'reorder_level'))
            ->orderBy('name')->get();

        $totalValue = $items->sum(fn($i) => $i->current_stock * $i->unit_price);

        return view('inventory.stock-report', compact('items', 'categories', 'totalValue'));
    }
}
