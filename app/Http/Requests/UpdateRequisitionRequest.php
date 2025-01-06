<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequisitionRequest extends FormRequest
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
        $rules = [
            'status' => 'required|in:1,2,3,4,5',
            'type' => 'required|in:1,2,3,4,5,6,7',
            'urgent_orders' => 'required|boolean',
            'date' => 'nullable|date',
            'note' => 'nullable|string',
            'inventory_items' => 'required|array|min:1',
            'inventory_items.*.product_id' => 'required_if:inventory_items.*.new_item,false',
            'inventory_items.*.product_name' => 'required',
            'inventory_items.*.uom_code' => 'required',
            'inventory_items.*.requested_qty' => 'required|numeric|min:0',
            'inventory_items.*.date' => 'nullable|date',
            'inventory_items.*.remark' => 'nullable|string',
            'inventory_items.*.file_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($this->input('type') == '7') {
            $rules['service_order_id'] = 'required|exists:service_orders,id';
        } else {
            $rules['production_order_id'] = 'nullable|exists:production_orders,id';
        }

        return $rules;
    }

    public function messages() {
        return [
            'status.required' => 'The status field is required.',
            'status.in' => 'Invalid value for the status field. Please select a valid status.',
            'type.required' => 'The type field is required.',
            'type.in' => 'Invalid value for the type field. Please select a valid type.',
            'order_id.required' => 'The order ID field is required.',
            'order_id.exists' => 'The selected order ID is invalid. Please select a valid order.',
            'service_order_id.required' => 'The service order ID field is required.',
            'service_order_id.exists' => 'The selected service order ID is invalid.',
            'approved_by.string' => 'The approved by field must be a string.',
            'urgent_orders.required' => 'The urgent orders field is required.',
            'urgent_orders.boolean' => 'The urgent orders field must be true or false.',
            'date.date' => 'The date field must be a valid date.',
            'note.string' => 'The note field must be a string.',
            'inventory_items.required' => 'Add at least 1 item.',
            'inventory_items.*.product_id.required' => 'The product ID field for inventory items is required.',
            'inventory_items.*.product_id.exists' => 'The selected product ID for inventory items is invalid.',
            'inventory_items.*.product_name.required' => 'Select an item or add new item name.',
            'inventory_items.*.uom_code.required' => 'Select UOM.',
            'inventory_items.*.requested_qty.required' => 'The quantity field for inventory items is required.',
            'inventory_items.*.quantity.numeric' => 'The quantity field for inventory items must be a number.',
            'inventory_items.*.quantity.min' => 'The quantity field for inventory items must be at least :min.',
            'inventory_items.*.date.date' => 'The date field for inventory items must be a valid date.',
            'inventory_items.*.remark.string' => 'The remark field for inventory items must be a string.',
            'inventory_items.*.file_url.image' => 'The file URL field for inventory items must be an image.',
            'inventory_items.*.file_url.mimes' => 'The file URL field for inventory items must be of type: jpeg, png, jpg.',
            'inventory_items.*.file_url.max' => 'The file URL field for inventory items must not be larger than :max kilobytes.'
        ];
    }
}
