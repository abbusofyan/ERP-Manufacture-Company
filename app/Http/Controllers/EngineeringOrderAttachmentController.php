<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EngineeringOrderAttachment;

class EngineeringOrderAttachmentController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'file_url' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            'remark' => 'nullable|string|max:255',
        ]);
        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $path = $file->store('attachments', 'public');

            EngineeringOrderAttachment::create([
                'engineering_order_id' => $request->engineering_order_id,
                'name' => $file->getClientOriginalName(),
                'file_url' => $path,
                'remarks' => $request->input('remarks'),
            ]);
        }
        return redirect()->back()->with('updated', 'File uploaded!');
    }

    public function destroy(EngineeringOrderAttachment $EngineeringOrderAttachment) {
        $EngineeringOrderAttachment->delete();
        return redirect()->back()->with('updated', 'File has been deleted!');
    }
}
