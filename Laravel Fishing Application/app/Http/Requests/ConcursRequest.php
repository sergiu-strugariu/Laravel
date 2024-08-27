<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConcursRequest extends FormRequest
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
            'nume' => ['required', 'string', 'max:255'],
            'created_by' => ['required'],
            'organizator_id' => ['required'],
            'descriere' => ['required'],
            'regulament' => ['required'],
            'poza' => ['required', 'string', 'max:255'],
            'start' => ['required'],
            'stop' => ['required'],
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
