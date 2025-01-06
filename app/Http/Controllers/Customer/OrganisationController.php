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

class OrganisationController extends Controller
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

        return Inertia::render('Customer/Organisation/Form', [
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
            'country_phone_code_id' => 'nullable|required_with:phone',
            'phone' => 'nullable',
            'uen' => 'required',
            'account_manager' => 'required',
            'customer_type' => 'required',
            'service' => 'required',
            'remark' => 'nullable',
            'refrigeration_sales' => 'required',
            'project' => 'required',
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
        ]);

        $data = $request->all();
        $data['info_type'] = Customer::TYPE_ORGANISATION;
        $customer = Customer::create($data);

        $customer->update(['code' => 'CO' . str_pad($customer->id, 4, '0', STR_PAD_LEFT)]);

        /** Create POC **/
        Poc::create([
            'customer_id' => $customer->id,
            'first_name' => $data['poc_first_name'],
            'last_name' => $data['poc_last_name'],
            'country_phone_code_id' => $data['poc_country_phone_code_id'],
            'phone' => $data['poc_phone'],
            'email' => $data['poc_email'],
            'title' => $data['poc_title'],
        ]);

        /** Create Address **/
        CustomerAddress::create([
            'customer_id' => $customer->id,
            'address' => $data['address_location'],
            'unit_no' => $data['address_unit_no'],
            'building_name' => $data['address_building_name'],
            'postal_code' => $data['address_postal_code'],
            'phone' => $data['address_office_number'],
        ]);

        return redirect('/customers')->with('created', 'Customer created successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Customer $organisation
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Customer $organisation)
    {
        $this->authorize('update', Customer::class);
        $c = Country::all()->toArray();
        $countries = self::toSelect2Data($c, 'id', 'name');
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code', 'name', ' | ');
        return Inertia::render('Customer/Organisation/Form', [
            'csrf' => csrf_token(),
            'customer' => $organisation,
            'countries' => $countries,
            'phoneCodes' => $phone_codes,
            'managers' => Customer::MANAGER_ARRAY,
            'customerTypes' => Customer::CUSTOMER_TYPE_ARRAY,
            'services' => Customer::SERVICE_ARRAY,
            'refrigerationSales' => Customer::REFRIGERATION_SALES_ARRAY,
            'projects' => Customer::PROJECT_ARRAY,
            'credit_term' => Customer::CREDIT_TERM_ARRAY,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Customer $organisation
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Request $request, Customer $organisation): Redirector|RedirectResponse|Application
    {
        $this->authorize('update', Customer::class);
        $request->validate([
            'name' => 'required',
            'country_phone_code_id' => 'nullable|required_with:phone',
            'phone' => 'nullable',
            'uen' => 'required',
            'account_manager' => 'required',
            'customer_type' => 'required',
            'service' => 'required',
            'remark' => 'nullable',
            'refrigeration_sales' => 'required',
            'project' => 'required',
            'credit_term' => 'required',
            'credit_limit' => 'required',
        ]);

        $data = $request->all();

        $organisation->update($data);

        return redirect('/customers')->with('updated', 'Customer updated successfully');
    }
}
