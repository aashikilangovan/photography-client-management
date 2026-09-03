<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'required', 'integer', Rule::exists('clients', 'id')],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'project_date' => ['sometimes', 'required', 'date'],
            'status' => ['sometimes', Rule::in(['pending', 'in_progress', 'completed'])],
        ];
    }
}
