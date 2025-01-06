<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionOrderAttachment;

class ProductionOrderAttachmentController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'file_url' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'remark' => 'nullable|string|max:255',
        ]);
        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $path = $file->store('attachments', 'public');

            ProductionOrderAttachment::create([
                'production_order_id' => $request->production_order_id,
                'name' => $file->getClientOriginalName(),
                'file_url' => $path,
                'remarks' => $request->input('remarks'),
            ]);
        }
        return redirect()->back()->with('updated', 'File uploaded!');
    }

    public function destroy(ProductionOrderAttachment $ProductionOrderAttachment) {
        $ProductionOrderAttachment->delete();
        return redirect()->back()->with('updated', 'File has been deleted!');
    }
}
