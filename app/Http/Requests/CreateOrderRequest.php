<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'vendor_id' => 'required',
            'status' => 'required',
            'ref_no' => 'required',
            'vendor_address' => 'nullable',
            'vendor_phone' => 'nullable',
            'delivery_address' => 'required',
            'edd' => 'required|date',
            'remark' => 'nullable',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.date' => 'required|date',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|numeric',
            'items.*.description' => 'nullable',
            'items.*.remark' => 'nullable',
            'items.*.currency' => 'required',
            'items.*.credit_term' => 'required|numeric',
            'items.*.tax' => 'nullable|numeric',
            'items.*.gst' => 'nullable|numeric',
            'items.*.nr' => 'nullable|numeric',
            'items.*.freight_charges' => 'nullable|numeric',
            'items.*.discount' => 'nullable|numeric',
            'items.*.discount_price' => 'nullable|numeric',
            'items.*.image_url' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ];
    }

    public function messages() {
        return [
            'vendor_id.required' => 'The Vendor Name field is required.',
            'status.required' => 'The Status field is required.',
            'ref_no.required' => 'The Reference No. field is required.',
            'edd.required' => 'The Delivery date field is required.',
            'edd.date' => 'The Delivery date must be a valid date.',
            'items.required' => 'At least one item is required.',
            'items.array' => 'The items must be an array.',
            'items.min' => 'At least one item is required.',
            'items.*.product_id.required' => 'The Product Name field for an item is required.',
            'items.*.date.required' => 'The Required Date field for an item is required.',
            'items.*.date.date' => 'The Required Date for an item must be a valid date.',
            'items.*.price.required' => 'The Price field for an item is required.',
            'items.*.price.numeric' => 'The Price for an item must be a number.',
            'items.*.quantity.required' => 'The Quantity field for an item is required.',
            'items.*.quantity.numeric' => 'The Quantity for an item must be a number.',
            'items.*.description.nullable' => 'The Description for an item must be nullable.',
            'items.*.remark.nullable' => 'The Remark for an item must be nullable.',
            'items.*.currency.required' => 'The Currency field for an item is required.',
            'items.*.credit_term.required' => 'The Credit Term field for an item is required.',
            'items.*.credit_term.numeric' => 'The Credit Term for an item must be a number.',
            'items.*.tax.nullable' => 'The TAX for an item must be nullable.',
            'items.*.tax.numeric' => 'The TAX for an item must be a number.',
            'items.*.gst.nullable' => 'The GST for an item must be nullable.',
            'items.*.gst.numeric' => 'The GST for an item must be a number.',
            'items.*.nr.nullable' => 'The NR for an item must be nullable.',
            'items.*.nr.numeric' => 'The NR for an item must be a number.',
            'items.*.freight_charges.nullable' => 'The Freight Charges for an item must be nullable.',
            'items.*.freight_charges.numeric' => 'The Freight Charges for an item must be a number.',
            'items.*.discount.nullable' => 'The Discount for an item must be nullable.',
            'items.*.discount.numeric' => 'The Discount for an item must be a number.',
            'items.*.discount_price.nullable' => 'The Discount Price for an item must be nullable.',
            'items.*.discount_price.numeric' => 'The Discount Price for an item must be a number.',
            'items.*.image_url.nullable' => 'The Image for an item must be nullable.',
            'items.*.image_url.image' => 'The Image for an item must be an image.',
            'items.*.image_url.mimes' => 'The Image for an item must be a valid image format (png, jpg, jpeg).',
            'items.*.image_url.max' => 'The Image for an item must not exceed 2048 kilobytes.',
        ];
    }
}
