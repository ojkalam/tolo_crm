<?php

declare(strict_types=1);

namespace App\Http\Requests\Lead;

use Illuminate\Foundation\Http\FormRequest;

class ConvertLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('leads.convert') ?? false;
    }

    public function rules(): array
    {
        return [
            'company' => ['nullable', 'array'],
            'company.name' => ['nullable', 'string', 'max:255'],
            'company.domain' => ['nullable', 'string', 'max:255'],
            'company.industry' => ['nullable', 'string', 'max:100'],
            'contact' => ['nullable', 'array'],
            'contact.first_name' => ['nullable', 'string', 'max:255'],
            'contact.last_name' => ['nullable', 'string', 'max:255'],
            'contact.email' => ['nullable', 'string', 'email', 'max:255'],
            'contact.phone' => ['nullable', 'string', 'max:50'],
            'create_deal' => ['nullable', 'boolean'],
            'deal' => ['nullable', 'array'],
            'deal.name' => ['nullable', 'string', 'max:255'],
            'deal.amount' => ['nullable', 'numeric', 'min:0'],
            'deal.pipeline_id' => ['nullable', 'uuid', 'exists:pipelines,id'],
            'deal.stage_id' => ['nullable', 'uuid', 'exists:pipeline_stages,id'],
            'deal.expected_close_date' => ['nullable', 'date'],
        ];
    }
}
