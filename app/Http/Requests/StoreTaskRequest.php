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
        ];
    }
}
