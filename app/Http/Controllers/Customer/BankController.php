<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerBank;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class BankController extends Controller
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

        $order = $request->order ?? 'id';
        $by = $request->by ?? 'desc';
        $paginate = $request->paginate ?? 10;

        $filters = $request->only(['search']);
        $filters['order'] = $order;
        $filters['by'] = $by;
        $filters['paginate'] = $paginate;

        $banks = CustomerBank::when($search, function ($query) use ($search) {
            $query->where('bank_name', 'LIKE', "%{$search}%")
                ->orWhere('bank_code', 'LIKE', "%{$search}%")
                ->orWhere('branch_code', 'LIKE', "%{$search}%")
                ->orWhere('account_number', 'LIKE', "%{$search}%")
                ->orWhere('swift_code', 'LIKE', "%{$search}%");
        })->where('customer_id', $customer->id)->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $banks->appends($queryString);

        return Inertia::render('Customer/Bank/View', [
            'banks' => $banks,
            'filters' => $filters,
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Customer $customer
     * @param CustomerBank $bank
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function markApprove(Customer $customer, CustomerBank $bank): RedirectResponse
    {
        $this->authorize('update', Customer::class);

        $customer->banks()->update(['is_default' => false]);
        $bank->update(['is_default' => true, 'status' => CustomerBank::STATUS_APPROVE]);

        return back()->with('updated', 'Bank updated successfully');
    }

    /**
     * Duplicate the specified resource in storage.
     *
     * @param Customer $customer
     * @param CustomerBank $bank
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function duplicate(Customer $customer, CustomerBank $bank): RedirectResponse
    {
        $this->authorize('update', Customer::class);
        $new = $bank->replicate();
        $new->is_default = false;
        $new->save();
        return back()->with('created', 'Bank duplicated successfully');
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
        return Inertia::render('Customer/Bank/Form', [
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
            'bank_name' => 'required',
            'bank_code' => 'required',
            'branch_code' => 'required',
            'account_number' => 'required',
            'swift_code' => 'required',
            'is_default' => 'boolean',
        ]);

        $data = $request->all();
        $data['customer_id'] = $customer->id;
        $data['status'] = CustomerBank::STATUS_APPROVE;

        if ($data['is_default']) {
            $customer->banks()->update(['is_default' => false]);
        }

        CustomerBank::create($data);

        return redirect("/customers/$customer->id/banks")->with('created', 'Bank created successfully');
    }

    /**
     * Show the form for editing a new resource.
     *
     * @param Customer $customer
     * @param CustomerBank $bank
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Customer $customer, CustomerBank $bank): Response
    {
        $this->authorize('update', Customer::class);
        return Inertia::render('Customer/Bank/Form', [
            'bank' => $bank,
            'customer' => $customer,
        ]);
    }

    /**
     * Update a resource in storage.
     *
     * @param Customer $customer
     * @param CustomerBank $bank
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Customer $customer, CustomerBank $bank, Request $request): Redirector|Application|RedirectResponse
    {
        $this->authorize('update', Customer::class);
        $request->validate([
            'bank_name' => 'required',
            'bank_code' => 'required',
            'branch_code' => 'required',
            'account_number' => 'required',
            'swift_code' => 'required',
            'is_default' => 'boolean',
        ]);

        $data = $request->all();

        if (isset($data['is_default']) && $data['is_default']) {
            $customer->banks()->update(['is_default' => false]);
        }

        $bank->update($data);

        return redirect("/customers/$customer->id/banks")->with('updated', 'Bank updated successfully');
    }

    /**
     * Switch status the specified resource from storage.
     *
     * @param Customer $customer
     * @param CustomerBank $bank
     * @return RedirectResponse
     */
    public function switch(Customer $customer, CustomerBank $bank): RedirectResponse
    {
        $newStatus = $bank->status == CustomerBank::STATUS_APPROVE ? CustomerBank::STATUS_NONE : CustomerBank::STATUS_APPROVE;
        $bank->update(['status' => $newStatus]);

        return back()->with('updated', 'Bank status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Customer $customer
     * @param CustomerBank $bank
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Customer $customer, CustomerBank $bank): RedirectResponse
    {
        $this->authorize('delete', Customer::class);
        $bank->delete();

        return back()->with('deleted', 'Bank deleted successfully');
    }
}
