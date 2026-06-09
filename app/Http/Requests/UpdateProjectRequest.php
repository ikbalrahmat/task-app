<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'year'        => 'required|integer|min:2020|max:2099',
            'status'      => 'required|in:Perencanaan,Berjalan,Selesai,Ditunda',
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date'   => 'nullable|date|after_or_equal:actual_start_date',
            'description'       => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Nama project wajib diisi.',
            'year.required'           => 'Tahun wajib diisi.',
            'status.required'         => 'Status wajib dipilih.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai.',
        ];
    }
}
