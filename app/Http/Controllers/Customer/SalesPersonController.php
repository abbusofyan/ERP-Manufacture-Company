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

class SalesPersonController extends Controller
{
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

        return Inertia::render('Customer/SalesPerson/Form', [
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
     * @throws AuthorizationException
     */
    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $this->authorize('create', Customer::class);
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
        $data['info_type'] = Customer::TYPE_SALES_PERSON;
        $customer = Customer::create($data);

        $customer->update(['code' => 'CS' . str_pad($customer->id, 4, '0', STR_PAD_LEFT)]);

        return redirect('/customers')->with('created', 'Customer created successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Customer $salesperson
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Customer $salesperson): Response
    {
        $this->authorize('create', Customer::class);
        $c = Country::all()->toArray();
        $countries = self::toSelect2Data($c, 'id', 'name');
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code', 'name', ' | ');

        return Inertia::render('Customer/SalesPerson/Form', [
            'csrf' => csrf_token(),
            'customer' => $salesperson,
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
     * @param Customer $salesperson
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Customer $salesperson): Redirector|RedirectResponse|Application
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

        $salesperson->update($data);

        return redirect('/customers')->with('updated', 'Customer updated successfully');
    }
}
