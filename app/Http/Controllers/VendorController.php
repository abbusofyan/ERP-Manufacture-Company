<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\VendorAddress;
use App\Models\VendorPoc;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Support\Collection;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Vendor::class, 'vendor');
    }

    public function select2Query(Request $request)
    {
        $productVendors = Vendor::with('productPrices')
            ->whereHas('productPrices', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            })
            ->when($request->has('search'), function ($query) use ($request) {
                $query->where('code', 'LIKE', "%{$request->search}%")
                      ->orWhere('name', 'LIKE', "%{$request->search}%");
            })
            ->limit(10)
            ->get();

        $otherVendors = Vendor::when($request->has('search'), function ($query) use ($request) {
                $query->where('code', 'LIKE', "%{$request->search}%")
                      ->orWhere('name', 'LIKE', "%{$request->search}%");
            })
            ->when($request->has('product_id'), function ($query) use ($request) {
                $query->whereDoesntHave('productPrices', function ($query) use ($request) {
                    $query->where('product_id', $request->product_id);
                });
            })
            ->limit(20 - $productVendors->count())
            ->get();
            $productVendors = $productVendors->merge($otherVendors);
            return $productVendors;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
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

        $vendors = Vendor::when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%");
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

        $vendors->appends($queryString);

        return Inertia::render('Vendor/Index', [
            'vendors' => $vendors,
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
        $c = Country::all()->toArray();
        $countries = self::toSelect2Data($c, 'id', 'name');
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code', 'name', ' | ');

        return Inertia::render('Vendor/Form', [
            'csrf' => csrf_token(),
            'countries' => $countries,
            'phoneCodes' => $phone_codes,
            'managers' => Customer::MANAGER_ARRAY,
            'currencies' => Customer::CURRENCIES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'code' => 'required|unique:vendors',
            'name' => 'required',
            'country_phone_code_id' => 'nullable|required_with:phone',
            'phone' => 'nullable',
            'uen' => 'required',
            'account_manager' => 'required',
            'remark' => 'nullable',
            'address_location' => 'required',
            'address_unit_no' => 'nullable',
            'address_building_name' => 'required',
            'address_postal_code' => 'required',
            'address_office_number' => 'nullable',
            'poc_first_name' => 'required',
            'poc_last_name' => 'nullable',
            'poc_email' => 'required|email',
            'poc_country_phone_code_id' => 'nullable|required_with:poc_phone',
            'poc_phone' => 'nullable',
            'poc_title' => 'nullable',
            'currency' => 'required|string|in:SGD,USD',
        ]);

        $data = $request->all();
        $vendor = Vendor::create($data);

        /** Create POC **/
        VendorPoc::create([
            'vendor_id' => $vendor->id,
            'first_name' => $data['poc_first_name'],
            'last_name' => $data['poc_last_name'],
            'country_phone_code_id' => $data['poc_country_phone_code_id'],
            'phone' => $data['poc_phone'],
            'email' => $data['poc_email'],
            'title' => $data['poc_title'],
            'currency' => $data['currency'],
        ]);

        /** Create Address **/
        VendorAddress::create([
            'vendor_id' => $vendor->id,
            'address' => $data['address_location'],
            'unit_no' => $data['address_unit_no'],
            'building_name' => $data['address_building_name'],
            'postal_code' => $data['address_postal_code'],
            'phone' => $data['address_office_number'],
        ]);

        return redirect('/vendors')->with('created', 'Vendor created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Vendor $vendor
     * @return Response
     */
    public function edit(Vendor $vendor): Response
    {
        $c = Country::all()->toArray();
        $countries = self::toSelect2Data($c, 'id', 'name');
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code', 'name', ' | ');

        return Inertia::render('Vendor/Form', [
            'vendor' => $vendor,
            'countries' => $countries,
            'phoneCodes' => $phone_codes,
            'managers' => Customer::MANAGER_ARRAY,
            'credit_term' => Customer::CREDIT_TERM_ARRAY,
            'currencies' => Customer::CURRENCIES,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Request $request, Vendor $vendor): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'code' => 'required|unique:vendors,code,' . $vendor->id,
            'name' => 'required',
            'country_phone_code_id' => 'nullable|required_with:phone',
            'phone' => 'nullable',
            'uen' => 'required',
            'account_manager' => 'required',
            'remark' => 'nullable',
            'credit_term' => 'required',
            'credit_limit' => 'nullable',
            'currency' => 'required|string|in:SGD,USD',
        ]);

        $data = $request->all();

        $vendor->update($data);

        return redirect('/vendors')->with('updated', 'Vendor updated successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return Response
     */
    public function show(Request $request, Vendor $vendor)
    {
        $vendor->load('countryPhoneCode');

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

        $pocs = VendorPoc::where('vendor_id', $vendor->id)
            ->with('countryPhoneCode')
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $pocs->appends($queryString);

        $c = Country::all()->toArray();
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code');

        return Inertia::render('Vendor/POC/View', [
            'vendor' => $vendor,
            'pocs' => $pocs,
            'filters' => $filters,
            'phoneCodes' => $phone_codes,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Vendor $vendor
     * @return RedirectResponse
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();

        return redirect('/vendors')->with('deleted', 'Vendor deleted successfully');
    }
}
