<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Poc;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Customer $customer
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Request $request, Customer $customer): Response
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

        $addresses = CustomerAddress::when($search, function ($query) use ($search) {
            $query->where('postal_code', 'LIKE', "%{$search}%")
                ->orWhere('address', 'LIKE', "%{$search}%")
                ->orWhere('building_name', 'LIKE', "%{$search}%")
                ->orWhere('unit_no', 'LIKE', "%{$search}%");
        })->where('customer_id', $customer->id)->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $addresses->appends($queryString);

        return Inertia::render('Customer/Address/View', [
            'addresses' => $addresses,
            'filters' => $filters,
            'customer' => $customer,
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Customer $customer
     * @param CustomerAddress $address
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function markApprove(Customer $customer, CustomerAddress $address): RedirectResponse
    {
        $this->authorize('update', Customer::class);

        $customer->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true, 'status' => CustomerAddress::STATUS_APPROVE]);

        return back()->with('updated', 'Address updated successfully');
    }


    /**
     * Duplicate the specified resource in storage.
     *
     * @param Customer $customer
     * @param CustomerAddress $address
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function duplicate(Customer $customer, CustomerAddress $address): RedirectResponse
    {
        $this->authorize('update', Customer::class);
        $new = $address->replicate();
        $new->is_default = false;
        $new->save();
        return back()->with('created', 'Address duplicated successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Customer $customer
     * @return Response
     * @throws AuthorizationException
     */
    public function create(Customer $customer): Response
    {
        $this->authorize('create', Customer::class);
        return Inertia::render('Customer/Address/Form', [
            'customer' => $customer,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Customer $customer
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Customer $customer, Request $request): Redirector|RedirectResponse|Application
    {
        $this->authorize('update', Customer::class);
        $request->validate([
            'block' => 'required',
            'street_name' => 'required',
            'unit_level' => 'required',
            'unit_number' => 'required',
            'city' => 'required',
            'region' => 'required',
            'country' => 'required',
            'postal_code' => 'required',
        ]);

        $data = $request->all();
        $data['customer_id'] = $customer->id;
        $data['status'] = CustomerAddress::STATUS_APPROVE;

        if ($data['is_default']) {
            $customer->addresses()->update(['is_default' => false]);
        }

        CustomerAddress::create($data);

        return redirect("/customers/$customer->id/addresses")->with('created', 'Address created successfully');
    }

    /**
     * Show the form for editing a new resource.
     *
     * @param Customer $customer
     * @param CustomerAddress $address
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Customer $customer, CustomerAddress $address): Response
    {
        $this->authorize('update', Customer::class);
        return Inertia::render('Customer/Address/Form', [
            'address' => $address,
            'customer' => $customer,
        ]);
    }

    /**
     * Update a resource in storage.
     *
     * @param Customer $customer
     * @param CustomerAddress $address
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Customer $customer, CustomerAddress $address, Request $request): Redirector|Application|RedirectResponse
    {
        $this->authorize('update', Customer::class);
        $request->validate([
            'block' => 'required',
            'street_name' => 'required',
            'unit_level' => 'required',
            'unit_number' => 'required',
            'city' => 'required',
            'region' => 'required',
            'country' => 'required',
            'postal_code' => 'required',
        ]);

        $data = $request->all();

        if (isset($data['is_default']) && $data['is_default']) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return redirect("/customers/$customer->id/addresses")->with('updated', 'Address updated successfully');
    }


    /**
     * Switch status the specified resource from storage.
     *
     * @param Customer $customer
     * @param CustomerAddress $address
     * @return RedirectResponse
     */
    public function switch(Customer $customer, CustomerAddress $address)
    {
        if ($address->status == CustomerAddress::STATUS_APPROVE) {
            $address->update(['status' => CustomerAddress::STATUS_NONE]);
        } else {
            $address->update(['status' => CustomerAddress::STATUS_APPROVE]);
        }

        return back()->with('created', 'Address update successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Customer $customer
     * @param CustomerAddress $address
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Customer $customer, CustomerAddress $address)
    {
        $this->authorize('delete', Customer::class);
        $address->delete();

        return back()->with('deleted', 'Address deleted successfully');
    }
}
