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

class PocController extends Controller
{
    public function __construct()
    {
//        $this->authorizeResource(Customer::class, 'customer');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @param Customer $customer
     * @return Response
     */
    public function create(Customer $customer): Response
    {
        $this->authorize('create', Customer::class);
        $c = Country::all()->toArray();
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code', 'name', ' | ');

        return Inertia::render('Customer/POC/Form', [
            'customer' => $customer,
            'phoneCodes' => $phone_codes,
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
        $this->authorize('create', Customer::class);
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'required|email',
            'country_phone_code_id' => 'nullable|required_with:phone',
            'phone' => 'nullable',
            'title' => 'nullable',
        ]);

        $data = $request->all();
        $data['customer_id'] = $customer->id;

        if ($request->has('resign')) {
            $data['status'] = Poc::STATUS_RESIGN;
        }

        if($data['is_default']){
            $customer->pocs()->update(['is_default' => false]);
        }

        Poc::create($data);

        return redirect("/customers/$customer->id")->with('created', 'POC created successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Customer $customer
     * @param Poc $poc
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function markApprove(Customer $customer, Poc $poc): RedirectResponse
    {
        $this->authorize('update', Customer::class);

        $customer->pocs()->update(['is_default' => false]);
        $poc->update(['is_default' => true, 'status' => Poc::STATUS_APPROVE]);

        return back()->with('updated', 'POC updated successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Customer $customer
     * @param Poc $poc
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Customer $customer, Poc $poc): Response
    {
        $this->authorize('update', Customer::class);
        $c = Country::all()->toArray();
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code', 'name', ' | ');

        return Inertia::render('Customer/POC/Form', [
            'poc' => $poc,
            'customer' => $customer,
            'phoneCodes' => $phone_codes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Customer $customer
     * @param Poc $poc
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Customer $customer, Poc $poc, Request $request)
    {
        $this->authorize('update', Customer::class);
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'required|email',
            'country_phone_code_id' => 'nullable|required_with:phone',
            'phone' => 'nullable',
            'title' => 'nullable',
        ]);

        $data = $request->all();

        if ($request->has('resign')) {
            $data['status'] = Poc::STATUS_RESIGN;
        }

        if($data['is_default']){
            $customer->pocs()->update(['is_default' => false]);
        }

        $poc->update($data);

        return redirect("/customers/$customer->id")->with('updated', 'POC updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Customer $customer
     * @param Poc $poc
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Customer $customer, Poc $poc)
    {
        $this->authorize('delete', Customer::class);
        $poc->delete();

        return back()->with('deleted', 'POC deleted successfully');
    }
}
