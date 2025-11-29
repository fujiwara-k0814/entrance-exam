<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
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
        if ($this->input('delete')) {
            return [];
        }

        if ($this->input('edit')) {
            return [
                'message.*' => ['required', 'max:400'],
            ];
        }

        if (!$this->input('send')) {
            return [
                'image_path' => ['file', 'mimes:jpg,jpeg,png'],
            ];
        }

        if ($this->input('send')) {
            if ($this->input('image_path')) {
                return [
                    'content' => ['required', 'max:400'],
                    'image_path' => ['regex:/\.(jpg|jpeg|png)$/i'],
                ];
            }
            return [
                'content' => ['required', 'max:400'],
            ];
        }
    }

    public function messages()
    {
        return [
            'image_path.file' => '画像はファイルを指定してください',
            'image_path.mimes' => '画像の拡張子は.jpegまたは.pngを指定してください',
            'image_path.regex' => '画像の拡張子は.jpegまたは.pngを指定してください',

            'message.*.required' => '本文を入力してください',
            'message.*.max' => '本文は400文字以内で入力してください',
        ];
    }
}
