<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class VendorAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Vendor $vendor
     * @param Request $request
     * @return Response
     */
    public function index(Vendor $vendor, Request $request): Response
    {
        // Determine order and sort direction
        $order = $request->get('order', 'id');
        $by = $request->get('by', 'desc');

        // Determine pagination limit
        $paginate = $request->get('paginate', 10);
        $page = $request->get('page', 1);

        // Query to retrieve attachments
        $attachments = VendorAttachment::where('vendor_id', $vendor->id)
            ->orderBy($order, $by)
            ->paginate($paginate, ['*'], 'page', $page)
            ->appends([
                'order' => $order,
                'by' => $by,
                'paginate' => $paginate,
                'page' => $page,
            ]);

        // Return view with vendor and attachments
        return Inertia::render('Vendor/Attachment/View', [
            'vendor' => $vendor,
            'attachments' => $attachments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param Vendor $vendor
     * @return RedirectResponse
     */
    public function store(Request $request, Vendor $vendor)
    {
        // Validate the request
        $request->validate([
            'file_url' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096', // Include PDF for variety
            'remarks' => 'nullable|string|max:255',
        ]);

        // Check if a file was uploaded
        if ($request->hasFile('file_url')) {
            // Get the uploaded file
            $file = $request->file('file_url');

            // Store the file and get its path
            $path = $file->store("vendor_attachments/$vendor->id", 'public');

            // Extract the file name from the file object
            $fileName = $file->getClientOriginalName();

            // Create a new VendorAttachment instance
            $attachment = new VendorAttachment([
                'name' => $fileName, // Use the file's original name
                'file_url' => $path, // Store the file path
                'remarks' => $request->input('remarks'), // Optional remarks
            ]);

            // Associate the attachment with the vendor and save it
            $vendor->attachments()->save($attachment);
        }

        // Redirect back with a success message
        return back()->with('created', 'Attachment uploaded successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Vendor $vendor
     * @param VendorAttachment $attachment
     * @return RedirectResponse
     */
    public function destroy(Vendor $vendor, VendorAttachment $attachment)
    {
        // Delete the file from storage
        if (Storage::exists($attachment->file_url)) {
            Storage::delete($attachment->file_url);
        }

        // Delete the record from the database
        $attachment->delete();

        return redirect()->back()->with('deleted', 'Attachment deleted successfully.');
    }
}
