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
        if (!$this->input('action')) {
            return [
                'image_path' => ['file', 'mimes:jpg,jpeg,png'],
            ];
        }

        if ($this->input('action')) {
            return [
                'image_path' => ['required', 'regex:/\.(jpg|jpeg|png)$/i'],
                'category' => ['required'],
                'condition' => ['required'],
                'name' => ['required'],
                'description' => ['required', 'max:255'],
                'price' => ['required', 'integer', 'min:0'],
            ];
        }
    }

    public function messages()
    {
        return [
            'image_path.file' => '画像はファイルを指定してください',
            'image_path.mimes' => '画像の拡張子は.jpegまたは.pngを指定してください',
            'image_path.regex' => '画像の拡張子は.jpegまたは.pngを指定してください',
            'image_path.required' => '画像を選択してください',
            'category.required' => 'カテゴリーを選択してください',
            'condition.required' => '商品状態を選択してください',
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以下で入力してください',
            'price.required' => '販売価格を入力してください',
            'price.integer' => '販売価格は整数型で入力してください',
            'price.min' => '販売価格は0円以上で入力してください',
        ];
    }
}
