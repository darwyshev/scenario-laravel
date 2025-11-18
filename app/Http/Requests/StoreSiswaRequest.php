<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'tb' => 'required|numeric|min:30|max:250',
            'bb' => 'required|numeric|min:10|max:200',
        ];
    }
}
