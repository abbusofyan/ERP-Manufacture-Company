<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'sku' => 'required|string|max:255|unique:products,sku,NULL,id,deleted_at,NULL',
            'name' => 'required|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'uom_id' => 'required|exists:unit_of_measurements,id',
            'auto_reorder' => 'required|boolean',
            'selling_price' => 'required',
            'lead_time' => 'nullable|integer',
            'reorder_level' => 'nullable|integer',
            'remark' => 'nullable|string|max:2000',
            'quantity_to_reorder' => 'nullable|integer',
            'file_url' => 'nullable|image|mimes:png,jpg,jpeg',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif',
            'description' => 'max:2000'
        ];
    }

    public function messages() {
        return [
            'prices.required' => 'At least one price must be provided.',
            'prices.array' => 'Prices must be an array.',
            'prices.min' => 'At least one price must be provided.',
            'prices.*.vendor_id.required' => 'Vendor ID is required for each price.',
            'prices.*.vendor_id.exists' => 'The selected vendor for a price is invalid.',
            'prices.*.price.required' => 'Price is required for each item.',
            'prices.*.price.numeric' => 'Price must be a number for each item.'
        ];
    }
}
