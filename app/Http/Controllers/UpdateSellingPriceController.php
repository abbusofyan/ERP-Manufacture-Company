<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Product;
use App\Exports\UpdateSellingPriceExport;
use App\Imports\UpdateSellingPriceImport;
use Maatwebsite\Excel\Facades\Excel;

class UpdateSellingPriceController extends Controller
{
    public function create(): Response
    {
        $categories = self::toSelect2Data(Category::all()->toArray(), 'id', 'prefix', 'name', ' | ');
        return Inertia::render('UpdateSellingPrice/Form',[
            'categories' => $categories,
            'csrf' => csrf_token(),
        ]);
    }

    public function store(Request $request) {
        $request->validate(
            [
                'category_id' => 'required|not_in:0',
                'effective_date' => 'required|date',
                'items.*.product_id' => 'required_if:input_type,Manual Input',
                'items.*.new_selling_price' => 'required_if:input_type,Manual Input',
                'file' => 'required_if:input_type,Upload File',
            ],
            [
                'category_id.required' => 'Select category',
                'category_id.not_in' => 'Select category',
                'file.required_if' => 'Select file',
                'items.*.product_id.required_if' => 'Select a product',
                'items.*.new_selling_price.required_if' => 'Input new selling price',
            ]
        );

        DB::beginTransaction();
        try {
            $effective_date = date('Y-m-d', strtotime($request->effective_date));

            if ($request->input_type == 'Manual Input') {
                foreach ($request->items as $item) {
                    Product::find($item['product_id'])->update([
                        'selling_price' => $item['new_selling_price'],
                        'effective_date_selling_price' => $effective_date
                    ]);
                }
            } else {
                $import = new UpdateSellingPriceImport;
                Excel::import($import, $request->file('file'));
                foreach ($import->rows as $key => $row) {
                    if ($key == 0) {
                        continue;
                    }
                    Product::where('sku', $row['0'])->update([
                        'selling_price' => $row['3'],
                        'effective_date_selling_price' => $effective_date
                    ]);
                }
            }
            DB::commit();
            return redirect('/products')->with('created', 'Product selling price updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('deleted', 'Failed to update selling price: ' . $e->getMessage());
        }

    }

    public function select2Query(Request $request)
    {
        return UnitOfMeasurement::when($request->has('search'), function ($query) use ($request) {
            $query->where('description', 'LIKE', "%{$request->search}%")
                ->orWhere('code', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
    }

    public function downloadItemList(Request $request) {
        $data = Product::where('category_id', $request->category_id)->get();
        return Excel::download(new UpdateSellingPriceExport($request->category_id, $data), 'item-list.xlsx');
    }
}
