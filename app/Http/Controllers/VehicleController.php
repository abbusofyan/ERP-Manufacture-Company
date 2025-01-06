<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Location;
use App\Models\Quotation;
use App\Models\Store;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class VehicleController extends Controller
{
    public function __construct()
    {
//        $this->authorizeResource(Vehicle::class, 'vehicle');
    }

    public function get(Request $request)
    {
        return Vehicle::where('license_plate', $request->license_plate)->first();
    }

    public function select2Query(Request $request)
    {
        return Vehicle::with('customer.defaultPoc', 'serviceContracts', 'salesOrders.refrigerationSale')
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('license_plate', 'LIKE', "%{$request->search}%")
                    ->orWhere('type', 'LIKE', "%{$request->search}%")
                    ->orWhere('contact_no', 'LIKE', "%{$request->search}%")
                    ->orWhere('chassis_no', 'LIKE', "%{$request->search}%")
                    ->orWhere('model', 'LIKE', "%{$request->search}%");
            })->limit(10)->get();
    }

    public function index(Request $request)
    {
        $search = $request->search;

        if ($request->order && $request->by) {
            $order = $request->order;
            $by = $request->by;
        } else {
            $order = 'id';
            $by = 'desc';
        }

        if ($request->paginate) {
            $paginate = $request->paginate;
        } else {
            $paginate = 10;
        }

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $vehicles = Vehicle::with('customer')
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('license_plate', 'LIKE', "%{$request->search}%")
                    ->orWhere('type', 'LIKE', "%{$request->search}%")
                    ->orWhere('contact_no', 'LIKE', "%{$request->search}%")
                    ->orWhere('chassis_no', 'LIKE', "%{$request->search}%")
                    ->orWhere('model', 'LIKE', "%{$request->search}%");
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $vehicles->appends($queryString);

        return Inertia::render('Vehicle/Index', [
            'vehicles' => $vehicles,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Vehicle/Form', [
            'csrf' => csrf_token(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'license_plate' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'end_user' => 'nullable|string|max:255',
            'vehicle_voltage' => 'required|string|max:255',
            'contact_no' => 'nullable|string|max:255',
            'chassis_no' => 'required|string|max:255',
            'chassis_delivery' => 'nullable|string|max:255',
            'vehicle_make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'refrigeration_serial_number' => 'nullable|string|max:255',
            'vehicle_class' => 'required|string|max:255',
            'type_of_plate' => 'required|string|max:255',
            'other_info' => 'nullable|string|max:255',
            'current_mileage' => 'nullable|integer',
            'mileage_date' => 'nullable|date',
            'warranty_mileage' => 'nullable|integer',
            'warranty_end_date' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['mileage_date'] = Carbon::parse($data['mileage_date']);
        $data['warranty_end_date'] = Carbon::parse($data['warranty_end_date']);

        Vehicle::create($data);

        return redirect('/vehicles')->with('created', 'Vehicle created successfully');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load('customer', 'serviceOrders.customer');
        return Inertia::render('Vehicle/Detail', [
            'vehicle' => $vehicle,
        ]);
    }

    public function edit(Vehicle $vehicle)
    {
        return Inertia::render('Vehicle/Form', [
            'vehicle' => $vehicle,
            'csrf' => csrf_token(),
            'customers' => self::toSelect2Data(Customer::where('id', $vehicle->customer_id)->get()->toArray(), 'id', 'name'),
        ]);
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'license_plate' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'end_user' => 'nullable|string|max:255',
            'current_mileage' => 'nullable|integer',
            'mileage_date' => 'nullable|date',
            'warranty_mileage' => 'nullable|integer',
            'warranty_end_date' => 'nullable|date',
        ]);

        $data = $request->all();
        $data['mileage_date'] = Carbon::parse($data['mileage_date']);
        $data['warranty_end_date'] = Carbon::parse($data['warranty_end_date']);

        $vehicle->update($data);

        return redirect('/vehicles')->with('updated', 'Vehicle updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Vehicle $vehicle
     * @return RedirectResponse
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return back()->with('deleted', 'Vehicle deleted successfully');
    }
}
