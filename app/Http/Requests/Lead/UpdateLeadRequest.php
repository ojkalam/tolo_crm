<?php

declare(strict_types=1);

namespace App\Http\Requests\Lead;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('leads.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'assigned_user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', new Enum(LeadStatus::class)],
            'source' => ['nullable', new Enum(LeadSource::class)],
            'score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'custom_attributes' => ['nullable', 'array'],
        ];
    }
}
