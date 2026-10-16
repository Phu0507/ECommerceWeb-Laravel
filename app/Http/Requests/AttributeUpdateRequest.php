<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttributeUpdateRequest extends FormRequest
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
        'category_id' => 'gt:0',
        'status' => 'gt:0',
        ];
    }
}