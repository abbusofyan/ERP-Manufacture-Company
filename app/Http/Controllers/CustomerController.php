<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Customer;
use App\Models\Poc;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;

class CustomerController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Customer::class, 'customer');
    }

    public function select2Query(Request $request)
    {
        return Customer::when($request->has('search'), function ($query) use ($request) {
            $query->where('code', 'LIKE', "%{$request->search}%")
                ->orWhere('name', 'LIKE', "%{$request->search}%");
        })->with(['pocs', 'addresses', 'defaultPoc'])->limit(10)->get(['id', 'code', 'name']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $order = $request->get('order', 'id');
        $by = $request->get('by', 'desc');
        $paginate = $request->get('paginate', 10);
        $deleted = $request->get('deleted', false);

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $customers = Customer::when($search, function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%");
        })
            ->when($deleted, function ($query) use ($deleted) {
                $query->onlyTrashed();
            })
            ->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
            'deleted' => $deleted,
        ];

        $customers->appends($queryString);

        return Inertia::render('Customer/Index', [
            'customers' => $customers,
            'filters' => $filters,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Customer $customer
     * @return RedirectResponse
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return back()->with('deleted', 'Customer deleted successfully');
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param $id
     * @return RedirectResponse
     */
    public function restore($id)
    {
        $customer = Customer::withTrashed()->find($id);
        $customer->restore();

        return back()->with('updated', 'Customer restored successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Customer $customer
     * @return Response
     */
    public function show(Request $request, Customer $customer)
    {
        $customer->load('countryPhoneCode');

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

        $pocs = Poc::where('customer_id', $customer->id)
            ->with(['countryPhoneCode','countryFaxCode'])
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

        return Inertia::render('Customer/POC/View', [
            'customer' => $customer,
            'pocs' => $pocs,
            'filters' => $filters,
            'phoneCodes' => $phone_codes,
        ]);
    }
}
