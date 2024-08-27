<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InscriereRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'created_by' => ['required'],
            'pescar_id' => ['required'],
            'concurs_id' => ['required'],
            'mansa_id' => ['required'],
            'stand_id' => ['required'],
            'lac_id' => ['required'],
            'sector_id' => ['required'],
            'puncte_penalizare' => ['required', 'integer'],
            'nume_trofeu' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
