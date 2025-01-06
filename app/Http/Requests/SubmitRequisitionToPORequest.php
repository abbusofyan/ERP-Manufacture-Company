<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRequisitionToPORequest extends FormRequest
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
            'payload.*.payment_method' => 'required',
            'payload.*.sub_total' => 'required',
            'payload.*.total' => 'required',
            'payload.*.data.*.unit_price' => ['required', 'numeric', 'min:0.01'],
            'payload.*.data.*.gst' => ['required', 'numeric',  'min:0.01'],
            'payload.*.data.*.total' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages() {
        return [
            'payload.*.payment_method' => 'Select payment method.',
            'payload.*.terms' => 'Select terms.',
            'payload.*.data.*.unit_price.min' => 'Unit price must be greater than 0.',
            'payload.*.data.*.unit_price.required' => 'Unit price is required.',
            'payload.*.data.*.total.min' => 'Total must be greater than 0.',
            'payload.*.data.*.gst.required' => 'Select GST',
            'payload.*.data.*.gst.min' => 'Select GST',
        ];
    }
}
