<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string|max:50|unique:dataadmin,username',
            'password' => 'required|string|min:4',
            'role'     => 'required|string|in:admin,guru,siswa',
            'nama'     => 'required|string|max:100',
            'tb'       => 'nullable|numeric',
            'bb'       => 'nullable|numeric',
            'mapel'    => 'nullable|string|max:100',
        ];
    }
}
