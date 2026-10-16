<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
        $id = $this->route('id'); // id của PostCatalogue đang update
        return [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'canonical' => [
            'required',
            'string',
            'max:255',
            Rule::unique('routers', 'canonical')
                ->ignore($id, 'module_id') // bỏ qua chính record đang update
        ],
        'base_price' => 'nullable|numeric|min:0',
        'base_quantity' => 'nullable|integer|min:0',
        'category_id' => 'gt:0',
        'status' => 'gt:0',
        'base_price' => 'nullable|numeric|min:0',
        'base_quantity' => 'nullable|integer|min:0',
        'meta_title' => 'nullable|string|max:255',
        'variant_quantity'   => 'nullable|array',
        'variant_price'   => 'nullable|array',
        'variant_sku'   => 'nullable|array',
        'variant_attributes'   => 'nullable',
        'variant_image' =>'nullable',
        'variant_id'=>'nullable'
    ];
    }
}