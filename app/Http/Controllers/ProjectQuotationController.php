<?php

namespace App\Http\Controllers;

use App\Models\StorageItem;
use App\Models\ProjectInvoice;
use App\Models\ProjectOrder;
use App\Models\ProjectQuotation;
use App\Models\Country;
use App\Models\ProjectQuotationInvoice;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProjectQuotationController extends Controller
{
    public function select2Query(Request $request)
    {
        return ProjectQuotation::when($request->has('search'), function ($query) use ($request) {
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
        WHEN status = " . ProjectQuotation::STATUS_PENDING . " AND created_at >= CURDATE() THEN 1
        WHEN status = " . ProjectQuotation::STATUS_ACCEPTED . " THEN 2
        WHEN status = " . ProjectQuotation::STATUS_PENDING . " AND created_at < CURDATE() THEN 3
        WHEN status = " . ProjectQuotation::STATUS_INVOICED . " THEN 4
        WHEN status = " . ProjectQuotation::STATUS_VOID . " THEN 5
        ELSE 6 END";

        $ascOrder = "CASE
        WHEN status = " . ProjectQuotation::STATUS_VOID . " THEN 1
        WHEN status = " . ProjectQuotation::STATUS_INVOICED . " THEN 2
        WHEN status = " . ProjectQuotation::STATUS_PENDING . " AND created_at < CURDATE() THEN 3
        WHEN status = " . ProjectQuotation::STATUS_ACCEPTED . " THEN 4
        WHEN status = " . ProjectQuotation::STATUS_PENDING . " AND created_at >= CURDATE() THEN 5
        ELSE 6 END";

        // Query to retrieve project quotations with related entities
        $projectQuotations = ProjectQuotation::with(['projectOrder', 'customer', 'vehicle', 'invoices', 'vehicleParts'])
            ->when($search, function ($query) use ($search) {
                $query->where('project_order_value', 'LIKE', "%{$search}%");
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
        $projectQuotations->appends($queryString);

        // Return view with project quotations and filters
        return Inertia::render('ProjectQuotation/Index', [
            'projectQuotations' => $projectQuotations,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param ProjectQuotation $project_quotation
     * @return Response
     */
    public function show(Request $request, ProjectQuotation $project_quotation): Response
    {
        // Load necessary relationships
        $project_quotation->load(['projectOrder', 'customer', 'vehicle', 'vehicleParts.storageItem', 'invoices', 'proformaInvoice']);

        return Inertia::render('ProjectQuotation/Detail', [
            'projectQuotation' => $project_quotation,
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
        $phoneCodes = (new ProjectQuotationController)->toSelect2Data($countries, 'id', 'phone_code');

        return Inertia::render('ProjectQuotation/Form', [
            'csrf' => csrf_token(),
            'types' => ProjectQuotation::TYPES,
            'statuses' => ProjectQuotation::STATUS_SELECT2,
            'phoneCodes' => $phoneCodes,
            'depositOption' => ProjectQuotation::DEPOSIT_OPTION,
            'paymentOption' => ProjectQuotation::PAYMENT_METHOD,
            'currencyOption' => ProjectQuotation::CURRENCIES,
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
            'project_order_id' => 'nullable|exists:project_orders,id',
            'project_quotation_id' => 'nullable|exists:project_quotations,id',
            'project_quotation_data' => 'nullable|array',
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
            'project_order_type' => 'nullable|string|max:255',
            'project_order_value' => 'nullable|string|max:255',
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
            'vehicle_parts.*.id' => 'nullable|exists:project_quotation_vehicle_parts,id',
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

        // Create the project quotation
        $quotation = ProjectQuotation::create($data);

        // Create vehicle parts
        foreach ($request->vehicle_parts as $part) {
            $storage_item = StorageItem::find($part['storage_item_id'])->load('product');

            if ($storage_item && $part['quantity']) {
                $quotation->vehicleParts()->create([
                    'project_quotation_id' => $quotation->id,
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
            $path = $request->file('image_url')->store("project_quotations/$quotation->id", 'public');
            $quotation->update(['image_url' => $path]);
        }

        if($request->has('is_generate_project_order') && $request->is_generate_project_order){
            self::generateProjectOrder($quotation);
        }

        // Redirect with a success message
        return redirect('/project-quotations')->with('created', 'Project quotation created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param ProjectQuotation $project_quotation
     * @return Response
     */
    public function edit(ProjectQuotation $project_quotation): Response
    {
        // Load all necessary relationships
        $project_quotation->load('projectOrder', 'customer', 'vehicle', 'vehicleParts.storageItem.product.uom', 'invoices');

        // Fetch countries and prepare phone codes for Select2
        $countries = Country::all()->toArray();
        $phoneCodes = (new ProjectQuotationController)->toSelect2Data($countries, 'id', 'phone_code');

        // Prepare data for Select2 components
        $projectOrders = $project_quotation->projectOrder ? [
            [
                'id' => $project_quotation->projectOrder->id,
                'text' => $project_quotation->projectOrder->code
            ]
        ] : [];
        $customers = $this->toSelect2Data([$project_quotation->customer], 'id', 'code', 'name', ' | ');
        $vehicles = $this->toSelect2Data([$project_quotation->vehicle], 'id', 'license_plate');


        $storage_items = $project_quotation->vehicleParts->pluck('storageItem')->flatten()->map(function ($item) {
            if (!empty($item)) {
                return [
                    'id' => $item->id,
                    'text' => $item->storage->code . ' - ' . $item->product->sku . ' | ' . $item->product->name
                ];
            }
        });

        // Return the Inertia response with the required data
        return Inertia::render('ProjectQuotation/Form', [
            'csrf' => csrf_token(),
            'types' => ProjectQuotation::TYPES,
            'statuses' => ProjectQuotation::STATUS_SELECT2,
            'phoneCodes' => $phoneCodes,
            'depositOption' => ProjectQuotation::DEPOSIT_OPTION,
            'paymentOption' => ProjectQuotation::PAYMENT_METHOD,
            'currencyOption' => ProjectQuotation::CURRENCIES,
            'quotation' => $project_quotation,
            'projectOrders' => $projectOrders,
            'customer' => $project_quotation->customer,
            'customers' => $customers,
            'vehicle' => $project_quotation->vehicle,
            'vehicles' => $vehicles,
            'storageItems' => $storage_items,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ProjectQuotation $project_quotation
     * @return RedirectResponse
     */
    public function update(Request $request, ProjectQuotation $project_quotation)
    {
        // Validation rules with custom messages
        $request->validate([
            'project_order_id' => 'nullable|exists:project_orders,id',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'pic_first_name' => 'nullable|string|max:255',
            'pic_last_name' => 'nullable|string|max:255',
            'pic_title' => 'nullable|string|max:255',
            'pic_country_code' => 'nullable|exists:countries,id',
            'pic_phone_number' => 'nullable|string|max:20',
            'pic_email' => 'nullable|email|max:255',
            'project_order_type' => 'nullable|string|max:255',
            'project_order_value' => 'nullable|string|max:255',
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

        // Update the project quotation
        $project_quotation->update($data);

        $subTotal = 0;

        // Sync vehicle parts
        $project_quotation->vehicleParts()->delete();
        foreach ($request->vehicle_parts as $part) {
            $storage_item = StorageItem::find($part['storage_item_id'])->load('product');

            if ($storage_item && $part['quantity']) {
                $project_quotation->vehicleParts()->create([
                    'project_quotation_id' => $project_quotation->id,
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

        $project_quotation->update([
            'sub_total' => $subTotal,
            'discount' => $discount,
            'gst_rate' => $gstRate,
            'gst_total' => $gstTotal,
            'total' => $total,
        ]);

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store("project_quotations/$project_quotation->id", 'public');
            $project_quotation->update(['image_url' => $path]);
        }

        if (!$project_quotation->invoices()->count()) {
            $project_quotation->update(['status' => ProjectQuotation::STATUS_PENDING]);
        }

        if($request->has('is_generate_project_order') && $request->is_generate_project_order){
            self::generateProjectOrder($project_quotation);
        }

        // Redirect with a success message
        return redirect('/project-quotations')->with('updated', 'Project quotation updated successfully.');
    }


    public function generateProjectOrder(ProjectQuotation $projectQuotation)
    {
        // Create the project order based on the project quotation
        $projectOrder = ProjectOrder::create([
            'project_quotation_id' => $projectQuotation->id,
            'customer_id' => $projectQuotation->customer_id,
            'vehicle_id' => $projectQuotation->vehicle_id,
            'pic_first_name' => $projectQuotation->pic_first_name,
            'pic_last_name' => $projectQuotation->pic_last_name,
            'pic_title' => $projectQuotation->pic_title,
            'pic_country_code' => $projectQuotation->pic_country_code,
            'pic_phone_number' => $projectQuotation->pic_phone_number,
            'pic_email' => $projectQuotation->pic_email,
            'project_order_type' => $projectQuotation->project_order_type,
            'remark' => $projectQuotation->remark,
            'status' => ProjectOrder::STATUS_PENDING, // Initial status of the project order
            'code' => 'SO' . str_pad(ProjectOrder::max('id') + 1, 4, '0', STR_PAD_LEFT), // Generate project order code
        ]);

        // Update the project quotation to link it with the newly created project order
        $projectQuotation->update([
            'project_order_id' => $projectOrder->id,
        ]);

        // Convert vehicle parts from project quotation to project order requirements
        foreach ($projectQuotation->vehicleParts as $vehiclePart) {
            $projectOrder->requirements()->create([
                'storage_item_id' => $vehiclePart->storage_item_id,
                'quantity' => $vehiclePart->quantity,
            ]);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ProjectQuotation $project_quotation
     * @return RedirectResponse
     */
    public function updatePhoto(Request $request, ProjectQuotation $project_quotation): RedirectResponse
    {
        $request->validate([
            'signed_image_url' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        /** Update photo **/
        if ($request->hasFile('signed_image_url')) {
            $path = $request->file('signed_image_url')->store("project_quotations/$project_quotation->id", 'public');
            $project_quotation->update([
                'signed_image_url' => $path,
                'status' => ProjectQuotation::STATUS_ACCEPTED
            ]);
        }

        /** Create Invoice **/
        $invoice = ProjectInvoice::create([
            'project_quotation_id' => $project_quotation->id,
            'status' => ProjectInvoice::STATUS_NOT_INVOICED,
        ]);

        return back()->with('updated', "Project quotation updated successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ProjectQuotation $project_quotation
     * @return RedirectResponse
     */
    public function void(Request $request, ProjectQuotation $project_quotation): RedirectResponse
    {
        $project_quotation->update(['status' => ProjectQuotation::STATUS_VOID]);

        return back()->with('updated', "Project quotation updated successfully");
    }

    public function generateProformaInvoicePdf(ProjectQuotation $project_quotation)
    {
        // Render the Blade view as HTML
        $html = view('pdf.project_quotation_proforma_invoice', [
            'quotation' => $project_quotation->load([
                'projectOrder.confirmed', 'projectOrder.technician',
                'customer.countryPhoneCode', 'customer.country',
                'vehicle', 'vehicleParts.storageItem.product.uom'
            ])
        ])->render();

        // Generate PDF from the rendered HTML
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');

        // Define the file name and path
        $fileSave = "Proforma-Invoice#{$project_quotation->code}-" . time() . ".pdf";
        $filePathSave = "project_quotations/{$project_quotation->id}/proforma_invoices/$fileSave";

        $fileStore = urlencode($fileSave);
        $filePathStore = "/storage/project_quotations/{$project_quotation->id}/proforma_invoices/$fileStore";

        // Save the PDF to the storage
        Storage::disk('public')->put($filePathSave, $pdf->output());

        // Save the ProjectQuotationInvoice record
        ProjectInvoice::create([
            'project_quotation_id' => $project_quotation->id,
            'type' => ProjectInvoice::TYPE_PROFORMA_INVOICE,
            'name' => $fileSave,
            'file_url' => $filePathStore,
            'status' => ProjectInvoice::STATUS_INVOICED,
        ]);

        return back()->with('updated', 'Proforma invoice saved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProjectQuotation $project_quotation
     * @return RedirectResponse
     */
    public function generateInvoicePdf(ProjectQuotation $project_quotation)
    {
        // Save the ProjectInvoice record
        $invoice = ProjectInvoice::create([
            'project_quotation_id' => $project_quotation->id,
            'type' => ProjectInvoice::TYPE_INVOICE,
            'status' => ProjectInvoice::STATUS_INVOICED,
        ]);

        // Update vehicle parts that don't have an invoice yet with the new invoice ID
        $project_quotation->vehicleParts()->whereNull('project_invoice_id')->update([
            'project_invoice_id' => $invoice->id
        ]);

        // Calculate amounts
        $subtotal = 0;
        $discountTotal = 0;
        $taxTotal = 0;
        $totalAmount = 0;

        // Loop through the vehicle parts associated with this invoice
        foreach ($project_quotation->vehicleParts->where('project_invoice_id', $invoice->id) as $vehiclePart) {
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
        $html = view('pdf.project_quotation', [
            'quotation' => $project_quotation->load([
                'projectOrder.confirmed', 'projectOrder.technician',
                'customer.countryPhoneCode', 'customer.country',
                'vehicle', 'vehicleParts.storageItem.product.uom'
            ]),
            'invoice' => $invoice, // Pass the invoice to the view
        ])->render();

        // Generate PDF from the rendered HTML
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');

        // Save the PDF to the storage
        $fileSave = "Invoice#{$project_quotation->code}-" . time() . ".pdf";
        $filePathSave = "project_quotations/{$project_quotation->id}/proforma_invoices/$fileSave";
        Storage::disk('public')->put($filePathSave, $pdf->output());

        $fileStore = urlencode($fileSave);
        $filePathStore = "/storage/project_quotations/{$project_quotation->id}/proforma_invoices/$fileStore";

        // Update the invoice with calculated amounts
        $invoice->update([
            'name' => "Invoice#{$project_quotation->code}-" . time() . ".pdf",
            'file_url' => $filePathStore
        ]);

        return back()->with('updated', 'Quotation invoice saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProjectQuotation $project_quotation
     * @return RedirectResponse
     */
    public function destroy(ProjectQuotation $project_quotation): RedirectResponse
    {
        // Delete the project quotation
        $project_quotation->delete();

        // Redirect back with success message
        return redirect('/project-quotations')->with('deleted', 'Project quotation deleted successfully.');
    }
}
