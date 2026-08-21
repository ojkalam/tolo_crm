<?php

declare(strict_types=1);

namespace App\Http\Requests\Pipeline;

use Illuminate\Foundation\Http\FormRequest;

class StorePipelineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('pipelines.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_default' => ['nullable', 'boolean'],
            'stages' => ['nullable', 'array', 'min:1'],
            'stages.*.name' => ['required_with:stages', 'string', 'max:255'],
            'stages.*.win_probability' => ['required_with:stages', 'integer', 'min:0', 'max:100'],
            'stages.*.order_column' => ['nullable', 'integer', 'min:0'],
            'stages.*.color_code' => ['nullable', 'string', 'max:30'],
        ];
    }
}
