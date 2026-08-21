<?php

declare(strict_types=1);

namespace App\Http\Requests\Activity;

use App\Enums\ActivityType;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('activities.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'subjectable_type' => ['required', 'string', 'in:company,contact,lead,deal,App\Models\Company,App\Models\Contact,App\Models\Lead,App\Models\Deal'],
            'subjectable_id' => ['required', 'uuid'],
            'type' => ['required', new Enum(ActivityType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function getResolvedSubjectableType(): string
    {
        $type = strtolower((string) $this->input('subjectable_type'));

        return match ($type) {
            'company', 'app\models\company' => Company::class,
            'contact', 'app\models\contact' => Contact::class,
            'lead', 'app\models\lead' => Lead::class,
            'deal', 'app\models\deal' => Deal::class,
            default => (string) $this->input('subjectable_type'),
        };
    }
}
