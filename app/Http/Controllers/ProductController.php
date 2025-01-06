<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDocument;
use App\Models\ProductPrice;
use App\Models\UnitOfMeasurement;
use App\Models\Vendor;
use App\Models\StoreProduct;
use App\Models\ProductPhoto;
use App\Models\ExpenseCode;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Order;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\ProductService;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\CreateProductRequest;
use Illuminate\Support\Facades\Route;

class ProductController extends Controller
{
    public $productService;

    public function __construct(ProductService $productService)
    {
        $this->authorizeResource(Product::class, 'product');
        $this->productService = $productService;
    }

    public function select2Query(Request $request)
    {
        return Product::with('uom', 'photos', 'prices.vendor')->when($request->has('search'), function ($query) use ($request) {
            $query->where('sku', 'LIKE', "%{$request->search}%")
                ->orWhere('name', 'LIKE', "%{$request->search}%");
        })->when($request->has('category_id') && $request->category_id != '', function($query) use ($request) {
            $query->where('category_id', $request->category_id);
        })->orderBy('id', 'DESC')->limit(100)->get();
    }

    public function select2QueryWithStorage(Request $request)
    {
        return Product::with('uom')->when($request->has('search'), function ($query) use ($request) {
            $query->where('sku', 'LIKE', "%{$request->search}%")
                ->orWhere('name', 'LIKE', "%{$request->search}%");
        })
            ->has('storageItems')
            ->limit(10)->get();
    }

    public function select2QueryByCategory(Request $request)
    {
        return Product::with('uom')->where('category_id', $request->category_id)->when($request->has('search'), function ($query) use ($request) {
            $query->where('sku', 'LIKE', "%{$request->search}%")
                ->orWhere('name', 'LIKE', "%{$request->search}%");
        })->with('prices')->limit(10)->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
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

        $products = Product::with('category', 'assembly', 'finishGoods')->with('uom')->where('status', Product::STATUS_ACTIVE)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
                $query->orWhere('sku', 'LIKE', "%{$search}%");
                $query->orWhere('type', 'LIKE', "%{$search}%");
                $query->orWhere('selling_price', 'LIKE', "%{$search}%");
                $query->orWhereHas('category', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', "%{$search}%");
                    $subQuery->orWhere('prefix', 'LIKE', "%{$search}%");
                });
                $query->orWhereHas('uom', function ($subQuery) use ($search) {
                    $subQuery->where('code', 'LIKE', "%{$search}%");
                    $subQuery->orWhere('description', 'LIKE', "%{$search}%");
                });
            })->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $products->appends($queryString);

        return Inertia::render('Product/Index', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Product/Form', [
            'categories' => toSelect2Data(Category::all()->toArray(), 'id', 'prefix', 'name', ' | '),
            'uom' => toSelect2Data(UnitOfMeasurement::all()->toArray(), 'id', 'code'),
            'vendors' => toSelect2Data(Vendor::all()->toArray(), 'id', 'code', 'name', ' | '),
            'csrf' => csrf_token(),
            'expense_codes' => toSelect2Data(ExpenseCode::all()->toArray(), 'code', 'code')
        ]);
    }

    public function store(CreateProductRequest $request)
    {
        DB::beginTransaction();

        try {

            $product = $this->productService->createNewProduct($request->all());

            DB::commit();

            return redirect('/products')->with('created', 'Product created successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('deleted', $e->getMessage());
        }
    }


    public function edit(Product $product): Response
    {
        return Inertia::render('Product/Form', [
            'product' => $product,
            'categories' => toSelect2Data(Category::all()->toArray(), 'id', 'name'),
            'uom' => toSelect2Data(UnitOfMeasurement::all()->toArray(), 'id', 'code'),
            'vendors' => toSelect2Data(Vendor::all()->toArray(), 'id', 'code', 'name', ' | '),
            'prices' => $product->prices()->get(),
            'documents' => $product->documents()->get(),
            'csrf' => csrf_token(),
        ]);
    }


    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::beginTransaction();
        try {
            $this->productService->updateProduct($product, $request->all());
            DB::commit();
            return redirect('/products/' . $product->id)->with('updated', 'Product updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/products/' . $product->id)->with('deleted', 'Error : ' . $e->getMessage());
        }
    }


    /**
     * Update the specified resource from storage.
     *
     * @param Product $product
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function updateStatus(Product $product): RedirectResponse
    {
        $this->authorize('update-product');
        if (intval($product->status) === 0) {
            $product->update(['status' => 1]);
        } else {
            $product->update(['status' => 0]);
        }

        return redirect('/products')->with('updated', 'Product status updated successfully');
    }


    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        $product->listRequest = $product->listRequest();
        $product->listCommit = $product->listCommit();
        $product->listOrder = $product->listOrder();

        $product->load('prices.vendor', 'category', 'uom', 'storageItems.storage',
            'storageItems.location', 'photos', 'salesPhotos',
            'assembly.materials.product.category',
            'assembly.materials.product.uom',
            'finishGoods.items.product.category',
            'finishGoods.items.product.uom',
            'documents',
            'assembly',
            'histories.createdBy'
        );
        $product->requisitionItems = RequisitionItem::with('requisition.requisitionable')->where('product_id', $product->id)->get();
        return Inertia::render('Product/Detail', [
            'product' => $product,
            'prices' => $product->prices()->with('vendor')->get(),
            'storeProduct' => StoreProduct::with('location', 'store')->where('product_id', $product->id)->get(),
            'lowestCost' => $product->lowestCost(),
            'highestCost' => $product->highestCost(),
            'lastCost' => $product->lastCost(),
            'documents' => $product->documents()->get(),
            'order_status_text_arr' => Order::STATUS_TEXT_ARRAY,
            'order_status_class_arr' => Order::STATUS_CLASS_ARRAY,
            'requisition_status_text_arr' => Requisition::STATUS_ARRAY,
            'requisition_status_class_arr' => Requisition::STATUS_CLASS_ARRAY,
            'requisition_type_arr' => Requisition::ORDER_TYPE_ARRAY,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect('/products')->with('deleted', 'Product deleted successfully');
    }


    public function inactive(Request $request): Response
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

        $products = Product::with('category')->with('uom')->where('status', Product::STATUS_INACTIVE)
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })->when(($order && $by), function ($query) use ($order, $by) {
                $query->orderBy($order, $by);
            })->paginate($paginate);

        $queryString = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
            'paginate' => $paginate,
        ];

        $products->appends($queryString);

        return Inertia::render('Product/Index', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    public function uploadSalesPhoto(Request $request, Product $product)
    {
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos', []) as $photo) {
                $name = $photo->getClientOriginalName();
                $path = $photo->store("product/$product->id", 'public');
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->user()->id,
                    'path' => $path,
                    'is_sales_photo' => 1
                ]);
            }
        }
        return redirect('/products/' . $product->id)->with('created', 'Photo uploaded successfully');
    }

    public function getPricesByVendor(Request $request)
    {
        return ProductPrice::where('product_id', $request->product_id)->where('vendor_id', $request->vendor_id)->first();
    }

    public function updatePrice(Product $product, Request $request)
    {
        ProductPrice::create([
            'product_id' => $product->id,
            'vendor_id' => $request->vendor_id,
            'price' => $request->unit_price
        ]);
        return redirect('/requisitions-to-order')->with('created', 'Vendor cross reference added successfully');
    }

    public function generateItemId(Request $request)
    {
        return Product::generateItemId();
    }

    public function getLastNonInventoryCode()
    {
        return Product::getLastNonInventoryCode();
    }

    public function getStockByWarehouse($product_id, $store_id) {
        $storeProduct = StoreProduct::where('product_id', $product_id)->where('store_id', $store_id)->first();
        return $storeProduct ? $storeProduct->stock : 'N/A';
    }

}
