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
            'name' => 'required|string',
            'description' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,jpg,png',
            'category_ids' => 'required|array',
            'condition' => 'required|string',
            'price' => 'required|numeric|min:0',
            'brand_name' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',

            'image.required' => '商品画像をアップロードしてください',
            'image.image' => '画像ファイルを選択してください',
            'image.mimes' => '画像はjpegまたはpng形式でアップロードしてください',

            'category_ids.required' => 'カテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',

            'price.required' => '商品価格を入力してください',
            'price.numeric' => '商品価格は数値で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
        ];
    }
}
