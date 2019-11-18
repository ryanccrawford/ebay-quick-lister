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
        return true;
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
            'price' => 'required|numeric',
            'sku' => 'required',
            'descriptionEditorArea' => 'required|string',
            'ShippingPoliciesResponse' => 'required|numeric',
            'ReturnPoliciesResponse' => 'required|numeric',
            'PaymentPoliciesResponse' => 'required|numeric',
            'shippingCost' => 'required|numeric',
            'shippingLength' => 'required|numeric',
            'shippingWidth' => 'required|numeric',
            'shippingHeight' => 'required|numeric',
            'shippingWeight' => 'required|numeric',
            'qty' => 'required|numeric',
            'primaryCategory' => 'required|numeric',
            //'descriptionImageFile' => 'required|image|mimes:jpeg,png,jpg',
            //'mainImageFile' => 'required|image|mimes:jpeg,png,jpg',

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
