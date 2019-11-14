<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItem extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:80',
            'price' => 'required',
            'sku' => 'required',
            'descriptionEditorArea' => 'nullable',
            'fulfillmentPolicyId' => 'required',
            'returnPolicyId' => 'required',
            'paymentPolicyId' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'A Title is required',
            'sku.required'  => 'A custom SKU is required',
        ];
    }
}
