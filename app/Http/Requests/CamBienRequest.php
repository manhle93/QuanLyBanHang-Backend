<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CamBienRequest extends FormRequest
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
            'ma' => 'required',
            'IMEI_thiet_bi' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'ma.required' => 'Mã cảm biến không được để trống',
            'IMEI_thiet_bi.required' => 'IMEI thiết bị không được để trống'
        ];
    }
}
