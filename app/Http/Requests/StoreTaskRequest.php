<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'project_id'  => 'required|exists:projects,id',
            'subproject_id' => 'nullable|exists:subprojects,id',
            'name'        => 'required|string|max:255',
            'pic_ids'     => 'nullable|array',
            'pic_ids.*'   => 'exists:users,id',
            'start_date'        => 'nullable|date',
            'due_date'          => 'nullable|date|after_or_equal:start_date',
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
                    return $this->filled('actual_end_date') && $this->input('actual_end_date') !== $this->input('due_date');
                }),
                'nullable',
                'string',
            ],
            'progress'          => 'required|integer|min:0|max:100',
            'status'            => 'required|in:Belum Mulai,Berjalan,Selesai,Overdue',
            'description'       => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'project_id.required' => 'Project wajib dipilih.',
            'name.required'       => 'Nama task wajib diisi.',
            'progress.min'        => 'Progress minimal 0%.',
            'progress.max'        => 'Progress maksimal 100%.',
            'actual_start_remarks.required' => 'Keterangan Realisasi Mulai wajib diisi jika tanggal realisasi berbeda dengan rencana.',
            'actual_end_remarks.required' => 'Keterangan Realisasi Selesai wajib diisi jika tanggal realisasi berbeda dengan rencana.',
        ];
    }
}
