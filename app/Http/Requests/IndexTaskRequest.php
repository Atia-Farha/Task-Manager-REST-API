<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'in:pending,in_progress,completed'],
            'priority' => ['sometimes', 'in:low,medium,high'],
            'sort_by' => ['sometimes', 'in:created_at,title'],
            'sort_order' => ['sometimes', 'in:asc,desc'],
        ];
    }
}
