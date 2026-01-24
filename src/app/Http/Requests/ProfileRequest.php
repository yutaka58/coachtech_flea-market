<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'image' => 'image|mimes:jpeg,png',
            'name' => 'required|max:20',
            'post_code' => 'required|max:8',
            'address' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'image.mimes:jpeg,png' => '「.jpeg」または「.png」形式でアップロードしてください',
            'name.required' => 'ユーザー名を入力してください',
            'name.max:20' => '20文字以内で入力してください',
            'post_code.required' => '郵便番号を入力してください',
            'post_code.max:8' => 'ハイフンを入れた8文字以内で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
