<?php

namespace App\Http\Controllers;

use App\Models\StorageItem;
use App\Models\ServiceInvoice;
use App\Models\ServiceOrder;
use App\Models\ServiceQuotation;
use App\Models\Country;
use App\Models\ServiceQuotationInvoice;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ServiceQuotationController extends Controller
{
    public function select2Query(Request $request)
    {
        return ServiceQuotation::when($request->has('search'), function ($query) use ($request) {
            $query->where('code', 'LIKE', "%{$request->search}%");
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

        // Determine pagination limit
        $paginate = $request->paginate ?? 10;

        // Filters to pass back to the view
        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        // Define the order case for ascending and descending order
        $descOrder = "CASE
        WHEN status = " . ServiceQuotation::STATUS_PENDING . " AND created_at >= CURDATE() THEN 1
        WHEN status = " . ServiceQuotation::STATUS_ACCEPTED . " THEN 2
        WHEN status = " . ServiceQuotation::STATUS_PENDING . " AND created_at < CURDATE() THEN 3
        WHEN status = " . ServiceQuotation::STATUS_INVOICED . " THEN 4
        WHEN status = " . ServiceQuotation::STATUS_VOID . " THEN 5
        ELSE 6 END";

        $ascOrder = "CASE
        WHEN status = " . ServiceQuotation::STATUS_VOID . " THEN 1
        WHEN status = " . ServiceQuotation::STATUS_INVOICED . " THEN 2
        WHEN status = " . ServiceQuotation::STATUS_PENDING . " AND created_at < CURDATE() THEN 3
        WHEN status = " . ServiceQuotation::STATUS_ACCEPTED . " THEN 4
        WHEN status = " . ServiceQuotation::STATUS_PENDING . " AND created_at >= CURDATE() THEN 5
        ELSE 6 END";

        // Query to retrieve service quotations with related entities
        $serviceQuotations = ServiceQuotation::with(['serviceOrder', 'customer', 'vehicle', 'invoices', 'vehicleParts'])
            ->when($search, function ($query) use ($search) {
                $query->where('service_order_value', 'LIKE', "%{$search}%");
            })
            ->when(!$order || $order === 'status', function ($query) use ($by, $ascOrder, $descOrder) {
                $query->orderByRaw($by === 'asc' ? $ascOrder : $descOrder);
            })
            ->when($order && $order !== 'status', function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        // Append query string parameters for pagination
        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];
        $serviceQuotations->appends($queryString);

        // Return view with service quotations and filters
        return Inertia::render('ServiceQuotation/Index', [
            'serviceQuotations' => $serviceQuotations,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param ServiceQuotation $service_quotation
     * @return Response
     */
    public function show(Request $request, ServiceQuotation $service_quotation): Response
    {
        // Load necessary relationships
        $service_quotation->load(['serviceOrder', 'customer', 'vehicle', 'vehicleParts.storageItem', 'invoices', 'proformaInvoice']);

        return Inertia::render('ServiceQuotation/Detail', [
            'serviceQuotation' => $service_quotation,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        $countries = Country::all()->toArray();
        $phoneCodes = (new ServiceQuotationController)->toSelect2Data($countries, 'id', 'phone_code');

        return Inertia::render('ServiceQuotation/Form', [
            'csrf' => csrf_token(),
            'types' => ServiceQuotation::TYPES,
            'statuses' => ServiceQuotation::STATUS_SELECT2,
            'phoneCodes' => $phoneCodes,
            'depositOption' => ServiceQuotation::DEPOSIT_OPTION,
            'paymentOption' => ServiceQuotation::PAYMENT_METHOD,
            'currencyOption' => ServiceQuotation::CURRENCIES,
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
        $request->validate([
            'service_order_id' => 'nullable|exists:service_orders,id',
            'service_quotation_id' => 'nullable|exists:service_quotations,id',
            'service_quotation_data' => 'nullable|array',
            'status' => 'required|integer|in:1,2,3,4', // Assuming the status values are 1, 2, 3, 4
            'customer' => 'nullable|array', // Allowing customer data array if needed
            'customer_id' => 'required|exists:customers,id',
            'vehicle' => 'nullable|array', // Allowing vehicle data array if needed
            'vehicle_id' => 'required|exists:vehicles,id',
            'pic_first_name' => 'nullable|string|max:255',
            'pic_last_name' => 'nullable|string|max:255',
            'pic_title' => 'nullable|string|max:255',
            'pic_country_code' => 'nullable|exists:countries,id',
            'pic_phone_number' => 'nullable|string|max:20',
            'pic_email' => 'nullable|email|max:255',
            'service_order_type' => 'nullable|string|max:255',
            'service_order_value' => 'nullable|string|max:255',
            'deposit_value' => 'nullable|numeric|min:0',
            'deposit_option' => 'nullable|string|max:255',
            'finance_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'terms' => 'nullable|string|max:255',
            'validity_date' => 'nullable|date', // Ensure it's a valid date
            'currency' => 'nullable|string|max:10', // For currency codes like "USD", "SGD"
            'currency_rate' => 'nullable|numeric|min:0',
            'rounding' => 'nullable|integer|min:0|max:4', // Assuming rounding is a number of decimal places
            'vehicle_parts' => 'required|array',
            'vehicle_parts.*.id' => 'nullable|exists:service_quotation_vehicle_parts,id',
            'vehicle_parts.*.storage_item_id' => 'required|exists:storage_items,id',
            'vehicle_parts.*.quantity' => 'required|integer|min:1',
            'vehicle_parts.*.storage_item' => 'nullable|array',
            'vehicle_parts.*.tax_code' => 'nullable|string|max:100',
            'vehicle_parts.*.tax_value' => 'nullable|numeric|min:0|max:100', // Assuming tax is a percentage
            'vehicle_parts.*.discount' => 'nullable|numeric|min:0|max:100', // Assuming discount is a percentage
            'vehicle_parts.*.discount_amount' => 'nullable|numeric|min:0',
            'vehicle_parts.*.subtotal_amount' => 'nullable|numeric|min:0',
            'vehicle_parts.*.total_amount' => 'nullable|numeric|min:0',
            'vehicle_parts.*.is_foc' => 'nullable|boolean',
            'vehicle_parts.*.description' => 'nullable|string|max:255',
            'vehicle_parts.*.misc_description' => 'nullable|string|max:255',
            'vehicle_parts.*.is_hide' => 'nullable|boolean',
            'remark' => 'nullable|string|max:255',
            'sub_total' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'gst_total' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();

        // Create the service quotation
        $quotation = ServiceQuotation::create($data);

        // Create vehicle parts
        foreach ($request->vehicle_parts as $part) {
            $storage_item = StorageItem::find($part['storage_item_id'])->load('product');

            if ($storage_item && $part['quantity']) {
                $quotation->vehicleParts()->create([
                    'service_quotation_id' => $quotation->id,
                    'storage_item_id' => $part['storage_item_id'],
                    'name' => $storage_item->product ? $storage_item->product->name : '-',
                    'quantity' => $part['quantity'],
                    'tax_code' => $part['tax_code'] ?? null,
                    'tax_value' => $part['tax_value'] ?? 0,
                    'discount' => $part['discount'] ?? 0,
                    'discount_amount' => $part['discount_amount'] ?? 0,
                    'subtotal_amount' => $part['subtotal_amount'] ?? 0,
                    'total_amount' => $part['total_amount'] ?? 0,
                    'is_foc' => $part['is_foc'] ?? false,
                    'description' => $part['description'] ?? null,
                    'misc_description' => $part['misc_description'] ?? null,
                    'is_hide' => $part['is_hide'] ?? false,
                ]);
            }
        }

        // Use the updatePricing method to calculate totals
        $quotation->updatePricing();

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store("service_quotations/$quotation->id", 'public');
            $quotation->update(['image_url' => $path]);
        }

        if($request->has('is_generate_service_order') && $request->is_generate_service_order){
            self::generateServiceOrder($quotation);
        }

        // Update ServiceOrder requirements if service_order_id is not null
        if ($quotation->service_order_id) {
            $this->updateServiceOrderRequirements($quotation);
        }

        // Redirect with a success message
        return redirect('/service-quotations')->with('created', 'Service quotation created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param ServiceQuotation $service_quotation
     * @return Response
     */
    public function edit(ServiceQuotation $service_quotation): Response
    {
        // Load all necessary relationships
        $service_quotation->load('serviceOrder', 'customer', 'vehicle', 'vehicleParts.storageItem.product.uom', 'invoices');

        // Fetch countries and prepare phone codes for Select2
        $countries = Country::all()->toArray();
        $phoneCodes = (new ServiceQuotationController)->toSelect2Data($countries, 'id', 'phone_code');

        // Prepare data for Select2 components
        $serviceOrders = $service_quotation->serviceOrder ? [
            [
                'id' => $service_quotation->serviceOrder->id,
                'text' => $service_quotation->serviceOrder->code
            ]
        ] : [];
        $customers = $this->toSelect2Data([$service_quotation->customer], 'id', 'code', 'name', ' | ');
        $vehicles = $this->toSelect2Data([$service_quotation->vehicle], 'id', 'license_plate');


        $storage_items = $service_quotation->vehicleParts->pluck('storageItem')->flatten()->map(function ($item) {
            if (!empty($item)) {
                return [
                    'id' => $item->id,
                    'text' => $item->storage->code . ' - ' . $item->product->sku . ' | ' . $item->product->name
                ];
            }
        });

        // Return the Inertia response with the required data
        return Inertia::render('ServiceQuotation/Form', [
            'csrf' => csrf_token(),
            'types' => ServiceQuotation::TYPES,
            'statuses' => ServiceQuotation::STATUS_SELECT2,
            'phoneCodes' => $phoneCodes,
            'depositOption' => ServiceQuotation::DEPOSIT_OPTION,
            'paymentOption' => ServiceQuotation::PAYMENT_METHOD,
            'currencyOption' => ServiceQuotation::CURRENCIES,
            'quotation' => $service_quotation,
            'serviceOrders' => $serviceOrders,
            'customer' => $service_quotation->customer,
            'customers' => $customers,
            'vehicle' => $service_quotation->vehicle,
            'vehicles' => $vehicles,
            'storageItems' => $storage_items,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ServiceQuotation $service_quotation
     * @return RedirectResponse
     */
    public function update(Request $request, ServiceQuotation $service_quotation)
    {
        // Validation rules with custom messages
        $request->validate([
            'service_order_id' => 'nullable|exists:service_orders,id',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'pic_first_name' => 'nullable|string|max:255',
            'pic_last_name' => 'nullable|string|max:255',
            'pic_title' => 'nullable|string|max:255',
            'pic_country_code' => 'nullable|exists:countries,id',
            'pic_phone_number' => 'nullable|string|max:20',
            'pic_email' => 'nullable|email|max:255',
            'service_order_type' => 'nullable|string|max:255',
            'service_order_value' => 'nullable|string|max:255',
            'vehicle_parts' => 'required|array',
            'vehicle_parts.*.storage_item_id' => 'required|exists:storage_items,id',
            'vehicle_parts.*.name' => 'nullable|string|max:255',
            'vehicle_parts.*.quantity' => 'required|integer|min:1',
            'vehicle_parts.*.tax_code' => 'nullable|string|max:100',
            'vehicle_parts.*.tax_value' => 'nullable|numeric|min:0',
            'vehicle_parts.*.discount' => 'nullable|numeric|min:0',
            'vehicle_parts.*.discount_amount' => 'nullable|numeric|min:0',
            'vehicle_parts.*.subtotal_amount' => 'nullable|numeric|min:0',
            'vehicle_parts.*.total_amount' => 'nullable|numeric|min:0',
            'vehicle_parts.*.is_foc' => 'nullable|boolean',
            'vehicle_parts.*.description' => 'nullable|string|max:255',
            'vehicle_parts.*.misc_description' => 'nullable|string|max:255',
            'vehicle_parts.*.is_hide' => 'nullable|boolean',
            'remark' => 'nullable|string',
            'status' => 'required|integer',
        ]);

        $data = $request->all();

        // Update the service quotation
        $service_quotation->update($data);

        $subTotal = 0;

        // Sync vehicle parts
        $service_quotation->vehicleParts()->delete();
        foreach ($request->vehicle_parts as $part) {
            $storage_item = StorageItem::find($part['storage_item_id'])->load('product');

            if ($storage_item && $part['quantity']) {
                $service_quotation->vehicleParts()->create([
                    'service_quotation_id' => $service_quotation->id,
                    'storage_item_id' => $part['storage_item_id'],
                    'name' => $storage_item->product ? $storage_item->product->name : '-',
                    'quantity' => $part['quantity'],
                    'tax_code' => $part['tax_code'] ?? null,
                    'tax_value' => $part['tax_value'] ?? 0,
                    'discount' => $part['discount'] ?? 0,
                    'discount_amount' => $part['discount_amount'] ?? 0,
                    'subtotal_amount' => $part['subtotal_amount'] ?? 0,
                    'total_amount' => $part['total_amount'] ?? 0,
                    'is_foc' => $part['is_foc'] ?? false,
                    'description' => $part['description'] ?? null,
                    'misc_description' => $part['misc_description'] ?? null,
                    'is_hide' => $part['is_hide'] ?? false,
                ]);
            }

            $subTotal += $part['subtotal_amount'];
        }

        // Calculate discount, gst, and total
        $discount = $data['discount'] ?? 0;
        $gstRate = config('app.gst_rate');
        $gstTotal = number_format(($subTotal - $discount) * ($gstRate / 100), 2);
        $total = $subTotal - $discount + (float)$gstTotal;

        $service_quotation->update([
            'sub_total' => $subTotal,
            'discount' => $discount,
            'gst_rate' => $gstRate,
            'gst_total' => $gstTotal,
            'total' => $total,
        ]);

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store("service_quotations/$service_quotation->id", 'public');
            $service_quotation->update(['image_url' => $path]);
        }

        if (!$service_quotation->invoices()->count()) {
            $service_quotation->update(['status' => ServiceQuotation::STATUS_PENDING]);
        }

        if($request->has('is_generate_service_order') && $request->is_generate_service_order){
            self::generateServiceOrder($service_quotation);
        }

        // Update ServiceOrder requirements if service_order_id is not null
        if ($service_quotation->service_order_id) {
            $this->updateServiceOrderRequirements($service_quotation);
        }

        // Redirect with a success message
        return redirect('/service-quotations')->with('updated', 'Service quotation updated successfully.');
    }


    public function generateServiceOrder(ServiceQuotation $serviceQuotation)
    {
        // Create the service order based on the service quotation
        $serviceOrder = ServiceOrder::create([
            'service_quotation_id' => $serviceQuotation->id,
            'customer_id' => $serviceQuotation->customer_id,
            'vehicle_id' => $serviceQuotation->vehicle_id,
            'pic_first_name' => $serviceQuotation->pic_first_name,
            'pic_last_name' => $serviceQuotation->pic_last_name,
            'pic_title' => $serviceQuotation->pic_title,
            'pic_country_code' => $serviceQuotation->pic_country_code,
            'pic_phone_number' => $serviceQuotation->pic_phone_number,
            'pic_email' => $serviceQuotation->pic_email,
            'service_order_type' => $serviceQuotation->service_order_type,
            'remark' => $serviceQuotation->remark,
            'status' => ServiceOrder::STATUS_PENDING, // Initial status of the service order
            'code' => 'SO' . str_pad(ServiceOrder::max('id') + 1, 4, '0', STR_PAD_LEFT), // Generate service order code
        ]);

        // Update the service quotation to link it with the newly created service order
        $serviceQuotation->update([
            'service_order_id' => $serviceOrder->id,
        ]);

        // Convert vehicle parts from service quotation to service order requirements
        foreach ($serviceQuotation->vehicleParts as $vehiclePart) {
            $serviceOrder->requirements()->create([
                'storage_item_id' => $vehiclePart->storage_item_id,
                'quantity' => $vehiclePart->quantity,
            ]);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ServiceQuotation $service_quotation
     * @return RedirectResponse
     */
    public function updatePhoto(Request $request, ServiceQuotation $service_quotation): RedirectResponse
    {
        $request->validate([
            'signed_image_url' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        /** Update photo **/
        if ($request->hasFile('signed_image_url')) {
            $path = $request->file('signed_image_url')->store("service_quotations/$service_quotation->id", 'public');
            $service_quotation->update([
                'signed_image_url' => $path,
                'status' => ServiceQuotation::STATUS_ACCEPTED
            ]);
        }

        /** Create Invoice **/
        $invoice = ServiceInvoice::create([
            'service_quotation_id' => $service_quotation->id,
            'status' => ServiceInvoice::STATUS_NOT_INVOICED,
        ]);

        return back()->with('updated', "Service quotation updated successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ServiceQuotation $service_quotation
     * @return RedirectResponse
     */
    public function void(Request $request, ServiceQuotation $service_quotation): RedirectResponse
    {
        $service_quotation->update(['status' => ServiceQuotation::STATUS_VOID]);

        return back()->with('updated', "Service quotation updated successfully");
    }

    public function generateProformaInvoicePdf(ServiceQuotation $service_quotation)
    {
        // Render the Blade view as HTML
        $html = view('pdf.service_quotation_proforma_invoice', [
            'quotation' => $service_quotation->load([
                'serviceOrder.confirmed', 'serviceOrder.technician',
                'customer.countryPhoneCode', 'customer.country',
                'vehicle', 'vehicleParts.storageItem.product.uom'
            ])
        ])->render();

        // Generate PDF from the rendered HTML
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');

        // Define the file name and path
        $fileSave = "Proforma-Invoice#{$service_quotation->code}-" . time() . ".pdf";
        $filePathSave = "service_quotations/{$service_quotation->id}/proforma_invoices/$fileSave";

        $fileStore = urlencode($fileSave);
        $filePathStore = "/storage/service_quotations/{$service_quotation->id}/proforma_invoices/$fileStore";

        // Save the PDF to the storage
        Storage::disk('public')->put($filePathSave, $pdf->output());

        // Save the ServiceQuotationInvoice record
        ServiceInvoice::create([
            'service_quotation_id' => $service_quotation->id,
            'type' => ServiceInvoice::TYPE_PROFORMA_INVOICE,
            'name' => $fileSave,
            'file_url' => $filePathStore,
            'status' => ServiceInvoice::STATUS_INVOICED,
        ]);

        return back()->with('updated', 'Proforma invoice saved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServiceQuotation $service_quotation
     * @return RedirectResponse
     */
    public function generateInvoicePdf(ServiceQuotation $service_quotation)
    {
        // Save the ServiceInvoice record
        $invoice = ServiceInvoice::create([
            'service_quotation_id' => $service_quotation->id,
            'type' => ServiceInvoice::TYPE_INVOICE,
            'status' => ServiceInvoice::STATUS_INVOICED,
        ]);

        // Update vehicle parts that don't have an invoice yet with the new invoice ID
        $service_quotation->vehicleParts()->whereNull('service_invoice_id')->update([
            'service_invoice_id' => $invoice->id
        ]);

        // Calculate amounts
        $subtotal = 0;
        $discountTotal = 0;
        $taxTotal = 0;
        $totalAmount = 0;

        // Loop through the vehicle parts associated with this invoice
        foreach ($service_quotation->vehicleParts->where('service_invoice_id', $invoice->id) as $vehiclePart) {
            // Add to the totals
            $subtotal += floatval($vehiclePart->subtotal_amount);
            $discountTotal += floatval($vehiclePart->discount_amount);
            $taxTotal += floatval($vehiclePart->tax_amount);
            $totalAmount += floatval($vehiclePart->total_amount);
        }


        // Update the invoice with calculated amounts
        $invoice->update([
            'subtotal_amount' => $subtotal,
            'discount_amount' => $discountTotal,
            'tax_amount' => $taxTotal,
            'total_amount' => $totalAmount,
        ]);

        // Render the Blade view as HTML
        $html = view('pdf.service_quotation', [
            'quotation' => $service_quotation->load([
                'serviceOrder.confirmed', 'serviceOrder.technician',
                'customer.countryPhoneCode', 'customer.country',
                'vehicle', 'vehicleParts.storageItem.product.uom'
            ]),
            'invoice' => $invoice, // Pass the invoice to the view
        ])->render();

        // Generate PDF from the rendered HTML
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');

        // Save the PDF to the storage
        $fileSave = "Invoice#{$service_quotation->code}-" . time() . ".pdf";
        $filePathSave = "service_quotations/{$service_quotation->id}/proforma_invoices/$fileSave";
        Storage::disk('public')->put($filePathSave, $pdf->output());

        $fileStore = urlencode($fileSave);
        $filePathStore = "/storage/service_quotations/{$service_quotation->id}/proforma_invoices/$fileStore";

        // Update the invoice with calculated amounts
        $invoice->update([
            'name' => "Invoice#{$service_quotation->code}-" . time() . ".pdf",
            'file_url' => $filePathStore
        ]);

        return back()->with('updated', 'Quotation invoice saved successfully');
    }

    protected function updateServiceOrderRequirements(ServiceQuotation $quotation)
    {
        $serviceOrder = $quotation->serviceOrder;
        if (!$serviceOrder) {
            return;
        }

        // Get existing requirements' storage_item_ids
        $existingRequirements = $serviceOrder->requirements->pluck('storage_item_id')->toArray();

        // For each vehiclePart in quotation
        foreach ($quotation->vehicleParts as $vehiclePart) {
            // Check if storage_item_id exists in requirements
            if (!in_array($vehiclePart->storage_item_id, $existingRequirements)) {
                // Create new requirement
                $serviceOrder->requirements()->create([
                    'storage_item_id' => $vehiclePart->storage_item_id,
                    'quantity' => $vehiclePart->quantity,
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ServiceQuotation $service_quotation
     * @return RedirectResponse
     */
    public function destroy(ServiceQuotation $service_quotation): RedirectResponse
    {
        // Delete the service quotation
        $service_quotation->delete();

        // Redirect back with success message
        return redirect('/service-quotations')->with('deleted', 'Service quotation deleted successfully.');
    }
}
