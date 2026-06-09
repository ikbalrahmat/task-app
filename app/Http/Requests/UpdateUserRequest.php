<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->route('user');
        return [
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $userId,
            'password'   => 'nullable|string|min:8|confirmed',
            'role'       => 'required|in:Admin,Pengendali Teknis,Ketua Tim,Anggota Tim',
            'department' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan.',
            'role.required'  => 'Role wajib dipilih.',
        ];
    }
}
