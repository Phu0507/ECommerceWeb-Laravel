<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionUpdateRequest extends FormRequest
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
        'code' => [
            'required',
            'string',
            'max:255',
            Rule::unique('promotions')->ignore($this->route('id')),
        ],
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'description' => 'required|string|max:1000',
        'promotion_type' => 'required',
        'discount' => 'required|array',
        'discount_type' => 'required|string|in:fixed,percentage',
        'customer_option' => 'required',
        'product-range-select' => 'nullable',

        'order_amount_from' => Rule::requiredIf(fn() => $this->promotion_type === 'order'),
        'order_amount_to' => Rule::requiredIf(fn() => $this->promotion_type === 'order'),

        'ids' => 'nullable|array',
        'min_quantity' => Rule::requiredIf(fn() => $this->promotion_type === 'product'),
        'max_quantity' => Rule::requiredIf(fn() => $this->promotion_type === 'product'),

        'gender' => 'nullable',
        'customer_group' => 'nullable|array',
        'age_group' => 'nullable|array',
        ];
    }
}