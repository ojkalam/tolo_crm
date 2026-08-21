<?php

declare(strict_types=1);

namespace App\Http\Requests\Deal;

use Illuminate\Foundation\Http\FormRequest;

class MoveStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('deals.move_stage') ?? false;
    }

    public function rules(): array
    {
        return [
            'stage_id' => ['required', 'uuid', 'exists:pipeline_stages,id'],
        ];
    }
}
