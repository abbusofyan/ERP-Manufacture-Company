<?php

namespace App\Http\Controllers;

use App\Models\Storage;
use App\Models\StorageItem;
use App\Models\Location;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDocument;
use App\Models\StockAdjustmentItem;
use App\Models\Store;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class StockDocumentController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(StockAdjustment::class, 'stock');
    }

    public function store(StockAdjustment $stockAdjustment, Request $request)
    {
        $request->validate([
            'file_url' => 'required|mimes:pdf|max:5000',
        ], [
            'file_url.required' => 'The file is required.',
            'file_url.mimes' => 'The file must be a PDF.',
            'file_url.max' => 'The file must be smaller than 5MB.',
        ]);

        /** Create Stock Adjustment Documents **/
        $path = $request->file('file_url')->store("stock-adjustments/$stockAdjustment->id", 'public');
        StockAdjustmentDocument::create([
            'stock_adjustment_id' => $stockAdjustment->id,
            'name' => $request->file('file_url')->getClientOriginalName(),
            'file_url' => $path,
        ]);

        return back()->with('created', 'Stock document uploaded successfully');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param StockAdjustment $stockAdjustment
     * @param StockAdjustmentDocument $document
     * @return RedirectResponse
     */
    public function destroy(StockAdjustment $stockAdjustment, StockAdjustmentDocument $document): RedirectResponse
    {
        $document->delete();

        return back()->with('deleted', 'Stock document deleted successfully');
    }
}
