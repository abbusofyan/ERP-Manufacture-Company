<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Poc;
use App\Models\Vendor;
use App\Models\VendorPoc;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class VendorPocController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Vendor::class, 'vendor');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Vendor $vendor
     * @return Response
     */
    public function create(Vendor $vendor): Response
    {
        $c = Country::all()->toArray();
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code', 'name', ' | ');

        return Inertia::render('Vendor/POC/Form', [
            'vendor' => $vendor,
            'phoneCodes' => $phone_codes,
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
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'required|email',
            'country_phone_code_id' => 'nullable|required_with:phone',
            'phone' => 'nullable',
            'title' => 'nullable',
        ]);

        $data = $request->all();
        $data['vendor_id'] = $vendor->id;

        if ($request->has('resign')) {
            $data['status'] = VendorPoc::STATUS_RESIGN;
        }

        VendorPoc::create($data);

        return redirect("/vendors/$vendor->id")->with('created', 'POC created successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Vendor $vendor
     * @param VendorPoc $poc
     * @return RedirectResponse
     */
    public function markApprove(Vendor $vendor, VendorPoc $poc): RedirectResponse
    {
        $this->authorize('update-vendor');
        $poc->update(['status' => VendorPoc::STATUS_APPROVE]);
        return back()->with('updated', 'POC updated successfully');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Vendor $vendor
     * @param Poc $poc
     * @return Response
     */
    public function edit(Vendor $vendor, VendorPoc $poc): Response
    {
        $c = Country::all()->toArray();
        $phone_codes = self::toSelect2Data($c, 'id', 'phone_code', 'name', ' | ');

        return Inertia::render('Vendor/POC/Form', [
            'poc' => $poc,
            'vendor' => $vendor,
            'phoneCodes' => $phone_codes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Vendor $vendor
     * @param VendorPoc $poc
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Vendor $vendor, VendorPoc $poc, Request $request): Redirector|RedirectResponse|Application
    {
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

        $poc->update($data);

        return redirect("/vendors/$vendor->id")->with('updated', 'POC updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Vendor $vendor
     * @param VendorPoc $poc
     * @return RedirectResponse
     */
    public function destroy(Vendor $vendor, VendorPoc $poc)
    {
        $poc->delete();

        return back()->with('deleted', 'POC deleted successfully');
    }
}
