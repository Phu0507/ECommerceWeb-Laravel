<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'canonical' => 'required|string|max:191|unique:routers,canonical',
            'category_id' => 'gt:0',
            'status' => 'gt:0',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'base_price' => 'nullable|numeric|min:0',
            'base_quantity' => 'nullable|integer|min:0',
            // validate variants
            'variant_quantity'   => 'nullable',
            'variant_price'   => 'nullable',
            'variant_sku'   => 'nullable',
            'variant_attributes'   => 'nullable',
            'variant_image' =>'nullable',
            'variant_id'=>'nullable'
        ];
    }
}