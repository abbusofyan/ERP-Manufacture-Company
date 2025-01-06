<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\ServiceAppointment;
use App\Models\ServiceMaintain;
use App\Models\ServiceOrder;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ServiceAppointmentController extends Controller
{
    public function select2Query(Request $request)
    {
        return ServiceAppointment::when($request->has('search'), function ($query) use ($request) {
            $query->where('cmp_number', 'LIKE', "%{$request->search}%");
        })
            ->limit(10)->get();
    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Retrieve search keyword from request
        $search = $request->search;

        // Determine order and sort direction
        $order = $request->order;
        $by = $request->by;

        // Show only 1 status
        $status = $request->status ?? 'default';

        // Determine pagination limit
        $paginate = $request->paginate ?? 10;

        // Filters to pass back to the view
        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;
        $filters['status'] = $status;

        // Define the order case for ascending and descending order
        $statusOrder = "CASE
        WHEN status = " . ServiceAppointment::STATUS_ACTIVE . " AND date_of_appointment >= CURDATE() THEN 1
        WHEN status = " . ServiceAppointment::STATUS_CONVERTED . " THEN 2
        WHEN status = " . ServiceAppointment::STATUS_ACTIVE . " AND date_of_appointment < CURDATE() THEN 3
        WHEN status = " . ServiceAppointment::STATUS_VOID . " THEN 4
        WHEN status = " . ServiceAppointment::STATUS_DRAFT . " THEN 5
        ELSE 6 END";

        // Query to retrieve appointments with related customer and vehicle
        $appointments = ServiceAppointment::withTrashed()
            ->with(['customer', 'vehicle'])
            ->when($search, function ($query) use ($search) {
                $query->where('cmp_number', 'LIKE', "%{$search}%");
            })
            ->when($status !== 'default', function ($query) use ($status) {
                switch ($status) {
                    case 'upcoming':
                        $query->where('status', ServiceAppointment::STATUS_ACTIVE)
                            ->where('date_of_appointment', '>=', now());
                        break;
                    case 'converted':
                        $query->where('status', ServiceAppointment::STATUS_CONVERTED);
                        break;
                    case 'expired':
                        $query->where('status', ServiceAppointment::STATUS_ACTIVE)
                            ->where('date_of_appointment', '<', now());
                        break;
                    case 'void':
                        $query->where('status', ServiceAppointment::STATUS_VOID);
                        break;
                    default:
                        break;
                }
            })
            ->orderByRaw($statusOrder)
            ->orderBy('id', 'desc')
            ->paginate($paginate);

        // Append query string parameters for pagination
        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
            'status' => $status,
        ];
        $appointments->appends($queryString);

        // Return view with appointments and filters
        return Inertia::render('ServiceAppointment/Index', [
            'appointments' => $appointments,
            'filters' => $filters,
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param ServiceAppointment $service_appointment
     * @return Response
     */
    public function show(ServiceAppointment $service_appointment): Response
    {
        return Inertia::render('ServiceAppointment/Detail', [
            'appointment' => $service_appointment->load(['customer', 'vehicle.customer']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        $c = Country::all()->toArray();
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code');
        return Inertia::render('ServiceAppointment/Form', [
            'csrf' => csrf_token(),
            'phoneCodes' => $phone_codes,
            'types' => ServiceAppointment::TYPES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        // Validation rules with custom messages
        $messages = [
            'date_of_appointment.required' => 'The Date of Appointment field is required.',
            'date_of_appointment.date' => 'The Date of Appointment is not a valid date.',
            'service_order_type.required' => 'The service type field is required.',
            'customer_id.required' => 'The Customer field is required.',
            'customer_id.exists' => 'The selected Customer does not exist.',
            'vehicle_id.required' => 'The Vehicle field is required.',
            'vehicle_id.exists' => 'The selected Vehicle does not exist.',
            'pic_email.email' => 'The Email must be a valid email address.',
            'status.required' => 'The Status field is required.',
            'status.boolean' => 'The Status field must be true or false.',
        ];

        $request->validate([
            'date_of_appointment' => 'required|date',
            'service_order_type' => 'required|string',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'pic_first_name' => 'nullable|string',
            'pic_last_name' => 'nullable|string',
            'pic_title' => 'nullable|string',
            'pic_country_code' => 'nullable|exists:countries,id',
            'pic_phone_number' => 'nullable|string',
            'pic_email' => 'nullable|email',
            'maintain_services' => 'nullable|array',
            'maintain_services.*.date' => 'nullable|date',
            'remark' => 'nullable|string',
            'status' => 'required|integer',
            'warranty_sale_order_eco_id' => 'nullable|exists:sales_orders_eco,id',
            'warranty_contract_id' => 'nullable|exists:service_contracts,id',
        ], $messages);

        $data = $request->all();
        $data['date_of_appointment'] = Carbon::parse($data['date_of_appointment']);

        // Additional custom validation
        $validator = Validator::make($data, []);
        $validator->after(function ($validator) use ($data) {
            if (isset($data['maintain_services']) && is_array($data['maintain_services']) && count($data['maintain_services']) > 0) {
                $dates = array_map(function ($service) {
                    return $service['is_active'] ? $service['date'] : null;
                }, $data['maintain_services']);
                $dates = array_filter($dates);
                $sortedDates = $dates;
                sort($sortedDates);

                if ($dates !== $sortedDates) {
                    $validator->errors()->add('maintain_services', 'The dates of maintain services must be in increasing order.');
                }
            }
        });

        // If validation fails, return with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create the service appointment
        $serviceAppointment = ServiceAppointment::create($data);

        // Update Code
        $serviceAppointment->update([
            'cmp_number' =>
                ($serviceAppointment->status == ServiceAppointment::STATUS_ACTIVE ? ServiceAppointment::ACTIVE_PREFIX : ServiceAppointment::DRAFT_PREFIX)
                . str_pad($serviceAppointment->id, 4, '0', STR_PAD_LEFT)
        ]);

        // Create maintain services
        if (isset($data['maintain_services'])) {
            foreach ($data['maintain_services'] as $service) {
                $serviceAppointment->maintains()->create([
                    'vehicle_id' => $serviceAppointment->vehicle_id,
                    'date' => Carbon::parse($service['date']),
                    'is_active' => $service['is_active'],
                    'is_draft' => $serviceAppointment->status == ServiceAppointment::STATUS_DRAFT,
                    'status' => ServiceMaintain::STATUS_PENDING,
                ]);
            }
        }

        // Redirect with a success message
        return redirect('/service-appointments')->with('created', 'Service appointment created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ServiceAppointment $service_appointment
     * @return Response
     */
    public function edit(ServiceAppointment $service_appointment): Response
    {
        $c = Country::all()->toArray();
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code');

        $customers = self::toSelect2Data(Customer::whereIn('id', [$service_appointment->customer_id])->get()->toArray(), 'id', 'code', 'name', ' | ');
        $vehicles = self::toSelect2Data(Vehicle::whereIn('id', [$service_appointment->vehicle_id])->get()->toArray(), 'id', 'license_plate');

        $vehicle = Vehicle::find($service_appointment->vehicle_id)
            ->load(['serviceContracts', 'salesOrders']);
        // Retrieve the nearest service contract
        $nearest_service_contract = $vehicle->nearestServiceContract();

        return Inertia::render('ServiceAppointment/Form', [
            'csrf' => csrf_token(),
            'appointment' => $service_appointment->load(['maintains']),
            'customer' => Customer::find($service_appointment->customer_id),
            'customers' => $customers,
            'vehicle' => Vehicle::find($service_appointment->vehicle_id)->load(['serviceContracts','salesOrders']),
            'vehicles' => $nearest_service_contract,
            'phoneCodes' => $phone_codes,
            'types' => ServiceAppointment::TYPES,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ServiceAppointment $serviceAppointment
     * @return RedirectResponse
     */
    public function update(Request $request, ServiceAppointment $serviceAppointment)
    {
        // Validation rules with custom messages
        $messages = [
            'date_of_appointment.required' => 'The Date of Appointment field is required.',
            'date_of_appointment.date' => 'The Date of Appointment is not a valid date.',
            'service_order_type.required' => 'The service type field is required.',
            'customer_id.required' => 'The Customer field is required.',
            'customer_id.exists' => 'The selected Customer does not exist.',
            'vehicle_id.required' => 'The Vehicle field is required.',
            'vehicle_id.exists' => 'The selected Vehicle does not exist.',
            'pic_email.email' => 'The Email must be a valid email address.',
            'status.required' => 'The Status field is required.',
            'status.boolean' => 'The Status field must be true or false.',
        ];

        $request->validate([
            'date_of_appointment' => 'required|date',
            'service_order_type' => 'required|string',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'pic_first_name' => 'nullable|string',
            'pic_last_name' => 'nullable|string',
            'pic_title' => 'nullable|string',
            'pic_country_code' => 'nullable|exists:countries,id',
            'pic_phone_number' => 'nullable|string',
            'pic_email' => 'nullable|email',
            'maintain_services' => 'nullable|array',
            'maintain_services.*.date' => 'nullable|date',
            'remark' => 'nullable|string',
            'status' => 'required|integer',
            'warranty_sale_order_eco_id' => 'nullable|exists:sales_orders_eco,id',
            'warranty_contract_id' => 'nullable|exists:service_contracts,id',
        ], $messages);

        $data = $request->all();
        $data['date_of_appointment'] = Carbon::parse($data['date_of_appointment']);

        // Additional custom validation
        $validator = Validator::make($data, []);
        $validator->after(function ($validator) use ($data) {
            if (isset($data['maintain_services']) && is_array($data['maintain_services']) && count($data['maintain_services']) > 0) {
                $dates = array_map(function ($service) {
                    return $service['is_active'] ? $service['date'] : null;
                }, $data['maintain_services']);
                $dates = array_filter($dates);
                $sortedDates = $dates;
                sort($sortedDates);

                if ($dates !== $sortedDates) {
                    $validator->errors()->add('maintain_services', 'The dates of maintain services must be in increasing order.');
                }
            }
        });

        // If validation fails, return with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update the service appointment
        $serviceAppointment->update($data);

        // Update maintain services
        if (isset($data['maintain_services'])) {
            foreach ($data['maintain_services'] as $service) {
                $serviceMaintain = ServiceMaintain::find($service['id']);
                if ($serviceMaintain) {
                    $serviceMaintain->update([
                        'vehicle_id' => $serviceAppointment->vehicle_id,
                        'date' => Carbon::parse($service['date']),
                        'is_active' => $service['is_active'],
                        'is_draft' => $serviceAppointment->status == ServiceAppointment::STATUS_DRAFT,
                    ]);
                } else {
                    $serviceAppointment->maintains()->create([
                        'vehicle_id' => $serviceAppointment->vehicle_id,
                        'date' => Carbon::parse($service['date']),
                        'is_active' => $service['is_active'],
                        'is_draft' => $serviceAppointment->status == ServiceAppointment::STATUS_DRAFT,
                        'status' => ServiceMaintain::STATUS_PENDING,
                    ]);
                }
            }
        }

        // Redirect with a success message
        return redirect('/service-appointments')->with('updated', 'Service appointment updated successfully.');
    }

    /**
     * Update the specified resource from storage.
     *
     * @param ServiceAppointment $service_appointment
     * @return RedirectResponse
     */
    public function convertDraft(ServiceAppointment $service_appointment): RedirectResponse
    {
        $service_appointment->update(['status' => 2]);

        //Update Code
        $service_appointment->update([
            'cmp_number' =>
                ($service_appointment->status == ServiceAppointment::STATUS_ACTIVE ? ServiceAppointment::ACTIVE_PREFIX : ServiceAppointment::DRAFT_PREFIX)
                . str_pad($service_appointment->id, 4, '0', STR_PAD_LEFT)
        ]);

        // Redirect back with success message
        return redirect('/service-appointments')->with('updated', 'Service appointment updated successfully.');
    }

    /**
     * Update the specified resource from storage.
     *
     * @param ServiceAppointment $service_appointment
     * @return Application|RedirectResponse|Redirector
     */
    public function convertServiceOrder(ServiceAppointment $service_appointment)
    {
        // Map data from ServiceAppointment to ServiceOrder
        $serviceOrder = new ServiceOrder();
        $serviceOrder->stage = 1;
        $serviceOrder->converted_service_appointment_id = $service_appointment->id;
        $serviceOrder->customer_feedback = $service_appointment->customer_feedback;
        $serviceOrder->service_order_type = $service_appointment->service_order_type;
        $serviceOrder->customer_id = $service_appointment->customer_id;
        $serviceOrder->vehicle_id = $service_appointment->vehicle_id;
        $serviceOrder->pic_first_name = $service_appointment->pic_first_name;
        $serviceOrder->pic_last_name = $service_appointment->pic_last_name;
        $serviceOrder->pic_title = $service_appointment->pic_title;
        $serviceOrder->pic_country_code = $service_appointment->pic_country_code;
        $serviceOrder->pic_phone_number = $service_appointment->pic_phone_number;
        $serviceOrder->pic_email = $service_appointment->pic_email;
        $serviceOrder->in_warranty = false;
        $serviceOrder->come_back_job = true;
        $serviceOrder->remark = $service_appointment->remark;
        $serviceOrder->status = $service_appointment->status;
        $serviceOrder->warranty_sale_order_eco_id = $service_appointment->warranty_sale_order_eco_id;
        $serviceOrder->warranty_contract_id = $service_appointment->warranty_contract_id;
        $serviceOrder->in_cmp = $service_appointment->service_order_type == 'CMP';
        // Save the ServiceOrder
        $serviceOrder->save();
        // Update code
        $serviceOrder->update([
            'code' =>
                ($serviceOrder->status == ServiceOrder::STATUS_DRAFT ? ServiceOrder::DRAFT_PREFIX : ServiceOrder::ACTIVE_PREFIX)
                . str_pad($serviceOrder->id, 4, '0', STR_PAD_LEFT)
        ]);

        if($service_appointment->customer_feedback){
           $serviceOrder->reports()->create([
              'name' => 'Customer Feedback',
              'remarks' => $service_appointment->customer_feedback,
           ]);
        }

        //Update status
        $service_appointment->update(['status' => ServiceAppointment::STATUS_CONVERTED]);



        // Redirect back with success message
        return redirect('/service-orders')->with('updated', 'Service order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceAppointment $service_appointment
     * @return RedirectResponse
     */
    public function destroy(ServiceAppointment $service_appointment): RedirectResponse
    {
        // Delete the appointment record
        $service_appointment->update(['status' => ServiceAppointment::STATUS_VOID]);

        // Redirect back with success message
        return redirect('/service-appointments')->with('deleted', 'Service appointment successfully cancelled');
    }
}
