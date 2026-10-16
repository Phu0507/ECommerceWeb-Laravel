<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email|exists:users,email',
            'password' =>'required|string|min:8'
        ];
    }

    public function messages(): array
    {
        return [
        'required' => ':attribute bắt buộc nhập',
        'email.email' => ':attribute chưa đúng định dạng',
        'password.min' => ':attribute có ít nhất 8 kí tự',
        'email.exists' => ':attribute không tồn tại trong hệ thống'
        ];
    }

    public function attributes(): array
    {
        return [
        'email' => 'Email',
        'password' => 'Mật khẩu'
        ];
    }
}