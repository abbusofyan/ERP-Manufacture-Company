<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\ContractorItem;
use App\Models\Customer;
use App\Models\Milestone;
use App\Models\MilestoneItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductionWorkOrder;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\QuotationHeaderType;
use App\Models\QuotationHeaderTypeItem;
use App\Models\QuotationRemark;
use App\Models\RefrigerationSale;
use App\Models\SalesOrderECO;
use App\Models\Vehicle;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderMaterial;
use App\Models\ProductionOrderProcess;
use App\Models\Assembly;
use App\Models\EngineeringOrder;
use App\Models\EngineeringOrderProcess;
use App\Models\ProductionOrderProcessDetail;
use App\Models\RefrigerationSaleSpecificationItem;
use App\Models\Requisition;
use App\Services\RequisitionService;
use App\Services\ProductionOrderService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SalesOrderECOController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param $shipment
     * @param Request $request
     * @return Response
     */
    public function index($shipment, Request $request): Response
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

        $orders = SalesOrderECO::with('refrigerationSale.customer.countryPhoneCode')
            ->when($search, function ($query) use ($search) {
                $query->where('schedule_comments', 'LIKE', "%{$search}%");
            })->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->whereHas('refrigerationSale', function ($query) use ($shipment) {
                $query->where('shipment', RefrigerationSale::SHIPMENT_PARAM[$shipment]);
            })
            ->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $orders->appends($queryString);

        return Inertia::render('SalesOrderECO/Index', [
            'shipment' => $shipment,
            'orders' => $orders,
            'filters' => $filters,
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param $shipment
     * @param SalesOrderECO $sales_order_eco
     * @return Response
     */
    public function show($shipment, SalesOrderECO $sales_order_eco): Response
    {
        return Inertia::render('SalesOrderECO/Detail', [
            'shipment' => $shipment,
            'order' => $sales_order_eco->load('quotation.customer', 'vehicle', 'mineStones.items', 'productionWorkOrder'),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param string $shipment
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function create($shipment, Request $request): Application|Redirector|RedirectResponse
    {
        // Validate the request
        $request->validate([
            'rs_id' => 'required|exists:refrigeration_sales,id',
        ]);

        // Find the RefrigerationSale instance and load related headers
        $rs = RefrigerationSale::find($request->rs_id)?->load('specs.items');
        if (!$rs) {
            return redirect()->back()->withErrors(['error' => 'Refrigeration Sale not found.']);
        }

        // Find or create a new SalesOrderECO
        $sales_order_eco = SalesOrderECO::firstOrNew(['rs_id' => $rs->id]);

        if (!$sales_order_eco->exists) {
            $sales_order_eco->save();

            // Prepare to create or retrieve vehicles
            $vehicle_ids = [];

            if ($rs->headers) {
                foreach ($rs->headers as $header) {
                    // Calculate warranty end date based on running hours
                    $currentDate = Carbon::now();
                    $warrantyEndDate = $currentDate->addHours($header->vehicle_running_hours);

                    // Check if the vehicle already exists by license plate
                    if (!empty($header->vehicle_license_plate)) {
                        $vehicle = Vehicle::firstOrCreate(
                            ['license_plate' => $header->vehicle_license_plate],
                            [
                                'customer_id' => $rs->customer_id,
                                'type' => $header->vehicle_class,
                                'end_user' => $header->vehicle_make,
                                'vehicle_voltage' => $header->vehicle_voltage,
                                'contact_no' => null,
                                'chassis_no' => $header->vehicle_chassis_no,
                                'chassis_delivery' => null,
                                'vehicle_make' => $header->vehicle_make,
                                'model' => $header->vehicle_model,
                                'vehicle_class' => $header->vehicle_class,
                                'type_of_plate' => $header->vehicle_type_of_plate,
                                'refrigeration_serial_number' => null,
                                'other_info' => null,
                                'current_mileage' => 0,
                                'mileage_date' => $currentDate->toDateString(),
                                'warranty_mileage' => $header->vehicle_mileage,
                                'warranty_end_date' => $warrantyEndDate->toDateString(),
                            ]);


                        // Add the vehicle ID to the list
                        $vehicle_ids[] = $vehicle->id;
                    }
                }

                // Associate the vehicles with the SalesOrderECO
                $sales_order_eco->vehicles()->sync($vehicle_ids);
            }

            // Update the status of RefrigerationSale
            $rs->update(['status' => RefrigerationSale::STATUS_CONFIRMED]);
        }


        // Redirect to the edit page of the newly created SalesOrderECO
        return redirect("/$shipment/sales-order-eco/{$sales_order_eco->id}/edit")
            ->with('created', 'Sale order created successfully.');
    }

    /**
     * Update the form for creating a new resource.
     *
     * @param $shipment
     * @param SalesOrderECO $order
     * @param Request $request
     * @return Response
     */
    public function editMilestone($shipment, SalesOrderECO $order, Request $request): Response
    {
        $mileStone = Milestone::firstOrNew(['sales_order_id' => $order->id]);

        if (!$mileStone->exists) {
            $mileStone->sales_order_id = $order->id;
            $mileStone->save();
        }

        return Inertia::render('SalesOrderECO/MileStone', [
            'shipment' => $shipment,
            'order' => $order->load('refrigerationSale'),
            'mileStone' => $mileStone->load('items'),
            'status' => MilestoneItem::STAGE_STATUS_TEXT,
        ]);
    }

    /**
     * Update the form for creating a new resource.
     *
     * @param $shipment
     * @param SalesOrderECO $order
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function updateMilestone($shipment, SalesOrderECO $order, Request $request): Redirector|RedirectResponse|Application
    {
        $data = $request->all();
        $milestone = Milestone::updateOrCreate(['id' => $data['id']], [
            'sales_order_id' => $order->id,
            'delivery_date' => $request->has('delivery_date') ? Carbon::parse($data['delivery_date']) : null,
        ]);

        foreach ($data['items'] as $item) {
            MilestoneItem::updateOrCreate(
                ['id' => $item['id']],
                [
                    'milestone_id' => $milestone->id,
                    'stage' => $item['stage'],
                    'calculated_due_date' => $item['calculated_due_date'] ? Carbon::parse($item['calculated_due_date']) : null,
                    'active' => $item['active'],
                    'status' => $item['status'],
                ]
            );
        }

        return redirect("/$shipment/sales-order-eco/create?rs_id=$order->rs_id")->with('updated', 'Milestone update successfully');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param $shipment
     * @param SalesOrderECO $sales_order_eco
     * @return Response
     */
    public function edit($shipment, SalesOrderECO $sales_order_eco): Response
    {
        return Inertia::render('SalesOrderECO/Form', [
            'shipment' => $shipment,
            'csrf' => csrf_token(),
            'order' => $sales_order_eco->load('refrigerationSale.customer', 'mineStones.items', 'vehicles', 'productionWorkOrder', 'contractor.items'),
            'customers' => $sales_order_eco->vehicle ? self::toSelect2Data(Customer::where('id', $sales_order_eco->vehicle->customer_id)->get()->toArray(), 'id', 'name') : [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $shipment
     * @param SalesOrderECO $sales_order_eco
     * @param Request $request
     * @return RedirectResponse
     */
    public function update($shipment, SalesOrderECO $sales_order_eco, Request $request): RedirectResponse
    {
        if ($request->type == 2) {
            self::updateVehicleInformation($shipment, $sales_order_eco, $request);
        }

        if ($request->type == 3) {
            self::storeVehicle($shipment, $sales_order_eco, $request);
        }

        if ($request->type == 4) {
            self::updateRefrigerationInformation($shipment, $sales_order_eco, $request);
        }

        if ($request->type == 5) {
            self::updateRemark($shipment, $sales_order_eco, $request);
        }

        if ($request->type == 6) {
            self::updateProductWorkOrder($shipment, $sales_order_eco, $request);
        }

        if ($request->type == 7) {
            self::updateContrator($shipment, $sales_order_eco, $request);
        }

        return redirect("/$shipment/sales-order-eco")->with('updated', 'Sales Order updated successfully');
    }

    private function updateVehicleInformation($shipment, SalesOrderECO $sales_order_eco, Request $request): void
    {
        $request->validate([
            'schedule_comments' => 'nullable|string|max:1000',
            'vehicle_receive_date' => 'nullable|string|max:255',
            'actual_completion_date' => 'nullable|string|max:255',
        ]);

        $sales_order_eco->update([
            'schedule_comments' => $request->schedule_comments,
            'vehicle_receive_date' => $request->vehicle_receive_date ? Carbon::parse($request->vehicle_receive_date) : null,
            'actual_completion_date' => $request->actual_completion_date ? Carbon::parse($request->actual_completion_date) : null,
        ]);
    }

    private function storeVehicle($shipment, SalesOrderECO $sales_order_eco, Request $request)
    {
        // Validate the request data
        $request->validate([
            'license_plate' => 'required|max:255',
            'vehicle_type' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
            'end_user' => 'nullable|string|max:255',
            'vehicle_voltage' => 'required|string|max:255',
            'contact_no' => 'nullable|string|max:255',
            'chassis_no' => 'required|string|max:255',
            'chassis_delivery' => 'nullable|string|max:255',
            'vehicle_make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'vehicle_class' => 'required|string|max:255',
            'type_of_plate' => 'required|string|max:255',
            'warranty_mileage' => 'nullable|integer',
            'warranty_end_date' => 'nullable|date',
        ]);

        // Prepare data for the vehicle
        $data = $request->all();
        $data['type'] = $request->vehicle_type;
        $data['current_mileage'] = 0;
        $data['mileage_date'] = Carbon::now();
        $data['warranty_end_date'] = Carbon::parse($data['warranty_end_date']);

        // Find or create the vehicle
        $vehicle = Vehicle::firstOrCreate(
            ['license_plate' => $request->license_plate],
            $data
        );

        // Associate the vehicle with the sales order
        $sales_order_eco->vehicles()->syncWithoutDetaching([$vehicle->id]);
    }

    private function updateRefrigerationInformation($shipment, SalesOrderECO $sales_order_eco, Request $request)
    {
        $request->validate([
            'engine_serial' => 'required|array|min:1',
            'engine_serial.*' => 'required|string|max:255',
            'evaporator_serial' => 'nullable|array|min:1',
            'evaporator_serial.*' => 'required|string|max:255',
            'refrigeration_unit_serial' => 'required|string|max:255',
            'nea_serial' => 'nullable|string|max:255',
            'condenser_serial' => 'required|array|min:1',
            'condenser_serial.*' => 'required|string|max:255',
            'standby_unit' => 'required|string|max:255',
        ]);

        $sales_order_eco->update([
            'engine_serial' => $request->engine_serial,
            'evaporator_serial' => $request->evaporator_serial,
            'refrigeration_unit_serial' => $request->refrigeration_unit_serial,
            'nea_serial' => $request->nea_serial,
            'condenser_serial' => $request->condenser_serial,
            'standby_unit' => $request->standby_unit,
        ]);
    }

    private function updateRemark($shipment, SalesOrderECO $sales_order_eco, Request $request)
    {
        ProductionWorkOrder::updateOrCreate(
            [
                'sales_order_id' => $sales_order_eco->id,
            ],
            [
                'remark' => $request->remark,
            ]
        );
    }

    private function updateProductWorkOrder($shipment, SalesOrderECO $sales_order_eco, Request $request)
    {
        $request->validate([
            'vehicle_class_wo' => 'required|string',
            'vehicle_voltage_wo' => 'required|string',
            'unit_model' => 'nullable|string',
            'box_dimension_mm_l' => 'nullable|numeric',
            'box_dimension_mm_w' => 'nullable|numeric',
            'box_dimension_mm_h' => 'nullable|numeric',
            'pe_archie' => 'nullable|string',
            'pe_prema' => 'nullable|string',
            'addition_accessories' => 'nullable|string',
            'receipt_date' => 'nullable|date',
            'received_date' => 'nullable|date',
            'bracket' => ['nullable', Rule::in(['Bracket', 'Ready'])],
            'production_schedule' => 'nullable|date',
            'ibox' => ['nullable', Rule::in(['BOX', 'System', 'Flooring'])],
            'final_assy' => 'nullable|boolean',
            'testing' => 'nullable|boolean',
            'vehicle_no' => 'nullable|string',
            'log_card' => 'nullable|boolean',
            'dummy_box' => 'nullable|string',
            'final_delivery' => ['nullable', Rule::in(['Cust', 'Goldbel (SYN/WF)', 'Triangle / Borneo Motor/ST/Hoe Heng'])],
        ]);

        $data = $request->except(['remark']);
        $data['receipt_date'] = Carbon::parse($data['receipt_date']);
        $data['received_date'] = Carbon::parse($data['received_date']);
        $data['production_schedule'] = Carbon::parse($data['production_schedule']);

        ProductionWorkOrder::updateOrCreate(
            [
                'sales_order_id' => $sales_order_eco->id,
            ],
            $data
        );
    }

    private function updateContrator($shipment, SalesOrderECO $sales_order_eco, Request $request)
    {
        $request->validate([
            'tailgate_supplier' => 'required|string',
            'tailgate_completion' => 'required|string',
            'spray_painting_supplier' => 'required|string',
            'spray_painting_completion' => 'required|string',
            'confirmation' => 'required|array',
            'items' => 'required|array',
            'items.*.id' => 'nullable|integer',
            'items.*.remarks' => 'nullable|string',
            'items.*.wip_location' => 'nullable|string',
            'items.*.start_date' => 'nullable|date',
            'items.*.edd' => 'nullable|date',
            'items.*.parking_location' => 'nullable|string',
            'items.*.done_date' => 'nullable|date',
        ]);

        $data = $request->all();

        $contractor = Contractor::updateOrCreate(['sales_order_id' => $sales_order_eco->id], $data);

        ContractorItem::where('contractor_id', $contractor->id)->delete();
        foreach ($data['items'] as $itemData) {
            $item = [
                'contractor_id' => $contractor->id,
                'remarks' => $itemData['remarks'],
                'wip_location' => $itemData['wip_location'],
                'start_date' => $itemData['start_date'] ? Carbon::parse($itemData['start_date']) : null,
                'edd' => $itemData['edd'] ? Carbon::parse($itemData['edd']) : null,
                'parking_location' => $itemData['parking_location'],
                'done_date' => $itemData['done_date'] ? Carbon::parse($itemData['done_date']) : null,
            ];

            ContractorItem::create($item);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param $shipment
     * @param Request $request
     * @param SalesOrderECO $order
     */
    public function updateStatus($shipment, Request $request, SalesOrderECO $order)
    {
        DB::transaction(function () use ($request, $order) {
            $previousStatus = $order->status;

            $order->update(['status' => $request->status]);

            $customer = $order->refrigerationSale->customer;
            $revenueChange = 0;

            /** Update Revenue **/
            if ($previousStatus == SalesOrderECO::STATUS_PENDING &&
                ($order->status == SalesOrderECO::STATUS_CONFIRMED || $order->status == SalesOrderECO::STATUS_COMPLETED)) {
                // From pending to confirmed or completed: Add revenue
                $revenueChange = $order->refrigerationSale->total;
            } else if (($previousStatus == SalesOrderECO::STATUS_CONFIRMED || $previousStatus == SalesOrderECO::STATUS_COMPLETED) &&
                $order->status == SalesOrderECO::STATUS_CANCELED) {
                // From confirmed or completed to canceled: Subtract revenue
                $revenueChange = -$order->refrigerationSale->total;
            }

            if ($revenueChange != 0) {
                $customer->updateRevenue($revenueChange);
            }

            if ($order->status == SalesOrderECO::STATUS_CONFIRMED) {
                $productionOrderService = new ProductionOrderService;
                $productionOrderService->createProductionOrder($order);

                $productionOrders = ProductionOrder::with('quotation.specs.items')->where('quotation_id', $order->rs_id)->get();
                foreach ($productionOrders as $productionOrder) {
                    $data = [
                        'status' => Requisition::PENDING_STATUS,
                        'type' => Requisition::PRODUCTION_ORDER_TYPE,
                        'urgent_orders' => 0,
                        'approved_by' => auth()->user()->id,
                        'created_by' => auth()->user()->id,
                        'model_id' => $productionOrder->id,
                        'type' => Requisition::PRODUCTION_ORDER_TYPE,
                        'inventory_items' => []
                    ];
                    foreach ($productionOrder->quotation->specs as $spec) {
                        foreach ($spec->items as $item) {
                            array_push($data['inventory_items'], [
                                'product_id' => $item['product_id'],
                                'requested_qty' => $item['planned_qty']
                            ]);
                        }
                    }
                    $requisitionService = new RequisitionService;
                    $requisitionService->createRequisition($data);
                }
            }
        });

        return back()->with('updated', 'Order updated successfully');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param $shipment
     * @param Request $request
     * @param SalesOrderECO $order
     * @return Application|Redirector|RedirectResponse
     */
    public function updateDetail($shipment, Request $request, SalesOrderECO $order): Redirector|RedirectResponse|Application
    {
        $pr = ProductionWorkOrder::updateOrCreate(
            [
                'sales_order_id' => $order->id,
            ],
            [
                'sales_order_id' => $order->id,
                'unit_model' => $request->unit_model,
                'box_dimension_mm' => $request->box_dimension_mm,
                'pe_drawing' => $request->pe_drawing,
                'chassis_extension_shortening' => $request->chassis_extension_shortening,
                'remark' => $request->remark,
            ]
        );

        /** Update pe_drawing_file **/
        if ($request->hasFile('pe_drawing_file')) {
            $path = $request->file('pe_drawing_file')->store("sales_order_files/$order->id", 'public');
            $pr->update(['pe_drawing_file' => $path]);
        }

        return redirect("/$shipment/sales-order-eco")->with('updated', 'Order updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param $shipment
     * @param SalesOrderECO $sales_order_eco
     * @return RedirectResponse
     */
    public function destroy($shipment, SalesOrderECO $sales_order_eco): RedirectResponse
    {
        $sales_order_eco->update(['status' => SalesOrderECO::STATUS_CANCELED]);

        /** Update Revenue **/
        if ($sales_order_eco->status == SalesOrderECO::STATUS_CONFIRMED || $sales_order_eco->status == SalesOrderECO::STATUS_COMPLETED) {
            $customer = $sales_order_eco->refrigerationSale->customer;
            $customer->updateRevenue($sales_order_eco->refrigerationSale->total * -1);
        }

        return redirect("/$shipment/sales-order-eco")->with('deleted', 'Order deleted successfully');
    }
}
