<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
            'role' => 'required|in:0,1',
            'province_id' => 'required|exists:1_devvn_tinhthanhpho,matp',
            'district_id' => 'required|exists:2_devvn_quanhuyen,maqh',
            'ward_id' => 'required|exists:3_devvn_xaphuongthitran,xaid',
            'birthday' => 'nullable|date',
        ];
    }
    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute bắt buộc nhập',
            'email.email' => ':attribute chưa đúng định dạng',
            'email.unique' => ':attribute đã tồn tại trong hệ thống',
            'password.min' => ':attribute có ít nhất 8 kí tự',
            'role.in' => ':attribute không hợp lệ',
            'province_id.exists' => 'Tỉnh/Thành phố không hợp lệ',
            'district_id.exists' => 'Quận/Huyện không hợp lệ',
            'ward_id.exists' => 'Phường/Xã không hợp lệ',
            'birthday.date' => ':attribute phải là một ngày hợp lệ',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'phone.max' => ':attribute không được vượt quá 15 kí tự',
        ];
    }
    public function attributes(): array
    {
        return [
            'name' => 'Họ tên',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'phone' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'role' => 'Vai trò',
            'province_id' => 'Tỉnh/Thành phố',
            'ward_id' => 'Phường/Xã',
            'birthday' => 'Ngày sinh',
        ];
    }
}