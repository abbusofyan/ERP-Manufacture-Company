<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequisitionRequest extends FormRequest
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
            'status' => 'required|in:1,2,3,4',
            'type' => 'required|in:1,2,3,4,5,6,7',
            'urgent_orders' => 'required|boolean',
            'date' => 'nullable|date',
            'note' => 'nullable|string',
            'inventory_items' => 'required|array|min:1',
            'inventory_items.*.product_name' => 'required',
            'inventory_items.*.store_id' => 'required',
            'inventory_items.*.uom_code' => 'required',
            'inventory_items.*.requested_qty' => 'required|numeric|min:0',
            'inventory_items.*.date' => 'nullable|date',
            'inventory_items.*.remark' => 'nullable|string',
            'inventory_items.*.file_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'inventory_items.*.category_id' => 'required',
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
            'service_order_id.required' => 'The service order ID field is required.',
            'service_order_id.exists' => 'The selected service order ID is invalid.',
            'production_order_id.exists' => 'The selected production order ID is invalid.',
            'urgent_orders.required' => 'The urgent orders field is required.',
            'urgent_orders.boolean' => 'The urgent orders field must be true or false.',
            'date.date' => 'The date field must be a valid date.',
            'note.string' => 'The note field must be a string.',
            'inventory_items.required' => 'Add at least 1 item.',
            'inventory_items.*.product_name.required' => 'Select an item or add new item name.',
            'inventory_items.*.store_id.required' => 'Select warehouse.',
            'inventory_items.*.uom_code.required' => 'Select UOM.',
            'inventory_items.*.requested_qty.required' => 'The quantity field is required.',
            'inventory_items.*.requested_qty.numeric' => 'The quantity field for inventory items must be a number.',
            'inventory_items.*.requested_qty.min' => 'The quantity field for inventory items must be at least :min.',
            'inventory_items.*.date.date' => 'The date field for inventory items must be a valid date.',
            'inventory_items.*.remark.string' => 'The remark field for inventory items must be a string.',
            'inventory_items.*.file_url.image' => 'The file URL field for inventory items must be an image.',
            'inventory_items.*.file_url.mimes' => 'The file URL field for inventory items must be of type: jpeg, png, jpg.',
            'inventory_items.*.file_url.max' => 'The file URL field for inventory items must not be larger than :max kilobytes.',
            'inventory_items.*.category_id.required' => 'Select category.',
        ];
    }
}
