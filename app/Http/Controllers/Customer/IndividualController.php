<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Customer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class IndividualController extends Controller
{
    public function __construct()
    {
//        $this->authorizeResource(Customer::class, 'customer');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        $this->authorize('create', Customer::class);
        $c = Country::all()->toArray();
        $countries = self::toSelect2Data($c, 'id', 'name');
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code', 'name', ' | ');

        return Inertia::render('Customer/Individual/Form', [
            'csrf' => csrf_token(),
            'countries' => $countries,
            'phoneCodes' => $phone_codes,
            'managers' => Customer::MANAGER_ARRAY,
            'customerTypes' => Customer::CUSTOMER_TYPE_ARRAY,
            'services' => Customer::SERVICE_ARRAY,
            'refrigerationSales' => Customer::REFRIGERATION_SALES_ARRAY,
            'projects' => Customer::PROJECT_ARRAY,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'name' => 'required',
            'poc_email' => 'required|email',
            'country_phone_code_id' => 'required',
            'phone' => 'required',
            'account_manager' => 'required',
            'service' => 'required',
            'refrigeration_sales' => 'required',
            'project' => 'required',
        ]);

        $data = $request->all();
        $data['info_type'] = Customer::TYPE_INDIVIDUAL;
        $customer = Customer::create($data);

        $customer->update(['code' => 'CI' . str_pad($customer->id, 4, '0', STR_PAD_LEFT)]);

        return redirect('/customers')->with('created', 'Customer created successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Customer $individual
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Customer $individual): Response
    {
        $this->authorize('create', Customer::class);
        $c = Country::all()->toArray();
        $countries = self::toSelect2Data($c, 'id', 'name');
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code', 'name', ' | ');

        return Inertia::render('Customer/Individual/Form', [
            'csrf' => csrf_token(),
            'customer' => $individual,
            'countries' => $countries,
            'phoneCodes' => $phone_codes,
            'managers' => Customer::MANAGER_ARRAY,
            'customerTypes' => Customer::CUSTOMER_TYPE_ARRAY,
            'services' => Customer::SERVICE_ARRAY,
            'refrigerationSales' => Customer::REFRIGERATION_SALES_ARRAY,
            'projects' => Customer::PROJECT_ARRAY,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Customer $individual
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Customer $individual): Redirector|RedirectResponse|Application
    {
        $this->authorize('update', Customer::class);
        $request->validate([
            'name' => 'required',
            'poc_email' => 'required|email',
            'country_phone_code_id' => 'required',
            'phone' => 'required',
            'account_manager' => 'required',
            'service' => 'required',
            'refrigeration_sales' => 'required',
            'project' => 'required',
        ]);

        $data = $request->all();

        $individual->update($data);

        return redirect('/customers')->with('updated', 'Customer updated successfully');
    }
}
