<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubprojectRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'project_id'        => 'required|exists:projects,id',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date'   => 'nullable|date|after_or_equal:actual_start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama sub-project wajib diisi.',
        ];
    }
}
