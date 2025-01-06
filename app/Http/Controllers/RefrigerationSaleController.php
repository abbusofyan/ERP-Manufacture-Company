<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\RefrigerationSale;
use App\Models\RefrigerationSaleHeader;
use App\Models\RefrigerationSaleHeaderType;
use App\Models\RefrigerationSaleHeaderTypeItem;
use App\Models\RefrigerationSaleInvoice;
use App\Models\RefrigerationSaleSpecification;
use App\Models\RefrigerationSaleSpecificationItem;
use App\Models\RefrigerationSaleSpecificationProcess;
use App\Models\Assembly;
use App\Models\ServiceInvoice;
use App\Models\ServiceQuotation;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class RefrigerationSaleController extends Controller
{
    public function headerSelect2Query(Request $request)
    {
        return RefrigerationSaleHeader::when($request->has('search'), function ($query) use ($request) {
            $query->where('header_name', 'LIKE', "%{$request->search}%");
        })->with(['types.items', 'refrigeration_sale'])->limit(10)->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @param $shipment
     * @param Request $request
     * @param int $post_type
     * @return Response
     */
    public function index($shipment, Request $request, int $post_type = RefrigerationSale::POST_TYPE_RS): Response
    {
        $search = $request->search;

        if ($request->order && $request->by) {
            $order = $request->order;
            $by = $request->by;
        } else {
            $order = 'id';
            $by = 'desc';
        }

        $paginate = $request->paginate ?? 10;

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $refrigerationSales = RefrigerationSale::with('customer', 'invoice')
            ->when($search, function ($query) use ($search) {
                $query->where('model', 'LIKE', "%{$search}%");
            })
            ->when($order && $by, function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })
            ->where('post_type', $post_type)
            ->where('shipment', RefrigerationSale::SHIPMENT_PARAM[$shipment])
            ->paginate($paginate)
            ->appends([
                'search' => $search,
                'order' => $order,
                'by' => $by,
                'paginate' => $paginate,
            ]);

        return Inertia::render('RefrigerationSale/Index', [
            'shipment' => $shipment,
            'refrigerationSales' => $refrigerationSales,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $shipment
     * @param int $post_type
     * @return Response
     */
    public function create($shipment, int $post_type = RefrigerationSale::POST_TYPE_RS): Response
    {
        $code = RefrigerationSale::latest()->value('id') ?? 0;
        $assemblies = Assembly::all();
        return Inertia::render('RefrigerationSale/Form', [
            'shipment' => $shipment,
            'csrf' => csrf_token(),
            'depositOption' => ServiceQuotation::DEPOSIT_OPTION,
            'paymentOption' => ServiceQuotation::PAYMENT_METHOD,
            'currencyOption' => ServiceQuotation::CURRENCIES,
            'item_types' => RefrigerationSaleHeaderType::TYPE_ARR,
            'code' => $code,
            'status_args' => RefrigerationSale::STATUS_SELECT,
            'assemblies' => $assemblies
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $shipment
     * @param Request $request
     * @param int $post_type
     * @return Application|Redirector|RedirectResponse
     */
    public function store($shipment, Request $request, int $post_type = RefrigerationSale::POST_TYPE_RS): Redirector|RedirectResponse|Application
    {
        $require = 'nullable';
        $header_item_required = '|required_if:headers.*.isDeleted,false';
        $type_item_required = '|required_if:headers.*.types.*.isDeleted,false';
        if ($request->hasFile('mockup_image_url')) {
            $require = 'nullable';
            $header_item_required = '';
            $type_item_required = '';
        }

        $request->validate([
            /** Customer Info **/
            'customer_id' => 'required|exists:customers,id',
            'confirmed_by' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'deposit_value' => 'nullable|numeric|min:0',
            'deposit_option' => 'nullable|string|max:255',
            'finance_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'terms' => 'nullable|string|max:255',
            'validity_date' => 'nullable|date', // Ensure it's a valid date
            'estimated_delivery_date' => 'nullable|date', // Ensure it's a valid date
            'currency' => 'nullable|string|max:10', // For currency codes like "USD", "SGD"
            'currency_rate' => 'nullable|numeric|min:0',
            'rounding' => 'nullable|integer|min:0|max:4', // Assuming rounding is a number of decimal places

            /** Headers **/
            'headers' => "$require|array",
            'headers.*.header_name' => "nullable|string",
            'headers.*.quantity' => 'nullable|integer',
            'headers.*.discount' => 'nullable|numeric',
            'headers.*.unit_price' => 'nullable|numeric',
            'headers.*.isDeleted' => 'nullable|boolean',

            /** Types **/
            'headers.*.types' => "$require|array",
            'headers.*.types.*.box' => "nullable$type_item_required|string",
            'headers.*.types.*.description' => 'nullable|string',
            'headers.*.types.*.quantity' => "nullable$type_item_required|integer",
            'headers.*.types.*.isDeleted' => 'nullable|boolean',

            /** Items **/
            'headers.*.types.*.items' => 'required|array',
            'headers.*.types.*.items.*.type' => "nullable|integer|in:1,2",
            'headers.*.types.*.items.*.assembly_id' => 'nullable|exists:assemblies,id',
            'headers.*.types.*.items.*.product_id' => 'nullable|exists:products,id',
            'headers.*.types.*.items.*.uom' => 'nullable|string',
            'headers.*.types.*.items.*.quantity' => "nullable|integer",
            'headers.*.types.*.items.*.unit_price' => "nullable|numeric",
            'headers.*.types.*.items.*.discount' => 'nullable|numeric',
            'headers.*.types.*.items.*.is_foc' => 'nullable|boolean',
            'headers.*.types.*.items.*.hide' => 'nullable|boolean',
            'headers.*.types.*.items.*.isDeleted' => 'nullable|boolean',
            'headers.*.types.*.items.*.description' => 'nullable|string',
            'headers.*.types.*.items.*.misc_description' => 'nullable|string',
        ], [
            'required' => 'The :attribute field is required.',
            'integer' => 'The :attribute field must be an integer.',
            'string' => 'The :attribute field must be a string.',
            'numeric' => 'The :attribute field must be a number.',
            'array' => 'The :attribute field must be an array.',
            'nullable' => 'The :attribute field can be empty.',
            'boolean' => 'The :attribute field must be true or false.',
            'email' => 'The :attribute must be a valid email address.',
            'max' => 'The :attribute may not be greater than :max characters.',
        ], [
            /** Headers **/
            'headers' => 'headers',
            'headers.*.header_name' => 'header name',
            'headers.*.quantity' => 'quantity',
            'headers.*.discount' => 'discount',
            'headers.*.unit_price' => 'unit price',

            /** Types **/
            'headers.*.types' => 'headers',
            'headers.*.types.*.box' => 'vehicle spec',
            'headers.*.types.*.description' => 'description',

            /** Items **/
            'headers.*.types.*.items' => 'required|array',
            'headers.*.types.*.items.*.type' => "nullable|integer|in:1,2",
            'headers.*.types.*.items.*.assembly_id' => "nullable|exists:assemblies,id",
            'headers.*.types.*.items.*.product_id' => "nullable|exists:products,id",
            'headers.*.types.*.items.*.uom' => "nullable|string",
            'headers.*.types.*.items.*.quantity' => "nullable|integer",
            'headers.*.types.*.items.*.unit_price' => "nullable|numeric",
            'headers.*.types.*.items.*.discount_type' => "nullable|in:Flat,Percent",
            'headers.*.types.*.items.*.discount_amount' => "nullable|numeric",
            'headers.*.types.*.items.*.discount_value' => "nullable|numeric",
            'headers.*.types.*.items.*.tax_code' => "nullable|string",
            'headers.*.types.*.items.*.tax_value' => "nullable|numeric",
            'headers.*.types.*.items.*.tax_amount' => "nullable|numeric",
            'headers.*.types.*.items.*.description' => 'nullable|string',
            'headers.*.types.*.items.*.misc_description' => 'nullable|string',
            'headers.*.types.*.items.*.is_foc' => 'nullable|boolean',
            'headers.*.types.*.items.*.hide' => 'nullable|boolean',
            'headers.*.types.*.items.*.isDeleted' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['post_type'] = $post_type;
        $data['shipment'] = RefrigerationSale::SHIPMENT_PARAM[$shipment];

        $rs = RefrigerationSale::create($data);
        $rs->update(['code' => str_pad($rs->id, 4, '0', STR_PAD_LEFT)]);

        if (isset($data['headers']) && is_array($data['headers'])) {
            foreach ($data['headers'] as $header) {
                if (!$header['isDeleted']) {
                    $header['rs_id'] = $rs->id;
                    $rsHeader = RefrigerationSaleHeader::create($header);

                    if (isset($header['types']) && is_array($header['types'])) {
                        foreach ($header['types'] as $type) {
                            if (!$type['isDeleted']) {
                                $type['rs_header_id'] = $rsHeader->id;
                                $rsType = RefrigerationSaleHeaderType::create($type);

                                if (isset($type['items']) && is_array($type['items'])) {
                                    foreach ($type['items'] as $assembly) {
                                        if (!$assembly['isDeleted']) {
                                            $assembly['rs_header_type_id'] = $rsType->id;
                                            RefrigerationSaleHeaderTypeItem::create($assembly);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        /** Update photo **/
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store("refrigeration_sale_file/$rs->id", 'public');
            $rs->update(['photo' => $path]);
        }

        /** Update mockup **/
        if ($request->hasFile('mockup_image_url')) {
            $path = $request->file('mockup_image_url')->store("refrigeration_sale_file/$rs->id", 'public');
            $rs->update(['mockup_image_url' => $path]);
        }

        return redirect("/$shipment/refrigeration-sales")->with('created', "Quotation created successfully");
    }


    /**
     * Show the form for editing the specified Refrigeration Sale.
     *
     * @param string $shipment
     * @param RefrigerationSale $refrigeration_sale
     * @param int $post_type
     * @return \Inertia\Response
     */
    public function edit($shipment, RefrigerationSale $refrigeration_sale, int $post_type = RefrigerationSale::POST_TYPE_RS): Response
    {
        $product_ids = [];
        $assembly_ids = []; // Initialize an array to collect assembly IDs

        foreach ($refrigeration_sale->headers as $header) {
            foreach ($header->types as $type) {
                foreach ($type->items as $item) {
                    if (isset($item['product_id']) && $item['product_id']) {
                        $product_ids[] = $item['product_id'];
                    }
                    if (isset($item['assembly_id']) && $item['assembly_id']) { // Collect assembly IDs
                        $assembly_ids[] = $item['assembly_id'];
                    }
                }
            }
        }

        // Retrieve customers
        $customers = self::toSelect2Data(
            Customer::where('id', $refrigeration_sale->customer_id)->get()->toArray(),
            'id',
            'name'
        );

        // Retrieve products based on collected product IDs
        $products = Product::whereIn('id', $product_ids)->get()->toArray();

        // Retrieve assemblies based on collected assembly IDs
        $assemblies = Assembly::whereIn('id', $assembly_ids)->get()->toArray();

        $item_types = RefrigerationSaleHeaderType::TYPE_ARR;
        $code = RefrigerationSale::latest()->value('id') ?? 0;

        return Inertia::render('RefrigerationSale/Form', [
            'shipment' => $shipment,
            'refrigeration_sale' => $refrigeration_sale->load('headers.types.items', 'customer'),
            'csrf' => csrf_token(),
            'depositOption' => ServiceQuotation::DEPOSIT_OPTION,
            'paymentOption' => ServiceQuotation::PAYMENT_METHOD,
            'currencyOption' => ServiceQuotation::CURRENCIES,
            'item_types' => $item_types,
            'code' => $code,
            'status_args' => RefrigerationSale::STATUS_SELECT,

            /** Get existing data without AJAX **/
            'customers' => $customers,
            'products' => $products,
            'assemblies' => $assemblies, // Pass assemblies to the frontend
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param $shipment
     * @param Request $request
     * @param RefrigerationSale $refrigeration_sale
     * @param int $post_type
     * @return Application|Redirector|RedirectResponse
     */
    public function update($shipment, Request $request, RefrigerationSale $refrigeration_sale, int $post_type = RefrigerationSale::POST_TYPE_RS): Redirector|RedirectResponse|Application
    {
        $require = 'required';
        $header_item_required = '|required_if:headers.*.isDeleted,false';
        $type_item_required = '|required_if:headers.*.types.*.isDeleted,false';
        if ($request->hasFile('mockup_image_url')) {
            $require = 'nullable';
            $header_item_required = '';
            $type_item_required = '';
        }

        $request->validate([
            /** Customer Info **/
            'customer_id' => 'required|exists:customers,id',
            'confirmed_by' => 'required|string|max:255',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'deposit_value' => 'nullable|numeric|min:0',
            'deposit_option' => 'nullable|string|max:255',
            'finance_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'terms' => 'nullable|string|max:255',
            'validity_date' => 'nullable|date', // Ensure it's a valid date
            'estimated_delivery_date' => 'nullable|date', // Ensure it's a valid date
            'currency' => 'nullable|string|max:10', // For currency codes like "USD", "SGD"
            'currency_rate' => 'nullable|numeric|min:0',
            'rounding' => 'nullable|integer|min:0|max:4', // Assuming rounding is a number of decimal places

            /** Headers **/
            'headers' => "$require|array",
            'headers.*.header_name' => "nullable|string",
            'headers.*.quantity' => 'nullable|integer',
            'headers.*.discount' => 'nullable|numeric',
            'headers.*.unit_price' => 'nullable|numeric',
            'headers.*.isDeleted' => 'nullable|boolean',

            /** Types **/
            'headers.*.types' => "$require|array",
            'headers.*.types.*.box' => "nullable$type_item_required|string",
            'headers.*.types.*.description' => 'nullable|string',
            'headers.*.types.*.quantity' => "nullable$type_item_required|integer",
            'headers.*.types.*.isDeleted' => 'nullable|boolean',

            /** Items **/
            'headers.*.types.*.items' => 'required|array',
            'headers.*.types.*.items.*.type' => "nullable|integer|in:1,2",
            'headers.*.types.*.items.*.assembly_id' => "nullable|exists:assemblies,id",
            'headers.*.types.*.items.*.product_id' => "nullable|exists:products,id",
            'headers.*.types.*.items.*.uom' => "nullable|string",
            'headers.*.types.*.items.*.quantity' => "nullable|integer",
            'headers.*.types.*.items.*.unit_price' => "nullable|numeric",
            'headers.*.types.*.items.*.discount_type' => "nullable|in:Flat,Percent",
            'headers.*.types.*.items.*.discount_amount' => "nullable|numeric",
            'headers.*.types.*.items.*.discount_value' => "nullable|numeric",
            'headers.*.types.*.items.*.tax_code' => "nullable|string",
            'headers.*.types.*.items.*.tax_value' => "nullable|numeric",
            'headers.*.types.*.items.*.tax_amount' => "nullable|numeric",
            'headers.*.types.*.items.*.description' => 'nullable|string',
            'headers.*.types.*.items.*.misc_description' => 'nullable|string',
            'headers.*.types.*.items.*.is_foc' => 'nullable|boolean',
            'headers.*.types.*.items.*.hide' => 'nullable|boolean',
            'headers.*.types.*.items.*.isDeleted' => 'nullable|boolean',
        ], [
            'required' => 'The :attribute field is required.',
            'integer' => 'The :attribute field must be an integer.',
            'string' => 'The :attribute field must be a string.',
            'numeric' => 'The :attribute field must be a number.',
            'array' => 'The :attribute field must be an array.',
            'nullable' => 'The :attribute field can be empty.',
            'boolean' => 'The :attribute field must be true or false.',
            'email' => 'The :attribute must be a valid email address.',
            'max' => 'The :attribute may not be greater than :max characters.',
        ], [
            /** Headers **/
            'headers' => 'headers',
            'headers.*.header_name' => 'header name',
            'headers.*.quantity' => 'quantity',
            'headers.*.discount' => 'discount',
            'headers.*.unit_price' => 'unit price',

            /** Types **/
            'headers.*.types' => 'headers',
            'headers.*.types.*.box' => 'vehicle spec',
            'headers.*.types.*.description' => 'description',

            /** Items **/
            'headers.*.types.*.items' => 'item',
            'headers.*.types.*.items.*.type' => 'type',
            'headers.*.types.*.items.*.product_id' => 'product',
            'headers.*.types.*.items.*.assembly_id' => 'assembly',
            'headers.*.types.*.items.*.quantity' => 'quantity',
            'headers.*.types.*.items.*.unit_price' => 'unit price',
            'headers.*.types.*.items.*.discount' => 'discount',
            'headers.*.types.*.items.*.is_foc' => 'foc',
            'headers.*.types.*.items.*.description' => 'description',
            'headers.*.types.*.items.*.misc_description' => 'misc description',
        ]);

        $data = $request->all();
        $data['status'] = RefrigerationSale::STATUS_PENDING;
        $data['post_type'] = $post_type;
        $data['shipment'] = RefrigerationSale::SHIPMENT_PARAM[$shipment];

        $refrigeration_sale->update($data);

        if (isset($data['headers']) && is_array($data['headers'])) {
            foreach ($data['headers'] as $header) {
                $headerObj = RefrigerationSaleHeader::updateOrCreate(
                    ['id' => $header['id'] ?? null],
                    array_merge($header, ['rs_id' => $refrigeration_sale->id])
                );

                if ($header['isDeleted'] ?? false) {
                    $headerObj->delete();
                    continue;
                }

                if (isset($header['types']) && is_array($header['types'])) {
                    foreach ($header['types'] as $type) {
                        $typeObj = RefrigerationSaleHeaderType::updateOrCreate(
                            ['id' => $type['id'] ?? null],
                            array_merge($type, ['rs_header_id' => $headerObj->id])
                        );

                        if ($type['isDeleted'] ?? false) {
                            $typeObj->delete();
                            continue;
                        }

                        if (isset($type['items']) && is_array($type['items'])) {
                            foreach ($type['items'] as $item) {
                                $itemObj = RefrigerationSaleHeaderTypeItem::updateOrCreate(
                                    ['id' => $item['id'] ?? null],
                                    array_merge($item, ['rs_header_type_id' => $typeObj->id])
                                );

                                if ($item['isDeleted'] ?? false) {
                                    $itemObj->delete();
                                }
                            }
                        }
                    }
                }
            }
        }

        /** Update photo **/
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store("refrigeration_sale_file/{$refrigeration_sale->id}", 'public');
            $refrigeration_sale->update(['photo' => $path]);
        }

        /** Update mockup **/
        if ($request->hasFile('mockup_image_url')) {
            $path = $request->file('mockup_image_url')->store("refrigeration_sale_file/{$refrigeration_sale->id}", 'public');
            $refrigeration_sale->update(['mockup_image_url' => $path]);
        }

        return redirect("/$shipment/refrigeration-sales")->with('updated', "Quotation updated successfully");
    }

    /**
     * Update the photo of the specified resource in storage.
     *
     * @param $shipment
     * @param Request $request
     * @param RefrigerationSale $refrigeration_sale
     * @param int $post_type
     * @return RedirectResponse
     */
    public function updatePhoto($shipment, Request $request, RefrigerationSale $refrigeration_sale, int $post_type = RefrigerationSale::POST_TYPE_RS): RedirectResponse
    {
        /** Update photo **/
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store("refrigeration_sale_file/{$refrigeration_sale->id}", 'public');
            $refrigeration_sale->update(['photo' => $path, 'status' => RefrigerationSale::STATUS_CONFIRMED]);
        }

        return back()->with('updated', "Quotation updated successfully");
    }

    /**
     * Download the CO file as PDF.
     *
     * @param $shipment
     * @param Request $request
     * @param RefrigerationSale $refrigeration_sale
     * @param int $post_type
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadCOFile($shipment, Request $request, RefrigerationSale $refrigeration_sale, int $post_type = RefrigerationSale::POST_TYPE_RS)
    {
        // Render the Blade view as HTML
        $html = view('pdf.quotation_co', [])->render();

        // Generate PDF from the rendered HTML
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');
        return $pdf->download("Quotation-CO-File.pdf");
    }

    /**
     * Display the specified resource.
     *
     * @param $shipment
     * @param RefrigerationSale $refrigeration_sale
     * @param int $post_type
     * @return Response
     */
    public function show($shipment, RefrigerationSale $refrigeration_sale, int $post_type = RefrigerationSale::POST_TYPE_RS): Response
    {
        return Inertia::render('RefrigerationSale/Detail', [
            'shipment' => $shipment,
            'refrigeration_sale' => $refrigeration_sale->load('headers.types.items', 'headers.types.items.product', 'customer', 'invoice', 'proformaInvoice'),
        ]);
    }

    /**
     * Generate and save the Proforma Invoice PDF.
     *
     * @param $shipment
     * @param RefrigerationSale $refrigeration_sale
     * @return RedirectResponse
     */
    public function generateProformaInvoicePdf($shipment, RefrigerationSale $refrigeration_sale)
    {
        $html = view('pdf.refrigeration_sale_proforma_invoice', [
            'refrigeration_sale' => $refrigeration_sale->load([
                'customer.countryPhoneCode', 'headers.types.items', 'headers.types.items.product.uom', 'headers.types.items.assembly',
            ])
        ])->render();

        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');

        $fileSave = "Proforma-Invoice#{$refrigeration_sale->code}-" . time() . ".pdf";
        $filePathSave = "refrigeration_sales/{$refrigeration_sale->id}/proforma_invoices/$fileSave";

        $fileStore = urlencode($fileSave);
        $filePathStore = "/storage/refrigeration_sales/{$refrigeration_sale->id}/proforma_invoices/$fileStore";

        Storage::disk('public')->put($filePathSave, $pdf->output());

        RefrigerationSaleInvoice::create([
            'refrigeration_sale_id' => $refrigeration_sale->id,
            'type' => RefrigerationSaleInvoice::TYPE_PROFORMA_INVOICE,
            'name' => $fileSave,
            'file_url' => $filePathStore,
            'status' => RefrigerationSaleInvoice::STATUS_INVOICED,
        ]);

        return back()->with('updated', 'Proforma invoice saved successfully');
    }

    /**
     * Generate and save the Invoice PDF.
     *
     * @param $shipment
     * @param RefrigerationSale $refrigeration_sale
     * @return RedirectResponse
     */
    public function generateInvoicePdf($shipment, RefrigerationSale $refrigeration_sale)
    {
        $html = view('pdf.refrigeration_sale_invoice', [
            'refrigeration_sale' => $refrigeration_sale->load([
                'customer.countryPhoneCode', 'headers.types.items', 'headers.types.items.product.uom', 'headers.types.items.assembly',
            ])
        ])->render();

        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');

        $fileSave = "Invoice#{$refrigeration_sale->code}-" . time() . ".pdf";
        $filePathSave = "refrigeration_sales/{$refrigeration_sale->id}/invoices/$fileSave";

        $fileStore = urlencode($fileSave);
        $filePathStore = "/storage/refrigeration_sales/{$refrigeration_sale->id}/invoices/$fileStore";

        Storage::disk('public')->put($filePathSave, $pdf->output());

        RefrigerationSaleInvoice::create([
            'refrigeration_sale_id' => $refrigeration_sale->id,
            'type' => RefrigerationSaleInvoice::TYPE_INVOICE,
            'name' => $fileSave,
            'file_url' => $filePathStore,
            'status' => RefrigerationSaleInvoice::STATUS_INVOICED,
        ]);

        return back()->with('updated', 'Invoice saved successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $shipment
     * @param RefrigerationSale $refrigeration_sale
     * @param int $post_type
     * @return RedirectResponse
     */
    public function destroy($shipment, RefrigerationSale $refrigeration_sale, int $post_type = RefrigerationSale::POST_TYPE_RS): RedirectResponse
    {
        $refrigeration_sale->update(['status' => RefrigerationSale::STATUS_VOID]);

        return redirect("/$shipment/refrigeration-sales")->with('deleted', "Quotation deleted successfully");
    }
}
