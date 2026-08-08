<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\ArrayExport;
use App\Models\BusAttendance;
use App\Models\Classes;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\TransportAllotment;
use App\Models\TransportAttendant;
use App\Models\TransportRoute;
use App\Models\TransportStop;
use App\Models\Vehicle;
use App\Models\VehicleFuelLog;
use App\Models\VehicleMaintenance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransportController extends Controller
{
    public function index()
    {
        $stats = [
            'routes'   => TransportRoute::where('is_active', true)->count(),
            'vehicles' => Vehicle::where('is_active', true)->count(),
            'students' => TransportAllotment::where('is_active', true)->count(),
        ];

        // Drivers / attendants
        try {
            $stats['drivers'] = DB::table('transport_drivers')->count();
        } catch (\Exception $e) {
            $stats['drivers'] = TransportAttendant::count();
        }

        // Today's bus attendance
        try {
            $stats['bus_present'] = BusAttendance::whereDate('date', today())->where('status', 'present')->count();
        } catch (\Exception $e) {
            $stats['bus_present'] = 0;
        }

        // Maintenance alerts: vehicles with overdue or due-this-week maintenance
        $maintenanceAlerts = collect();
        try {
            $maintenanceAlerts = VehicleMaintenance::with('vehicle')
                ->where('next_service_date', '<=', today()->addDays(7))
                ->whereHas('vehicle', fn($q) => $q->where('is_active', true))
                ->orderBy('next_service_date')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {}

        // Document expiry alerts (insurance/fitness within 30 days)
        $docAlerts = collect();
        try {
            $docAlerts = Vehicle::where('is_active', true)
                ->where(fn($q) => $q
                    ->where('insurance_expiry', '<=', today()->addDays(30))
                    ->orWhere('fitness_expiry', '<=', today()->addDays(30))
                )
                ->select('id', 'vehicle_number', 'insurance_expiry', 'fitness_expiry')
                ->limit(5)
                ->get();
        } catch (\Exception $e) {}

        return view('transport.index', compact('stats', 'maintenanceAlerts', 'docAlerts'));
    }

    public function routes(Request $request)
    {
        $showInactive = $request->boolean('show_inactive');
        $routes = TransportRoute::withCount('allotments')
            ->with('vehicle')
            ->when($request->search, fn($q, $v) => $q->where('route_name', 'like', "%$v%"))
            ->when(!$showInactive, fn($q) => $q->where('is_active', true))
            ->paginate(20)->withQueryString();
        return view('transport.routes', compact('routes', 'showInactive'));
    }

    public function toggleRoute(int $id)
    {
        $route = TransportRoute::findOrFail($id);
        $route->update(['is_active' => !$route->is_active]);
        $label = $route->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Route \"{$route->route_name}\" {$label}.");
    }

    public function vehicles(Request $request)
    {
        $today    = now()->toDateString();
        $vehicles = Vehicle::with(['route', 'maintenanceLogs' => fn($q) => $q->latest('service_date')->limit(1)])
            ->when($request->search, fn($q, $v) => $q->where('vehicle_number', 'like', "%$v%"))
            ->paginate(20);

        // Flag vehicles with overdue next service
        foreach ($vehicles as $v) {
            $latest = $v->maintenanceLogs->first();
            $v->next_service_overdue = $latest && $latest->next_service_date && $latest->next_service_date < $today;
            $v->next_service_date    = $latest?->next_service_date;
        }

        return view('transport.vehicles', compact('vehicles'));
    }

    public function drivers()
    {
        $today    = now()->toDateString();
        $in30     = now()->addDays(30)->toDateString();

        $vehicles = Vehicle::whereNotNull('driver_name')->orderBy('driver_name')->get()
            ->each(function ($v) use ($today, $in30) {
                $v->license_expired   = $v->driver_license_expiry && $v->driver_license_expiry < $today;
                $v->license_due_soon  = $v->driver_license_expiry && !$v->license_expired && $v->driver_license_expiry <= $in30;
            });

        $expiredCount  = $vehicles->where('license_expired', true)->count();
        $dueSoonCount  = $vehicles->where('license_due_soon', true)->count();
        $unverified    = $vehicles->whereIn('police_verification_status', ['pending', 'expired'])->count();

        return view('transport.drivers', compact('vehicles', 'expiredCount', 'dueSoonCount', 'unverified'));
    }

    public function updatePoliceVerification(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:pending,verified,expired']);
        Vehicle::findOrFail($id)->update([
            'police_verification_status' => $request->status,
            'police_verification_date'   => $request->verification_date,
            'police_verification_notes'  => $request->notes,
        ]);
        return back()->with('success', 'Police verification status updated.');
    }

    public function incidents(Request $request)
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $routes   = TransportRoute::orderBy('route_name')->get();

        $incidents = DB::table('transport_incidents')
            ->leftJoin('vehicles', 'transport_incidents.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('transport_routes', 'transport_incidents.route_id', '=', 'transport_routes.id')
            ->select('transport_incidents.*', 'vehicles.vehicle_number', 'transport_routes.route_name')
            ->when($request->vehicle_id, fn($q, $v) => $q->where('transport_incidents.vehicle_id', $v))
            ->when($request->incident_type, fn($q, $v) => $q->where('transport_incidents.incident_type', $v))
            ->when($request->status, fn($q, $v) => $q->where('transport_incidents.status', $v))
            ->orderByDesc('transport_incidents.incident_date')
            ->paginate(20)->withQueryString();

        return view('transport.incidents', compact('incidents', 'vehicles', 'routes'));
    }

    public function storeIncident(Request $request)
    {
        $request->validate([
            'incident_date' => 'required|date',
            'incident_type' => 'required|in:accident,breakdown,theft,vandalism,other',
            'description'   => 'required|string|max:1000',
            'severity'      => 'required|in:minor,moderate,major',
        ]);
        DB::table('transport_incidents')->insert([
            'vehicle_id'    => $request->vehicle_id ?: null,
            'route_id'      => $request->route_id ?: null,
            'incident_date' => $request->incident_date,
            'incident_type' => $request->incident_type,
            'location'      => $request->location,
            'description'   => $request->description,
            'severity'      => $request->severity,
            'action_taken'  => $request->action_taken,
            'reported_by'   => $request->reported_by,
            'fir_number'    => $request->fir_number,
            'estimated_loss'=> $request->estimated_loss ?: null,
            'status'        => 'open',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        return back()->with('success', 'Incident logged.');
    }

    public function updateIncidentStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:open,under_review,resolved,closed']);
        DB::table('transport_incidents')->where('id', $id)->update([
            'status'           => $request->status,
            'resolution_notes' => $request->resolution_notes,
            'resolved_date'    => in_array($request->status, ['resolved', 'closed']) ? now()->toDateString() : null,
            'updated_at'       => now(),
        ]);
        return back()->with('success', 'Incident status updated.');
    }

    public function tracking()
    {
        $routes   = TransportRoute::where('is_active', true)->with('vehicle')->get();
        $vehicles = Vehicle::where('is_active', true)->get();
        return view('transport.tracking', compact('routes', 'vehicles'));
    }

    public function editVehicle(int $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return view('transport.vehicle-edit', compact('vehicle'));
    }

    public function updateVehicle(Request $request, int $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update($request->only(['make', 'model', 'fitness_expiry', 'insurance_expiry', 'permit_expiry', 'puc_expiry', 'tax_expiry', 'vehicle_type']));
        return back()->with('success', 'Vehicle updated.');
    }

    public function allotment(Request $request)
    {
        $routes   = TransportRoute::where('is_active', true)->with('vehicle')->get();
        $stops    = TransportStop::orderBy('name')->get();
        $classes  = Classes::active()->get();
        $sections = Section::all();
        $currentYear = \App\Models\AcademicYear::current();
        $enrollments = StudentEnrollment::with('student', 'class')->where('status', 'active')
            ->when($currentYear, fn($q) => $q->where('academic_year_id', $currentYear->id))
            ->get();
        $allotments = TransportAllotment::with(['enrollment.student', 'enrollment.class', 'route', 'stop'])
            ->where('is_active', true)
            ->when($request->route_id, fn($q, $v) => $q->where('route_id', $v))
            ->when($request->class_id, fn($q, $v) => $q->whereHas('enrollment', fn($q) => $q->where('class_id', $v)))
            ->paginate(25)->withQueryString();
        return view('transport.allotment', compact('routes', 'stops', 'classes', 'sections', 'enrollments', 'allotments'));
    }

    public function storeAllotment(Request $request)
    {
        $request->validate(['enrollment_id' => 'required|exists:student_enrollments,id', 'route_id' => 'required|exists:transport_routes,id']);

        // Seat availability check
        $route = TransportRoute::findOrFail($request->route_id);
        $vehicle = $route->vehicle;
        if ($vehicle) {
            $occupied = TransportAllotment::where('route_id', $route->id)->where('is_active', true)->count();
            $capacity = $vehicle->capacity ?: $vehicle->seating_capacity;
            if ($capacity > 0 && $occupied >= $capacity) {
                return back()->withErrors(['route_id' => "Route \"{$route->route_name}\" is full ({$capacity} seats occupied). Choose another route."])->withInput();
            }
        }
        $stopFare = null;
        if ($request->stop_id) {
            $stop     = \App\Models\TransportStop::find($request->stop_id);
            $stopFare = $stop?->fare;
        }
        $fee = $request->fee ?? $stopFare;

        TransportAllotment::updateOrCreate(
            ['enrollment_id' => $request->enrollment_id],
            array_merge($request->only(['route_id', 'stop_id', 'pickup_time', 'drop_time']), ['fee' => $fee, 'is_active' => true])
        );

        // Auto-link transport fee to student via StudentCustomFee
        if ($fee && $fee > 0) {
            $enrollment = \App\Models\StudentEnrollment::find($request->enrollment_id);
            if ($enrollment) {
                $feeHead     = \App\Models\FeeHead::firstOrCreate(
                    ['name' => 'Transport Fee'],
                    ['fee_type' => 'transport', 'is_active' => true]
                );
                $currentYear = \App\Models\AcademicYear::current();
                \App\Models\StudentCustomFee::updateOrCreate(
                    [
                        'student_id'      => $enrollment->student_id,
                        'fee_head_id'     => $feeHead->id,
                        'academic_year_id'=> $currentYear?->id,
                    ],
                    [
                        'custom_amount' => $fee,
                        'reason'        => 'Auto-linked from transport stop assignment',
                        'set_by'        => \Illuminate\Support\Facades\Auth::id(),
                    ]
                );
            }
        }

        return back()->with('success', 'Route allotted' . ($fee ? " (Transport fee ₹{$fee}/term auto-linked)." : '.'));
    }

    public function deleteAllotment(int $id)
    {
        TransportAllotment::findOrFail($id)->update(['is_active' => false]);
        return back()->with('success', 'Allotment removed.');
    }

    public function exportAllotment()
    {
        return redirect()->route('transport.allotment');
    }

    public function vehicleDocuments()
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->get()->each(function ($v) {
            $v->expiring_soon = collect(['fitness_expiry', 'insurance_expiry', 'permit_expiry', 'puc_expiry', 'tax_expiry'])
                ->contains(fn($f) => $v->$f && \Carbon\Carbon::parse($v->$f)->diffInDays(now()) < 30);
        });
        return view('transport.documents', compact('vehicles'));
    }

    public function maintenance(Request $request)
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $logs = VehicleMaintenance::with('vehicle')
            ->when($request->vehicle_id, fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($request->month, function ($q, $v) {
                [$yr, $mo] = explode('-', $v);
                return $q->whereYear('service_date', $yr)->whereMonth('service_date', $mo);
            })
            ->latest('service_date')->paginate(20)->withQueryString();

        return view('transport.maintenance', compact('vehicles', 'logs'));
    }

    public function storeMaintenance(Request $request)
    {
        $request->validate(['vehicle_id' => 'required|exists:vehicles,id', 'maintenance_type' => 'required|string', 'service_date' => 'required|date']);
        VehicleMaintenance::create($request->only(['vehicle_id', 'maintenance_type', 'service_date', 'odometer', 'cost', 'vendor', 'description', 'next_service_date']));
        return back()->with('success', 'Maintenance log added.');
    }

    public function fuel(Request $request)
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $summary  = null;
        if ($request->filled('month')) {
            [$yr, $mo] = explode('-', $request->month);
            $q = VehicleFuelLog::whereYear('log_date', $yr)->whereMonth('log_date', $mo)
                ->when($request->vehicle_id, fn($q, $v) => $q->where('vehicle_id', $v));
            $totalLitres = $q->sum('quantity_litres');
            $totalAmount = $q->sum('total_cost');
            $maxOdometer = $q->max('odometer_reading');
            $minOdometer = $q->min('odometer_reading');
            $kmDriven    = ($maxOdometer && $minOdometer && $maxOdometer > $minOdometer) ? ($maxOdometer - $minOdometer) : 0;
            $summary = [
                'litres'        => $totalLitres,
                'amount'        => $totalAmount,
                'km_driven'     => $kmDriven,
                'cost_per_km'   => ($kmDriven > 0 && $totalAmount > 0) ? round($totalAmount / $kmDriven, 2) : null,
                'mileage'       => ($totalLitres > 0 && $kmDriven > 0) ? round($kmDriven / $totalLitres, 2) : null,
            ];
        }
        $logs = VehicleFuelLog::with('vehicle')
            ->when($request->vehicle_id, fn($q, $v) => $q->where('vehicle_id', $v))
            ->when($request->month, function ($q, $v) {
                [$yr, $mo] = explode('-', $v);
                return $q->whereYear('log_date', $yr)->whereMonth('log_date', $mo);
            })
            ->latest('log_date')->paginate(20)->withQueryString();
        return view('transport.fuel', compact('vehicles', 'logs', 'summary'));
    }

    public function storeFuel(Request $request)
    {
        $request->validate(['vehicle_id' => 'required|exists:vehicles,id', 'fill_date' => 'required|date', 'litres' => 'required|numeric|min:0']);
        VehicleFuelLog::create([
            'vehicle_id'       => $request->vehicle_id,
            'log_date'         => $request->fill_date,
            'quantity_litres'  => $request->litres,
            'cost_per_litre'   => $request->rate_per_litre,
            'total_cost'       => $request->amount,
            'odometer_reading' => $request->odometer,
            'filled_by'        => $request->station ?? 'Driver',
        ]);
        return back()->with('success', 'Fuel log added.');
    }

    public function roster()
    {
        $routes   = TransportRoute::with(['vehicle', 'stops', 'allotments.enrollment.student'])->where('is_active', true)->get();
        return view('transport.roster', compact('routes'));
    }

    public function createVehicle()
    {
        $routes = TransportRoute::where('is_active', true)->orderBy('route_name')->get();
        return view('transport.vehicle-create', compact('routes'));
    }

    public function storeVehicle(Request $request)
    {
        $request->validate([
            'vehicle_number'  => 'required|string|max:20|unique:vehicles',
            'make'            => 'nullable|string|max:60',
            'model'           => 'nullable|string|max:60',
            'seating_capacity'=> 'required|integer|min:1',
            'vehicle_type'    => 'nullable|in:bus,van,minibus,auto',
            'driver_name'     => 'nullable|string|max:100',
            'driver_mobile'   => 'nullable|string|max:15',
        ]);
        Vehicle::create(array_merge($request->validated(), ['is_active' => true]));
        return redirect()->route('transport.vehicles')->with('success', 'Vehicle added.');
    }

    public function createRoute()
    {
        $vehicles = Vehicle::where('is_active', true)->orderBy('vehicle_number')->get();
        return view('transport.route-create', compact('vehicles'));
    }

    public function editRoute(int $id)
    {
        $route    = TransportRoute::findOrFail($id);
        $vehicles = Vehicle::where('is_active', true)->orderBy('vehicle_number')->get();
        return view('transport.route-edit', compact('route', 'vehicles'));
    }

    public function updateRoute(Request $request, int $id)
    {
        $route = TransportRoute::findOrFail($id);
        $request->validate(['route_name' => 'required|string|max:100', 'vehicle_id' => 'nullable|exists:vehicles,id']);
        $route->update([
            'route_name'     => $request->route_name,
            'route_code'     => $request->route_code,
            'vehicle_id'     => $request->vehicle_id,
            'from_location'  => $request->start_point,
            'to_location'    => $request->end_point,
            'departure_time' => $request->start_time,
            'arrival_time'   => $request->end_time,
            'distance_km'    => $request->distance_km,
            'fee'            => $request->fare,
        ]);
        return redirect()->route('transport.routes')->with('success', "Route \"{$route->route_name}\" updated.");
    }

    public function storeRoute(Request $request)
    {
        $request->validate([
            'route_name'  => 'required|string|max:100',
            'vehicle_id'  => 'nullable|exists:vehicles,id',
            'distance_km' => 'nullable|numeric|min:0',
            'fare'        => 'nullable|numeric|min:0',
        ]);
        TransportRoute::create([
            'route_name'       => $request->route_name,
            'vehicle_id'       => $request->vehicle_id,
            'from_location'    => $request->start_point,
            'to_location'      => $request->end_point,
            'departure_time'   => $request->start_time,
            'arrival_time'     => $request->end_time,
            'distance_km'      => $request->distance_km,
            'fee'              => $request->fare,
            'is_active'        => true,
        ]);
        return redirect()->route('transport.routes')->with('success', 'Route added.');
    }

    public function createDriver()
    {
        $vehicles = Vehicle::where('is_active', true)->orderBy('vehicle_number')->get();
        return view('transport.driver-create', compact('vehicles'));
    }

    public function storeDriver(Request $request)
    {
        $request->validate([
            'vehicle_id'      => 'required|exists:vehicles,id',
            'driver_name'     => 'required|string|max:100',
            'driver_mobile'   => 'required|string|max:15',
            'license_number'  => 'nullable|string|max:20',
            'license_expiry'  => 'nullable|date',
        ]);
        Vehicle::findOrFail($request->vehicle_id)->update($request->only(['driver_name', 'driver_mobile', 'license_number', 'license_expiry']));
        return redirect()->route('transport.drivers')->with('success', 'Driver assigned.');
    }

    public function stops(Request $request)
    {
        $routes = TransportRoute::where('is_active', true)->get();
        $stops  = TransportStop::with('route')
            ->when($request->route_id, fn($q, $v) => $q->where('route_id', $v))
            ->orderBy('stop_order')->paginate(25)->withQueryString();
        return view('transport.stops', compact('routes', 'stops'));
    }

    public function storeStop(Request $request)
    {
        $request->validate([
            'route_id'    => 'required|exists:transport_routes,id',
            'name'        => 'required|string|max:100',
            'stop_order'  => 'required|integer|min:1',
            'pickup_time' => 'nullable|date_format:H:i',
            'drop_time'   => 'nullable|date_format:H:i',
            'fare'        => 'nullable|numeric|min:0',
        ]);
        TransportStop::create($request->only(['route_id', 'name', 'stop_order', 'pickup_time', 'drop_time', 'fare', 'landmark', 'distance_km']));
        return back()->with('success', 'Stop added.');
    }

    public function editStop(int $id)
    {
        $stop   = TransportStop::findOrFail($id);
        $routes = TransportRoute::where('is_active', true)->get();
        return view('transport.stop-edit', compact('stop', 'routes'));
    }

    public function updateStop(Request $request, int $id)
    {
        TransportStop::findOrFail($id)->update($request->only(['route_id', 'name', 'stop_order', 'pickup_time', 'drop_time', 'fare', 'landmark']));
        return redirect()->route('transport.stops')->with('success', 'Stop updated.');
    }

    public function deleteStop(int $id)
    {
        TransportStop::findOrFail($id)->delete();
        return back()->with('success', 'Stop removed.');
    }

    public function attendants(Request $request)
    {
        $attendants = TransportAttendant::with(['vehicle', 'route'])
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')->paginate(20);
        $vehicles = Vehicle::where('is_active', true)->get();
        $routes   = TransportRoute::where('is_active', true)->get();
        return view('transport.attendants', compact('attendants', 'vehicles', 'routes'));
    }

    public function storeAttendant(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'mobile'     => 'nullable|string|max:15',
            'aadhaar'    => 'nullable|string|max:12',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'route_id'   => 'nullable|exists:transport_routes,id',
        ]);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('attendants', 'public');
        }
        TransportAttendant::create($data);
        return back()->with('success', 'Attendant added successfully.');
    }

    public function updateAttendant(Request $request, int $id)
    {
        $attendant = TransportAttendant::findOrFail($id);
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'mobile'     => 'nullable|string|max:15',
            'aadhaar'    => 'nullable|string|max:12',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'route_id'   => 'nullable|exists:transport_routes,id',
            'is_active'  => 'boolean',
        ]);
        $attendant->update($data);
        return back()->with('success', 'Attendant updated.');
    }

    public function deleteAttendant(int $id)
    {
        TransportAttendant::findOrFail($id)->delete();
        return back()->with('success', 'Attendant removed.');
    }

    public function vehicleUtilisation(Request $request)
    {
        $vehicles = Vehicle::where('is_active', true)->get();
        $routesByVehicle = TransportRoute::where('is_active', true)
            ->withCount(['allotments as assigned' => fn($q) => $q->where('is_active', true)])
            ->get()->keyBy('vehicle_id');
        $data = $vehicles->map(function ($vehicle) use ($routesByVehicle) {
            $route    = $routesByVehicle->get($vehicle->id);
            $assigned = $route?->assigned ?? 0;
            $capacity = $vehicle->capacity ?? 0;
            return [
                'vehicle'     => $vehicle,
                'route'       => $route,
                'capacity'    => $capacity,
                'assigned'    => $assigned,
                'available'   => max(0, $capacity - $assigned),
                'utilisation' => $capacity > 0 ? round(($assigned / $capacity) * 100, 1) : 0,
            ];
        });
        return view('transport.vehicle-utilisation', compact('data'));
    }

    /* ------------------------------------------------------------------ */
    /*  Fuel Expense Monthly Summary                                        */
    public function emergencyContacts(Request $request)
    {
        $routes   = TransportRoute::where('is_active', true)->orderBy('route_name')->get();
        $routeId  = $request->route_id;
        $contacts = [];

        if ($routeId) {
            $contacts = DB::table('transport_emergency_contacts')
                ->where('route_id', $routeId)
                ->orderByDesc('is_primary')
                ->orderBy('name')
                ->get();
        }

        return view('transport.emergency-contacts', compact('routes', 'contacts', 'routeId'));
    }

    public function storeEmergencyContact(Request $request)
    {
        $request->validate([
            'route_id'     => 'required|exists:transport_routes,id',
            'name'         => 'required|string|max:100',
            'phone'        => 'required|string|max:20',
            'relationship' => 'nullable|string|max:50',
        ]);
        DB::table('transport_emergency_contacts')->insert([
            'route_id'     => $request->route_id,
            'name'         => $request->name,
            'phone'        => $request->phone,
            'relationship' => $request->relationship,
            'designation'  => $request->designation,
            'is_primary'   => $request->boolean('is_primary'),
            'notes'        => $request->notes,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
        return back()->with('success', 'Emergency contact added.');
    }

    public function deleteEmergencyContact(int $id)
    {
        DB::table('transport_emergency_contacts')->where('id', $id)->delete();
        return back()->with('success', 'Contact removed.');
    }

    /* ------------------------------------------------------------------ */

    public function fuelSummary(Request $request)
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $month    = $request->month ?? now()->format('Y-m');
        [$yr, $mo] = explode('-', $month);

        $fuelByVehicle = VehicleFuelLog::select(
                'vehicle_id',
                DB::raw('SUM(quantity_litres) as total_litres'),
                DB::raw('SUM(total_cost) as total_amount'),
                DB::raw('COUNT(*) as fill_count'),
                DB::raw('MAX(odometer_reading) - MIN(odometer_reading) as km_covered')
            )
            ->whereYear('log_date', $yr)->whereMonth('log_date', $mo)
            ->groupBy('vehicle_id')
            ->with('vehicle')
            ->get();

        $grandTotals = [
            'litres' => $fuelByVehicle->sum('total_litres'),
            'amount' => $fuelByVehicle->sum('total_amount'),
        ];

        return view('transport.fuel-summary', compact('fuelByVehicle', 'grandTotals', 'month', 'vehicles'));
    }

    public function fuelSummaryPdf(Request $request)
    {
        $month  = $request->month ?? now()->format('Y-m');
        [$yr, $mo] = explode('-', $month);
        $fuelByVehicle = VehicleFuelLog::select(
                'vehicle_id',
                DB::raw('SUM(quantity_litres) as total_litres'),
                DB::raw('SUM(total_cost) as total_amount'),
                DB::raw('COUNT(*) as fill_count'),
                DB::raw('MAX(odometer_reading) - MIN(odometer_reading) as km_covered')
            )
            ->whereYear('log_date', $yr)->whereMonth('log_date', $mo)
            ->groupBy('vehicle_id')
            ->with('vehicle')
            ->get();
        $grandTotals = ['litres' => $fuelByVehicle->sum('total_litres'), 'amount' => $fuelByVehicle->sum('total_amount')];
        $school = SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.transport-fuel-summary', compact('fuelByVehicle', 'grandTotals', 'month', 'school'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('fuel-summary-' . $month . '.pdf');
    }

    public function fuelSummaryExcel(Request $request)
    {
        $month  = $request->month ?? now()->format('Y-m');
        [$yr, $mo] = explode('-', $month);
        $fuelByVehicle = VehicleFuelLog::select(
                'vehicle_id',
                DB::raw('SUM(quantity_litres) as total_litres'),
                DB::raw('SUM(total_cost) as total_amount'),
                DB::raw('COUNT(*) as fill_count'),
                DB::raw('MAX(odometer_reading) - MIN(odometer_reading) as km_covered')
            )
            ->whereYear('log_date', $yr)->whereMonth('log_date', $mo)
            ->groupBy('vehicle_id')
            ->with('vehicle')
            ->get();

        $rows = $fuelByVehicle->map(fn($row) => [
            'Vehicle'      => $row->vehicle?->vehicle_number ?? ('Vehicle #' . $row->vehicle_id),
            'Fill Count'   => $row->fill_count,
            'Total Litres' => number_format($row->total_litres, 2),
            'Total Amount' => '₹' . number_format($row->total_amount, 2),
            'KM Covered'   => $row->km_covered ? number_format($row->km_covered, 0) : '—',
            'Km/Litre'     => ($row->total_litres > 0 && $row->km_covered)
                ? round($row->km_covered / $row->total_litres, 2) : '—',
        ])->toArray();

        return Excel::download(new ArrayExport($rows), 'fuel-summary-' . $month . '.xlsx');
    }

    /* ------------------------------------------------------------------ */
    /*  Roster PDF Export                                                   */
    /* ------------------------------------------------------------------ */

    public function rosterPdf(Request $request)
    {
        $routes = TransportRoute::with(['vehicle', 'stops', 'allotments.enrollment.student'])->where('is_active', true)->get();
        $school = SchoolSetting::first();
        $pdf = Pdf::loadView('pdf.transport-roster', compact('routes', 'school'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->stream('transport-roster-' . now()->format('Y-m-d') . '.pdf');
    }

    public function rosterExcel(Request $request)
    {
        $routes = TransportRoute::with(['vehicle', 'stops', 'allotments.enrollment.student'])->where('is_active', true)->get();
        $rows = [];
        foreach ($routes as $route) {
            $rows[] = ['=== ' . strtoupper($route->route_name) . ' ===' , '', '', ''];
            $rows[] = ['Stop', 'Pickup Time', 'Student', 'Class'];
            foreach ($route->stops->sortBy('sequence') as $stop) {
                $studentsAtStop = $route->allotments->filter(fn($a) => $a->stop_id == $stop->id);
                if ($studentsAtStop->isEmpty()) {
                    $rows[] = [$stop->stop_name, $stop->pickup_time ?? '—', '(No students)', ''];
                } else {
                    foreach ($studentsAtStop as $allotment) {
                        $rows[] = [
                            $stop->stop_name,
                            $stop->pickup_time ?? '—',
                            $allotment->enrollment?->student?->full_name ?? '—',
                            $allotment->enrollment?->class?->name ?? '—',
                        ];
                    }
                }
            }
            $rows[] = ['', '', '', ''];
        }
        return Excel::download(new ArrayExport($rows, ['Stop / Info', 'Time', 'Student', 'Class']), 'transport-roster-' . now()->format('Y-m-d') . '.xlsx');
    }

    /* ------------------------------------------------------------------ */
    /*  Maintenance Cost per Vehicle Report                                 */
    /* ------------------------------------------------------------------ */

    public function maintenanceCostReport(Request $request)
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $year     = $request->year ?? now()->year;

        $perVehicle = $vehicles->map(function ($v) use ($year) {
            $logs = VehicleMaintenance::where('vehicle_id', $v->id)
                ->whereYear('service_date', $year)
                ->get();
            $byMonth = $logs->groupBy(fn($l) => \Carbon\Carbon::parse($l->service_date)->format('m'))
                ->map(fn($g) => $g->sum('cost'));
            return [
                'vehicle'       => $v,
                'logs'          => $logs,
                'total'         => $logs->sum('cost'),
                'breakdown_count' => $logs->where('maintenance_type','breakdown')->count(),
                'scheduled_count' => $logs->where('maintenance_type','scheduled')->count(),
                'by_month'      => $byMonth,
                'last_service'  => $logs->sortByDesc('service_date')->first()?->service_date,
                'next_service'  => $logs->sortByDesc('service_date')->first()?->next_service_date,
            ];
        });

        $grandTotal  = $perVehicle->sum('total');
        $months      = collect(range(1, 12))->mapWithKeys(fn($m) => [$m => \Carbon\Carbon::create(null, $m)->format('M')]);

        return view('transport.maintenance-cost', compact('perVehicle', 'grandTotal', 'vehicles', 'year', 'months'));
    }

    public function maintenanceCostPdf(Request $request)
    {
        $year     = $request->year ?? now()->year;
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $school   = SchoolSetting::first();

        $perVehicle = $vehicles->map(function ($v) use ($year) {
            $logs = VehicleMaintenance::where('vehicle_id', $v->id)->whereYear('service_date', $year)->get();
            return ['vehicle' => $v, 'logs' => $logs, 'total' => $logs->sum('cost')];
        });
        $grandTotal = $perVehicle->sum('total');

        $pdf = Pdf::loadView('pdf.transport-maintenance-cost', compact('perVehicle', 'grandTotal', 'school', 'year'));
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('maintenance-cost-' . $year . '.pdf');
    }

    public function maintenanceCostExcel(Request $request)
    {
        $year     = $request->year ?? now()->year;
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $rows     = [];
        $rows[]   = ['Vehicle', 'Reg Number', 'Date', 'Type', 'Work Done', 'Vendor', 'Cost (₹)', 'Next Service'];
        foreach ($vehicles as $v) {
            $logs = VehicleMaintenance::where('vehicle_id', $v->id)->whereYear('service_date', $year)->orderBy('service_date')->get();
            if ($logs->isEmpty()) continue;
            foreach ($logs as $l) {
                $rows[] = [
                    $v->vehicle_number,
                    $v->vehicle_number,
                    \Carbon\Carbon::parse($l->service_date)->format('d M Y'),
                    ucfirst($l->maintenance_type),
                    $l->work_done,
                    $l->vendor ?? '—',
                    $l->cost,
                    $l->next_service_date ? \Carbon\Carbon::parse($l->next_service_date)->format('d M Y') : '—',
                ];
            }
            $rows[] = ['', '', '', '', 'Vehicle Total:', '', $logs->sum('cost'), ''];
            $rows[] = ['', '', '', '', '', '', '', ''];
        }
        return Excel::download(new ArrayExport($rows), 'maintenance-cost-' . $year . '.xlsx');
    }

    // ── Bus Attendance Register ───────────────────────────

    public function busAttendance(Request $request)
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->get();
        $routes   = TransportRoute::where('is_active', true)->orderBy('route_name')->get();

        $date      = $request->date ?? today()->toDateString();
        $vehicleId = $request->vehicle_id;
        $tripType  = $request->trip_type ?? 'morning';

        $students = collect();
        $existing = collect();

        if ($vehicleId) {
            // Students allotted to this vehicle
            $students = TransportAllotment::with(['student', 'stop'])
                ->where('vehicle_id', $vehicleId)
                ->where('is_active', true)
                ->get()
                ->map(fn($a) => [
                    'student'      => $a->student,
                    'boarding_stop'=> $a->stop?->name ?? $a->boarding_stop ?? '',
                ])
                ->filter(fn($a) => $a['student'] !== null);

            $existing = BusAttendance::where('vehicle_id', $vehicleId)
                ->where('date', $date)
                ->where('trip_type', $tripType)
                ->pluck('status', 'student_id');
        }

        return view('transport.bus-attendance', compact(
            'vehicles', 'routes', 'date', 'vehicleId', 'tripType', 'students', 'existing'
        ));
    }

    public function saveBusAttendance(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date'       => 'required|date',
            'trip_type'  => 'required|in:morning,afternoon,both',
            'attendance'  => 'nullable|array',
        ]);

        $attendance = $request->input('attendance', []);

        foreach ($attendance as $studentId => $status) {
            BusAttendance::updateOrCreate(
                [
                    'vehicle_id' => $request->vehicle_id,
                    'date'       => $request->date,
                    'trip_type'  => $request->trip_type,
                    'student_id' => $studentId,
                ],
                [
                    'route_id'  => $request->route_id,
                    'status'    => $status,
                    'marked_by' => Auth::id(),
                ]
            );
        }

        return back()->with('success', count($attendance) . ' attendance records saved.');
    }

    public function busAttendanceReport(Request $request)
    {
        $vehicles = Vehicle::orderBy('vehicle_number')->get();

        $vehicleId = $request->vehicle_id;
        $from      = $request->from ?? today()->startOfMonth()->toDateString();
        $to        = $request->to   ?? today()->toDateString();

        $records = collect();
        if ($vehicleId) {
            $records = BusAttendance::with(['student', 'vehicle'])
                ->where('vehicle_id', $vehicleId)
                ->whereBetween('date', [$from, $to])
                ->orderBy('date')->orderBy('student_id')
                ->get()
                ->groupBy('student_id')
                ->map(fn($group) => [
                    'student' => $group->first()->student,
                    'total'   => $group->count(),
                    'present' => $group->where('status', 'present')->count(),
                    'absent'  => $group->where('status', 'absent')->count(),
                ]);
        }

        return view('transport.bus-attendance-report', compact('vehicles', 'vehicleId', 'from', 'to', 'records'));
    }

    public function busAttendanceReportExcel(Request $request)
    {
        $vehicleId = $request->vehicle_id;
        $from      = $request->from ?? today()->startOfMonth()->toDateString();
        $to        = $request->to   ?? today()->toDateString();
        $vehicle   = Vehicle::find($vehicleId);

        $records = BusAttendance::with('student')
            ->when($vehicleId, fn($q) => $q->where('vehicle_id', $vehicleId))
            ->whereBetween('date', [$from, $to])
            ->orderBy('date')->orderBy('student_id')
            ->get()
            ->groupBy('student_id')
            ->map(fn($group) => [
                'Student'       => $group->first()->student?->full_name ?? '—',
                'Admission No'  => $group->first()->student?->admission_number ?? '—',
                'Total Days'    => $group->count(),
                'Present'       => $group->where('status', 'present')->count(),
                'Absent'        => $group->where('status', 'absent')->count(),
                'Attendance %'  => $group->count() > 0 ? round($group->where('status','present')->count() / $group->count() * 100) . '%' : '—',
            ])->values()->toArray();

        return Excel::download(
            new ArrayExport($records),
            'bus-attendance-' . ($vehicle?->vehicle_number ?? 'all') . '-' . $from . '-to-' . $to . '.xlsx'
        );
    }
}
