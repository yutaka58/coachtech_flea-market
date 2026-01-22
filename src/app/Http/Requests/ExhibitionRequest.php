<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => 'required',
            'description' => 'required|max:255',
            'image' => 'required|mines:jpeg,png',
            'category_id' => 'required',
            'condition' => 'required',
            'price' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max255' => '255文字以内で入力してください',
            'image.required' => '商品画像を登録してください',
            'image.mines:jpeg,png' => '「.png」または「.jpeg」形式でアップロードしてください',
            'category_id.required' => '商品のカテゴリーを選択してください',
            'condition.required' => '商品の状態を入力してください',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '数値で入力してください',
            'price.min:0' => '０円以上で入力してください',
        ];
    }
}
