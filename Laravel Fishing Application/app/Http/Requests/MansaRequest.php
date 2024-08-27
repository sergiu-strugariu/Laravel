<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MansaRequest extends FormRequest
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
            'concurs_id' => ['required'],
            'lac_id' => ['required'],
            'nume' => ['required', 'string', 'max:255'],
            'start_mansa' => ['required'],
            'stop_mansa' => ['required'],
            'status_mansa' => ['required'],
            'participanti_max' => ['required', 'integer'],

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
