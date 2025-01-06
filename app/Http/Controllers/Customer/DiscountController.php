<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerBank;
use App\Models\CustomerDiscount;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;

class DiscountController extends Controller
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

        $discounts = CustomerDiscount::when($search, function ($query) use ($search) {
            $query->where('category_name', 'LIKE', "%{$search}%")
                ->orWhere('category_id', 'LIKE', "%{$search}%");
        })->where('customer_id', $customer->id)->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate
        ];

        $discounts->appends($queryString);

        return Inertia::render('Customer/Discount/View', [
            'discounts' => $discounts,
            'filters' => $filters,
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Customer $customer
     * @param CustomerDiscount $discount
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function markApprove(Customer $customer, CustomerDiscount $discount): RedirectResponse
    {
        $this->authorize('update', Customer::class);

        $customer->discounts()->update(['is_default' => false]);
        $discount->update(['is_default' => true, 'status' => CustomerDiscount::STATUS_APPROVE]);

        return back()->with('updated', 'Bank updated successfully');
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
        return Inertia::render('Customer/Discount/Form', [
            'customer' => $customer,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Customer $customer
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function store(Customer $customer, Request $request): RedirectResponse
    {
        $this->authorize('create', Customer::class);
        $request->validate([
            'category_id' => 'required|string',
            'category_name' => 'required|string',
            'discount_percentage' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['customer_id'] = $customer->id;
        $data['status'] = CustomerDiscount::STATUS_APPROVE;

        if (isset($data['is_default']) && $data['is_default']) {
            // Ensure only one discount is marked as default
            CustomerDiscount::where('customer_id', $customer->id)->update(['is_default' => false]);
        }

        CustomerDiscount::create($data);

        return redirect("/customers/$customer->id/discounts")->with('created', 'Discount created successfully');
    }

    /**
     * Show the form for editing a resource.
     *
     * @param Customer $customer
     * @param CustomerDiscount $discount
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Customer $customer, CustomerDiscount $discount): Response
    {
        $this->authorize('update', Customer::class);
        return Inertia::render('Customer/Discount/Form', [
            'discount' => $discount,
            'customer' => $customer,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Customer $customer
     * @param CustomerDiscount $discount
     * @param Request $request
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(Customer $customer, CustomerDiscount $discount, Request $request): RedirectResponse
    {
        $this->authorize('update', Customer::class);
        $request->validate([
            'category_id' => 'required|string',
            'category_name' => 'required|string',
            'discount_percentage' => 'required|numeric',
        ]);

        $data = $request->all();

        if (isset($data['is_default']) && $data['is_default']) {
            // Ensure only one discount is marked as default
            CustomerDiscount::where('customer_id', $customer->id)->update(['is_default' => false]);
        }

        $discount->update($data);

        return redirect("/customers/$customer->id/discounts")->with('updated', 'Discount updated successfully');
    }

    /**
     * Switch status the specified resource from storage.
     *
     * @param Customer $customer
     * @param CustomerDiscount $discount
     * @return RedirectResponse
     */
    public function switch(Customer $customer, CustomerDiscount $discount): RedirectResponse
    {
        $newStatus = $discount->status == CustomerDiscount::STATUS_APPROVE
            ? CustomerDiscount::STATUS_NONE
            : CustomerDiscount::STATUS_APPROVE;

        $discount->update(['status' => $newStatus]);

        return back()->with('updated', 'Discount status updated successfully');
    }

    /**
     * Duplicate the specified resource in storage.
     *
     * @param Customer $customer
     * @param CustomerDiscount $discount
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function duplicate(Customer $customer, CustomerDiscount $discount): RedirectResponse
    {
        $this->authorize('create', Customer::class);
        $new = $discount->replicate();
        $new->is_default = false;
        $new->save();
        return back()->with('created', 'Discount duplicated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Customer $customer
     * @param CustomerDiscount $discount
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function destroy(Customer $customer, CustomerDiscount $discount): RedirectResponse
    {
        $this->authorize('delete', Customer::class);
        $discount->delete();

        return back()->with('deleted', 'Discount deleted successfully');
    }
}
