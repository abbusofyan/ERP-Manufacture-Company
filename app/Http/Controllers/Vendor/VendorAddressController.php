<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorAddress;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class VendorAddressController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Vendor::class, 'vendor');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return Response
     */
    public function index(Request $request, Vendor $vendor): Response
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

        $addresses = VendorAddress::when($search, function ($query) use ($search) {
            $query->where('postal_code', 'LIKE', "%{$search}%")
                ->orWhere('address', 'LIKE', "%{$search}%")
                ->orWhere('building_name', 'LIKE', "%{$search}%")
                ->orWhere('unit_no', 'LIKE', "%{$search}%");
        })->where('vendor_id', $vendor->id)->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $addresses->appends($queryString);

        return Inertia::render('Vendor/Address/View', [
            'addresses' => $addresses,
            'filters' => $filters,
            'vendor' => $vendor,
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Vendor $vendor
     * @param VendorAddress $address
     * @return RedirectResponse
     */
    public function markApprove(Vendor $vendor, VendorAddress $address): RedirectResponse
    {
        $this->authorize('update-vendor');
        $address->update(['status' => VendorAddress::STATUS_APPROVE]);
        return back()->with('updated', 'Address updated successfully');
    }


    /**
     * Duplicate the specified resource in storage.
     *
     * @param Vendor $vendor
     * @param VendorAddress $address
     * @return RedirectResponse
     */
    public function duplicate(Vendor $vendor, VendorAddress $address): RedirectResponse
    {
        $this->authorize('create-vendor');
        $new = $address->replicate();
        $new->save();
        return back()->with('created', 'Address duplicated successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Vendor $vendor
     * @return Response
     */
    public function create(Vendor $vendor): Response
    {
        return Inertia::render('Vendor/Address/Form', [
            'vendor' => $vendor,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Vendor $vendor
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function store(Vendor $vendor, Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'address' => 'required',
            'building_name' => 'nullable',
            'postal_code' => 'required',
            'unit_no' => 'nullable',
        ]);

        $data = $request->all();
        $data['vendor_id'] = $vendor->id;

        VendorAddress::create($data);

        return redirect("/vendors/$vendor->id/addresses")->with('created', 'Address created successfully');
    }

    /**
     * Show the form for editing a new resource.
     *
     * @param Vendor $vendor
     * @param VendorAddress $address
     * @return Response
     */
    public function edit(Vendor $vendor, VendorAddress $address): Response
    {
        return Inertia::render('Vendor/Address/Form', [
            'address' => $address,
            'vendor' => $vendor,
        ]);
    }

    /**
     * Update a resource in storage.
     *
     * @param Vendor $vendor
     * @param VendorAddress $address
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Vendor $vendor, VendorAddress $address, Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'address' => 'required',
            'building_name' => 'nullable',
            'postal_code' => 'required',
            'unit_no' => 'nullable',
        ]);

        $data = $request->all();

        $address->update($data);

        return redirect("/vendors/$vendor->id/addresses")->with('updated', 'Address updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Vendor $vendor
     * @param VendorAddress $address
     * @return RedirectResponse
     */
    public function destroy(Vendor $vendor, VendorAddress $address)
    {
        $address->delete();

        return back()->with('deleted', 'POC deleted successfully');
    }
}
