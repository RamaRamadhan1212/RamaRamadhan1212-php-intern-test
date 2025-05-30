<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'nomor' => 'required|string|unique:employees,nomor',
            'nama' => 'required|string',
            'jabatan' => 'nullable|string',
            'talahir' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ];
    }
    
}
