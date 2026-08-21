<?php

declare(strict_types=1);

namespace App\Http\Requests\Pipeline;

use Illuminate\Foundation\Http\FormRequest;

class ReorderStagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('pipelines.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'stages' => ['required', 'array', 'min:1'],
            'stages.*.id' => ['required', 'uuid', 'exists:pipeline_stages,id'],
            'stages.*.order_column' => ['required', 'integer', 'min:1'],
        ];
    }
}
