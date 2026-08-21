<?php

declare(strict_types=1);

namespace App\Http\Requests\Activity;

use Illuminate\Foundation\Http\FormRequest;

class CompleteActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('activities.complete') ?? false;
    }

    public function rules(): array
    {
        return [
            'completed_at' => ['nullable', 'date'],
            'outcome' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
