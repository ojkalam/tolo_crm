<?php

declare(strict_types=1);

namespace App\Http\Requests\Deal;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('deals.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'pipeline_id' => ['nullable', 'uuid', 'exists:pipelines,id'],
            'stage_id' => ['nullable', 'uuid', 'exists:pipeline_stages,id'],
            'company_id' => ['nullable', 'uuid', 'exists:companies,id'],
            'contact_id' => ['nullable', 'uuid', 'exists:contacts,id'],
            'assigned_to' => ['nullable', 'uuid', 'exists:users,id'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'expected_close_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:open,won,lost'],
            'custom_attributes' => ['nullable', 'array'],
        ];
    }
}
