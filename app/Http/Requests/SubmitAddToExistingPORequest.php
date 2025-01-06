<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAddToExistingPORequest extends FormRequest
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
            'payload' => 'required|array',
            'payload.*.new_items.*.unit_price' => ['required', 'numeric', 'min:0.01'],
            'payload.*.new_items.*.gst' => ['required', 'numeric',  'min:0.01'],
            'payload.*.new_items.*.total_price' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages() {
        return [
            'payload.*.new_items.*.unit_price.min' => 'Unit price must be greater than 0.',
            'payload.*.new_items.*.unit_price.required' => 'Unit price is required.',
            'payload.*.new_items.*.total_price.min' => 'Total must be greater than 0.',
            'payload.*.new_items.*.gst.required' => 'Select GST',
            'payload.*.new_items.*.gst.min' => 'Select GST',
        ];
    }
}
