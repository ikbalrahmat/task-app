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
            'start_date'        => 'nullable|date',
            'end_date'          => 'nullable|date|after_or_equal:start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date'   => 'nullable|date|after_or_equal:actual_start_date',
            'actual_start_remarks' => [
                \Illuminate\Validation\Rule::requiredIf(function () {
                    return $this->filled('actual_start_date') && $this->input('actual_start_date') !== $this->input('start_date');
                }),
                'nullable',
                'string',
            ],
            'actual_end_remarks' => [
                \Illuminate\Validation\Rule::requiredIf(function () {
                    return $this->filled('actual_end_date') && $this->input('actual_end_date') !== $this->input('end_date');
                }),
                'nullable',
                'string',
            ],
            'description'       => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Nama project wajib diisi.',
            'year.required'           => 'Tahun wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai.',
            'actual_start_remarks.required' => 'Keterangan Realisasi Mulai wajib diisi jika tanggal realisasi berbeda dengan rencana.',
            'actual_end_remarks.required' => 'Keterangan Realisasi Selesai wajib diisi jika tanggal realisasi berbeda dengan rencana.',
        ];
    }
}
