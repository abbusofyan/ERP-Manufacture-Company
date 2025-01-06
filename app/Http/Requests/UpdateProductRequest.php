<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
        $product = $this->route('product');
        return [
            'sku' => 'nullable|string|max:255|unique:unit_of_measurements,code,' . $product->id . ',id,deleted_at,NULL',
            'name' => 'required|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'uom_id' => 'required|exists:unit_of_measurements,id',
            'selling_price' => 'required',
            'auto_reorder' => 'required|boolean',
            'lead_time' => 'nullable|integer',
            'reorder_level' => 'nullable|integer',
            'remark' => 'nullable|string|max:2000',
            'quantity_to_reorder' => 'nullable|integer',
            'file_url' => 'nullable|image|mimes:png,jpg,jpeg',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif,docs,docx,webp',
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
