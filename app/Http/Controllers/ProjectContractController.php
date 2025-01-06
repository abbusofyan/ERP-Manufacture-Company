<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Customer;
use App\Models\RefrigerationSaleHeaderType;
use App\Models\ProjectContractInvoice;
use App\Models\ProjectContractPricing;
use App\Models\ProjectContractVehicleItemDetail;
use App\Models\ProjectMaintain;
use App\Models\Vehicle;
use App\Models\ProjectContract;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade as PDF;

class ProjectContractController extends Controller
{
    public function select2Query(Request $request)
    {
        return ProjectContract::when($request->has('search'), function ($query) use ($request) {
            $query->where('project_contract_number', 'LIKE', "%{$request->search}%");
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
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';

        // Determine pagination limit
        $paginate = $request->paginate ?? 10;

        // Filters to pass back to the view
        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        // Query to retrieve contracts with related customer and vehicle
        $contracts = ProjectContract::with(['customer', 'vehicles'])
            ->when($search, function ($query) use ($search) {
                $query->where('project_contract_number', 'LIKE', "%{$search}%");
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $contracts->getCollection()->transform(function ($contract) {
            $contract->vehicle_license_plates = $contract->vehicles()->pluck('license_plate')->join(', ');
            return $contract;
        });

        // Append query string parameters for pagination
        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];
        $contracts->appends($queryString);

        // Return view with contracts and filters
        return Inertia::render('ProjectContract/Index', [
            'contracts' => $contracts,
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param ProjectContract $projectContract
     * @return Response
     */
    public function show(ProjectContract $projectContract): Response
    {
        $projectContract->load(['customer', 'vehicles', 'invoices']);

        return Inertia::render('ProjectContract/Detail', [
            'contract' => $projectContract->load(['customer']),
            'vehicles' => $projectContract->vehicles(),
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
        return Inertia::render('ProjectContract/Form', [
            'csrf' => csrf_token(),
            'status' => ProjectContract::STATUS_SELECT2,
            'phoneCodes' => $phone_codes,
            'term_of_payments' => ProjectContract::TERM_OF_PAYMENTS,
            'item_types' => RefrigerationSaleHeaderType::TYPE_ARR,
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
            'project_contract_number.required' => 'The Project Contract Number field is required.',
            'project_contract_number.unique' => 'The Project Contract Number has already been taken.',
            'start_duration_date.required' => 'The Contract Start Date field is required.',
            'start_duration_date.date' => 'The Contract Start Date is not a valid date.',
            'end_duration_date.required' => 'The Contract End Date field is required.',
            'end_duration_date.date' => 'The Contract End Date is not a valid date.',
            'value.required' => 'The Value field is required.',
            'value.numeric' => 'The Value must be a number.',
            'term_of_payment.required' => 'The Billing Cycle field is required.',
            'customer_id.required' => 'The Customer field is required.',
            'customer_id.exists' => 'The selected Customer does not exist.',
            'vehicle_id.*.exists' => 'The selected Vehicle does not exist.',
            'pic_email.email' => 'The Email must be a valid email address.',
            'status.required' => 'The Status field is required.',
            'status.boolean' => 'The Status field must be true or false.',

            'pricing.*.name.required' => 'Each pricing item must have a name.',
            'pricing.*.quantity.required' => 'Each pricing item must have a quantity.',
            'pricing.*.quantity.numeric' => 'The quantity must be a numeric value.',
            'pricing.*.subtotal_amount.required' => 'Each pricing item must have a subtotal amount.',
            'pricing.*.total_amount.required' => 'Each pricing item must have a total amount.',

            'vehicle_item_details.*.vehicle_id.required' => 'Each vehicle item detail must have a vehicle.',
            'vehicle_item_details.*.vehicle_id.exists' => 'The selected vehicle does not exist.',
            'vehicle_item_details.*.date_of_install.required' => 'Each vehicle item detail must have a date of installation.',
            'vehicle_item_details.*.date_of_install.date' => 'The date of installation is not a valid date.',
        ];

        // Validation rules
        $request->validate([
            'project_contract_number' => 'nullable|string|unique:project_contracts,project_contract_number',
            'start_duration_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $currentDate = Carbon::now();
                    if (Carbon::parse($value)->lte($currentDate)) {
                        $fail('The contract start date must be a future date.');
                    }
                }
            ],
            'end_duration_date' => 'required|date',
            'value' => 'nullable|numeric',
            'term_of_payment' => 'required|string',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'pic_first_name' => 'nullable|string',
            'pic_last_name' => 'nullable|string',
            'pic_title' => 'nullable|string',
            'pic_country_code' => 'nullable|string',
            'pic_phone_number' => 'nullable|string',
            'pic_email' => 'nullable|email',
            'maintain_projects' => 'nullable|array',
            'maintain_projects.*.date' => 'nullable|date',
            'remark' => 'nullable|string',
            'status' => 'required|integer',

            'pricing' => 'required|array',
            'pricing.*.type' => "required|integer|in:1,2",
            'pricing.*.assembly_id' => 'nullable|exists:assemblies,id',
            'pricing.*.product_id' => 'nullable|exists:products,id',
            'pricing.*.name' => 'required|string',
            'pricing.*.quantity' => 'required|numeric',
            'pricing.*.tax_code' => 'nullable|string',
            'pricing.*.tax_amount' => 'nullable|numeric',
            'pricing.*.discount' => 'nullable|numeric',
            'pricing.*.discount_amount' => 'nullable|numeric',
            'pricing.*.subtotal_amount' => 'required|numeric',
            'pricing.*.total_amount' => 'required|numeric',

            'vehicle_item_details' => 'required|array',
            'vehicle_item_details.*.vehicle_id' => 'required|exists:vehicles,id',
            'vehicle_item_details.*.refrigeration_model' => 'nullable|string',
            'vehicle_item_details.*.date_of_install' => 'required|date',
            'vehicle_item_details.*.standby_unit' => 'nullable|boolean',
        ], $messages);

        $data = $request->all();
        $data['start_duration_date'] = Carbon::parse($data['start_duration_date']);
        $data['end_duration_date'] = Carbon::parse($data['end_duration_date']);
        $data['status'] = ProjectContract::STATUS_PENDING;

        // Additional custom validation
        $validator = Validator::make($data, []);
        $validator->after(function ($validator) use ($data) {
            if (isset($data['maintain_projects']) && is_array($data['maintain_projects']) && count($data['maintain_projects']) > 0) {
                $dates = array_map(function ($project) {
                    return $project['is_active'] ? $project['date'] : null;
                }, $data['maintain_projects']);
                $dates = array_filter($dates);
                $sortedDates = $dates;
                sort($sortedDates);

                if ($dates !== $sortedDates) {
                    $validator->errors()->add('maintain_projects', 'The dates of maintain projects must be in increasing order.');
                }
            }
        });

        // If validation fails, return with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create the project contract
        $projectContract = ProjectContract::create($data);

        // Generate project contract number if it's not provided
        if (!$request->has('project_contract_number') || empty($request->project_contract_number)) {
            $baseNumber = 'CTR' . str_pad($projectContract->id, 4, '0', STR_PAD_LEFT);
            $newNumber = $baseNumber;
            $counter = 1;

            while (ProjectContract::where('project_contract_number', $newNumber)->exists()) {
                $newNumber = $baseNumber . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
                $counter++;
            }

            $projectContract->update(['project_contract_number' => $newNumber]);
        }

        // Attach vehicles to the project contract
        $projectContract->vehicles()->attach($data['vehicle_ids']);

        // Create maintain projects
        if (isset($data['maintain_projects']) && is_array($data['maintain_projects']) && count($data['maintain_projects']) > 0) {
            foreach ($data['maintain_projects'] as $project) {
                foreach ($data['vehicle_ids'] as $vehicle) {
                    $projectContract->maintains()->create([
                        'vehicle_id' => $vehicle,
                        'date' => Carbon::parse($project['date']),
                        'is_active' => $project['is_active'],
                        'status' => ProjectMaintain::STATUS_PENDING,
                        'is_draft' => $projectContract->status == ProjectContract::STATUS_PENDING || $projectContract->status == ProjectContract::STATUS_VOID,
                    ]);
                }
            }
        }

        // Save pricing items
        $pricing_ids = [];
        foreach ($data['pricing'] as $pricingItem) {
            $pricing = $projectContract->pricing()->create($pricingItem);
            $pricing_ids[] = $pricing->id;
        }

        // Save vehicle item details
        if (isset($data['vehicle_item_details']) && is_array($data['vehicle_item_details'])) {
            foreach ($data['vehicle_item_details'] as $itemDetail) {
                ProjectContractVehicleItemDetail::create([
                    'project_contract_pricing_id' => $pricing_ids[$itemDetail['id']],
                    'vehicle_id' => $itemDetail['vehicle_id'],
                    'refrigeration_model' => $itemDetail['refrigeration_model'] ?? null,
                    'date_of_install' => Carbon::parse($itemDetail['date_of_install']),
                    'standby_unit' => $itemDetail['standby_unit'] ?? false,
                ]);
            }
        }

        // Redirect with a success message
        return redirect('/project-contracts')->with('created', 'Project contract created successfully.');
    }

    protected function createInvoices(ProjectContract $projectContract, $termOfPayment)
    {
        // Convert the start and end dates from database format (YYYY-MM-DD) to Carbon instances
        $startDate = \Carbon\Carbon::parse($projectContract->start_duration_date);
        $endDate = \Carbon\Carbon::parse($projectContract->end_duration_date);
        $invoiceCount = 0;
        $dateInterval = null;

        // Determine the date interval based on the term of payment
        switch ($termOfPayment) {
            case 'Monthly':
                $dateInterval = '1 month';
                break;
            case 'Quarterly':
                $dateInterval = '3 months';
                break;
            case 'Half yearly payment':
                $dateInterval = '6 months';
                break;
            case 'Yearly':
                $dateInterval = '1 year';
                break;
            case 'Full Payment':
                // Create a single invoice for the full period
                $invoiceCount = 1;
                $this->createInvoice($projectContract, $invoiceCount, $startDate, $endDate);
                return; // No further invoices are needed for full payment
        }

        // If dateInterval is defined, create multiple invoices
        if ($dateInterval) {
            while ($startDate->lte($endDate)) {  // Use 'lte' to ensure end date is included
                $invoiceCount++;

                // Calculate the end date of the current invoice period
                $nextEndDate = $startDate->copy()->add($dateInterval)->subDay(); // Subtract 1 day to make it inclusive
                if ($nextEndDate->gt($endDate)) {
                    $nextEndDate = $endDate;  // Ensure the last invoice period ends at the correct end date
                }

                // Create invoice for the period
                $this->createInvoice($projectContract, $invoiceCount, $startDate, $nextEndDate);

                // Move to the next period, adding a day to the next start date to avoid overlap
                $startDate = $nextEndDate->copy()->addDay();
            }
        }
    }

    protected function createInvoice(ProjectContract $projectContract, $index, $startDate, $endDate)
    {
        $footer = "Billing period from " . $startDate->format('Y-m-d') . " to " . $endDate->format('Y-m-d');

        // Render PDF (assumes you have a project_contract_invoice blade template)
        $html = view('pdf.project_contract_invoice', [
            'projectContract' => $projectContract->load([
                'vehicles', 'customer.countryPhoneCode', 'customer.country',
                'vehicles', 'pricing.product.uom'
            ]),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'footer' => $footer,
        ])->render();

        // Generate PDF from the rendered HTML
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'landscape');

        // Save the PDF to the storage
        $fileSave = "Contract-Invoice-#{$projectContract->id}-" . time() . ".pdf";
        $filePathSave = "contract_invoices/{$projectContract->id}/$fileSave";
        Storage::disk('public')->put($filePathSave, $pdf->output());

        $fileStore = urlencode($fileSave);
        $filePathStore = "/storage/contract_invoices/{$projectContract->id}/$fileStore";

        // Create the invoice in the database
        ProjectContractInvoice::create([
            'project_contract_id' => $projectContract->id,
            'index' => $index,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'tax_amount' => $projectContract->tax_amount ?? 0,
            'discount_amount' => $projectContract->discount_amount ?? 0,
            'subtotal_amount' => $projectContract->subtotal_amount ?? 0,
            'total_amount' => $projectContract->total_amount ?? 0,
            'footer' => $footer,
            'file_url' => $filePathStore,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ProjectContract $project_contract
     * @return Response|null
     */
    public function edit(ProjectContract $project_contract): ?Response
    {
        if ($project_contract->status != ProjectContract::STATUS_PENDING) {
            return null;
        }

        $c = Country::all()->toArray();
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code');

        $vehicles = $project_contract->vehicles;
        $customers_select = self::toSelect2Data(Customer::whereIn('id', [$project_contract->customer_id])->get()->toArray(), 'id', 'code', 'name', ' | ');
        $vehicles_select = $vehicles->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'text' => $vehicle->license_plate,
                'data' => $vehicle->toArray()
            ];
        });

        $products = $project_contract->pricing->pluck('product')->flatten()->map(function ($item) {
            if (!empty($item)) {
                return [
                    'id' => $item->id,
                    'text' => $item->sku . ' | ' . $item->name,
                    'data' => $item,
                ];
            }
        });

        $assemblies = $project_contract->pricing->pluck('assembly')->flatten()->map(function ($item) {
            if (!empty($item)) {
                return [
                    'id' => $item->id,
                    'text' => $item->code . ' | ' . $item->name,
                    'data' => $item,
                ];
            }
        });

        return Inertia::render('ProjectContract/Form', [
            'csrf' => csrf_token(),
            'status' => ProjectContract::STATUS_SELECT2,
            'contract' => $project_contract->load(['vehicles','maintains', 'pricing.product', 'vehicleItemDetails.pricing.product']),
            'customer' => Customer::find($project_contract->customer_id),
            'customers_select' => $customers_select,
            'vehiclesSelect' => $vehicles,
            'productsSelect' => $products,
            'assembliesSelect' => $assemblies,
            'vehicles_select' => $vehicles_select,
            'phoneCodes' => $phone_codes,
            'term_of_payments' => ProjectContract::TERM_OF_PAYMENTS,
            'item_types' => RefrigerationSaleHeaderType::TYPE_ARR,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ProjectContract $projectContract
     * @return RedirectResponse
     */
    public function update(Request $request, ProjectContract $projectContract)
    {
        // Validation rules with custom messages
        $messages = [
            'project_contract_number.required' => 'The Project Contract Number field is required.',
            'project_contract_number.unique' => 'The Project Contract Number has already been taken.',
            'start_duration_date.required' => 'The Contract Start Date field is required.',
            'start_duration_date.date' => 'The Contract Start Date is not a valid date.',
            'end_duration_date.required' => 'The Contract End Date field is required.',
            'end_duration_date.date' => 'The Contract End Date is not a valid date.',
            'value.required' => 'The Value field is required.',
            'value.numeric' => 'The Value must be a number.',
            'term_of_payment.required' => 'The Billing Cycle field is required.',
            'customer_id.required' => 'The Customer field is required.',
            'customer_id.exists' => 'The selected Customer does not exist.',
            'vehicle_ids.*.exists' => 'The selected Vehicle does not exist.',
            'pic_email.email' => 'The Email must be a valid email address.',
            'status.required' => 'The Status field is required.',
            'status.boolean' => 'The Status field must be true or false.',
            'pricing.*.storage_item_id.required' => 'Each pricing item must have an associated storage item.',
            'pricing.*.name.required' => 'Each pricing item must have a name.',
            'pricing.*.quantity.required' => 'Each pricing item must have a quantity.',
            'pricing.*.quantity.numeric' => 'The quantity must be a numeric value.',
            'pricing.*.subtotal_amount.required' => 'Each pricing item must have a subtotal amount.',
            'pricing.*.total_amount.required' => 'Each pricing item must have a total amount.',
            'vehicle_item_details.*.vehicle_id.required' => 'Each vehicle item detail must have a vehicle.',
            'vehicle_item_details.*.vehicle_id.exists' => 'The selected vehicle does not exist.',
            'vehicle_item_details.*.date_of_install.required' => 'Each vehicle item detail must have a date of installation.',
            'vehicle_item_details.*.date_of_install.date' => 'The date of installation is not a valid date.',
        ];

        // Validation rules
        $request->validate([
            'project_contract_number' => 'nullable|string|unique:project_contracts,project_contract_number,' . $projectContract->id,
            'start_duration_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $currentDate = Carbon::now();
                    if (Carbon::parse($value)->lte($currentDate)) {
                        $fail('The contract start date must be a future date.');
                    }
                }
            ],
            'end_duration_date' => 'required|date',
            'value' => 'nullable|numeric',
            'term_of_payment' => 'required|string',
            'customer_id' => 'required|exists:customers,id',
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'pic_first_name' => 'nullable|string',
            'pic_last_name' => 'nullable|string',
            'pic_title' => 'nullable|string',
            'pic_country_code' => 'nullable|string',
            'pic_phone_number' => 'nullable|string',
            'pic_email' => 'nullable|email',
            'maintain_projects' => 'nullable|array',
            'maintain_projects.*.date' => 'nullable|date',
            'remark' => 'nullable|string',
            'status' => 'required|integer',
            'pricing' => 'required|array',
            'pricing.*.type' => "required|integer|in:1,2",
            'pricing.*.assembly_id' => 'nullable|exists:assemblies,id',
            'pricing.*.product_id' => 'nullable|exists:products,id',
            'pricing.*.name' => 'required|string',
            'pricing.*.quantity' => 'required|numeric',
            'pricing.*.tax_code' => 'nullable|string',
            'pricing.*.tax_amount' => 'nullable|numeric',
            'pricing.*.discount' => 'nullable|numeric',
            'pricing.*.discount_amount' => 'nullable|numeric',
            'pricing.*.subtotal_amount' => 'required|numeric',
            'pricing.*.total_amount' => 'required|numeric',
            'vehicle_item_details' => 'required|array',
            'vehicle_item_details.*.vehicle_id' => 'required|exists:vehicles,id',
            'vehicle_item_details.*.refrigeration_model' => 'nullable|string',
            'vehicle_item_details.*.date_of_install' => 'required|date',
            'vehicle_item_details.*.standby_unit' => 'nullable|boolean',
        ], $messages);

        $data = $request->all();
        $data['start_duration_date'] = Carbon::parse($data['start_duration_date']);
        $data['end_duration_date'] = Carbon::parse($data['end_duration_date']);

        // Additional custom validation
        $validator = Validator::make($data, []);
        $validator->after(function ($validator) use ($data) {
            if (isset($data['maintain_projects']) && is_array($data['maintain_projects']) && count($data['maintain_projects']) > 0) {
                $dates = array_map(function ($project) {
                    return $project['is_active'] ? $project['date'] : null;
                }, $data['maintain_projects']);
                $dates = array_filter($dates);
                $sortedDates = $dates;
                sort($sortedDates);

                if ($dates !== $sortedDates) {
                    $validator->errors()->add('maintain_projects', 'The dates of maintain projects must be in increasing order.');
                }
            }
        });

        // If validation fails, return with errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Update the project contract
        $projectContract->update($data);

        // Sync vehicles with the project contract
        $projectContract->vehicles()->sync($data['vehicle_ids']);

        // Update maintain projects
        if (isset($data['maintain_projects'])) {
            foreach ($data['maintain_projects'] as $project) {
                $projectMaintain = ProjectMaintain::find($project['id']);
                if ($projectMaintain) {
                    $projectMaintain->update([
                        'date' => Carbon::parse($project['date']),
                        'is_active' => $project['is_active'],
                        'is_draft' => $projectContract->status == ProjectContract::STATUS_PENDING || $projectContract->status == ProjectContract::STATUS_VOID,
                    ]);
                } else {
                    $projectContract->maintains()->create([
                        'date' => Carbon::parse($project['date']),
                        'is_active' => $project['is_active'],
                        'status' => ProjectMaintain::STATUS_PENDING,
                        'is_draft' => $projectContract->status == ProjectContract::STATUS_PENDING || $projectContract->status == ProjectContract::STATUS_VOID,
                    ]);
                }
            }
        }

        // Update pricing items
        $projectContract->pricing()->delete();
        foreach ($data['pricing'] as $pricingItem) {
            $projectContract->pricing()->create($pricingItem);
        }

        // Update vehicle item details
        ProjectContractVehicleItemDetail::where('project_contract_pricing_id', $projectContract->id)->delete();
        foreach ($data['vehicle_item_details'] as $itemDetail) {
            ProjectContractVehicleItemDetail::create([
                'project_contract_pricing_id' => $projectContract->id,
                'vehicle_id' => $itemDetail['vehicle_id'],
                'refrigeration_model' => $itemDetail['refrigeration_model'] ?? null,
                'date_of_install' => Carbon::parse($itemDetail['date_of_install']),
                'standby_unit' => $itemDetail['standby_unit'] ?? false,
            ]);
        }

        // Redirect with a success message
        return redirect('/project-contracts')->with('updated', 'Project contract updated successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ProjectContract $project_contract
     * @return RedirectResponse
     */
    public function updatePhoto(Request $request, ProjectContract $project_contract)
    {
        // Validate the uploaded image
        $request->validate([
            'signed_image_url' => 'required|image|mimes:png,jpg,jpeg',
        ]);

        // Check if the file has been uploaded
        if ($request->hasFile('signed_image_url')) {
            // Store the file in the public storage
            $path = $request->file('signed_image_url')->store("project_contracts/{$project_contract->id}", 'public');

            // Update the project contract with the new image URL and set status to Accepted
            $project_contract->update([
                'signed_image_url' => $path,
                'status' => ProjectContract::STATUS_ACCEPTED,
            ]);

            // Load the vehicles associated with the project contract
            $project_contract->load('maintains', 'vehicles');

            // Create new ProjectMaintain entries for each vehicle
            foreach ($project_contract->vehicles as $vehicle) {
                foreach ($project_contract->maintains as $maintain) {
                    $project_contract->maintains()->create([
                        'vehicle_id' => $vehicle->id,
                        'date' => $maintain['date'],
                        'is_active' => $maintain['is_active'],
                        'status' => $maintain['status'],
                    ]);
                }
            }

            // Delete existing ProjectMaintain entries
            $project_contract->maintains()->whereNull('vehicle_id')->delete();

            // Call the function to create invoices based on the billing cycle
            $this->createInvoices($project_contract, $project_contract->term_of_payment);
        }

        // Return back with success message
        return back()->with('updated', "Project contract updated successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ProjectContract $project_contract
     * @return RedirectResponse
     */
    public function void(Request $request, ProjectContract $project_contract): RedirectResponse
    {
        $project_contract->update(['status' => ProjectContract::STATUS_VOID]);

        return back()->with('updated', "Project contract updated successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param ProjectContract $project_contract
     * @return RedirectResponse
     */
    public function earlyTermination(Request $request, ProjectContract $project_contract): RedirectResponse
    {
        // Validate the uploaded image and the end_duration_date
        $request->validate([
            'file_url' => 'required|file',
            'end_duration_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($project_contract) {
                    $currentEndDate = Carbon::parse($project_contract->end_duration_date);
                    $newEndDate = Carbon::parse($value);
                    $minEndDate = Carbon::now()->subDays(15);

                    if ($newEndDate >= $currentEndDate) {
                        $fail('The new end duration date must be earlier than the current end duration date.');
                    }

                    if ($newEndDate < $minEndDate) {
                        $fail('The end duration date must be at least 15 days from today.');
                    }
                },
            ],
        ]);

        // Check if the file has been uploaded
        if ($request->hasFile('file_url')) {
            // Store the file in the public storage
            $path = $request->file('file_url')->store("project_contracts/{$project_contract->id}", 'public');

            // Update the project contract with the new image URL, status, and end_duration_date
            $project_contract->update([
                'file_url' => $path,
                'status' => ProjectContract::STATUS_EARLY_TERMINATION,
                'end_duration_date' => Carbon::parse($request->input('end_duration_date')),
            ]);
        }

        return back()->with('updated', "Project contract updated successfully");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProjectContract $project_contract
     */
    public function downloadPdf(ProjectContract $project_contract)
    {
        // Load related customer and vehicles
        $project_contract->load(['customer']);
        $vehicles = $project_contract->vehicles()->get();

        // Render the Blade view as HTML
        $html = view('pdf.project_contract', [
            'contract' => $project_contract,
            'vehicles' => $vehicles,
        ])->render();

        // Generate PDF from the rendered HTML
        $pdf = PDF::loadHTML($html);

        return $pdf->download("project_contract#$project_contract->id.pdf");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param ProjectContract $project_contract
     * @return RedirectResponse
     */
    public function destroy(ProjectContract $project_contract): RedirectResponse
    {
        // Delete the contract record
        $project_contract->delete();

        // Redirect back with success message
        return redirect('/project-contracts')->with('deleted', 'Project contract deleted successfully');
    }
}
