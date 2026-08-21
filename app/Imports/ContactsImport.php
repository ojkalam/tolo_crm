<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ContactsImport implements ToCollection, WithChunkReading, WithHeadingRow, WithValidation
{
    public function __construct(
        protected Organization $organization,
        protected ?User $importedBy = null
    ) {}

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     */
    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $companyId = null;

            if (! empty($row['company_name'])) {
                $company = Company::firstOrCreate(
                    [
                        'organization_id' => $this->organization->id,
                        'name' => trim((string) $row['company_name']),
                    ],
                    [
                        'owner_id' => $this->importedBy?->id,
                    ]
                );
                $companyId = $company->id;
            }

            Contact::updateOrCreate(
                [
                    'organization_id' => $this->organization->id,
                    'email' => ! empty($row['email']) ? trim((string) $row['email']) : null,
                ],
                [
                    'company_id' => $companyId,
                    'assigned_user_id' => $this->importedBy?->id,
                    'first_name' => trim((string) ($row['first_name'] ?? 'Unknown')),
                    'last_name' => trim((string) ($row['last_name'] ?? '')),
                    'phone' => ! empty($row['phone']) ? (string) $row['phone'] : null,
                    'mobile' => ! empty($row['mobile']) ? (string) $row['mobile'] : null,
                    'job_title' => ! empty($row['job_title']) ? (string) $row['job_title'] : null,
                    'department' => ! empty($row['department']) ? (string) $row['department'] : null,
                    'lifecycle_stage' => ! empty($row['lifecycle_stage']) ? (string) $row['lifecycle_stage'] : 'lead',
                ]
            );
        }
    }

    public function rules(): array
    {
        return [
            '*.first_name' => ['required', 'string', 'max:255'],
            '*.last_name' => ['nullable', 'string', 'max:255'],
            '*.email' => ['nullable', 'email', 'max:255'],
            '*.phone' => ['nullable'],
            '*.company_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
