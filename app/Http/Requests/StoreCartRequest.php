<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required' => 'Bạn chưa nhập Họ Tên.',
            'phone.required' => 'Bạn chưa nhập Số điện thoại.',
            'email.required' => 'Bạn chưa nhập Email.',
            'email.email' => 'Email không đúng định dạng.',
            'address.required' => 'Bạn chưa nhập địa chỉ.',
        ];
    }
}
