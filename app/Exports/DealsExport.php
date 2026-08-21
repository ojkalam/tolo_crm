<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Deal;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DealsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected string $organizationId
    ) {}

    public function query(): Builder
    {
        return Deal::query()
            ->where('organization_id', $this->organizationId)
            ->with(['pipeline', 'stage', 'company', 'contact', 'assignedTo'])
            ->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return [
            'Deal ID',
            'Deal Name',
            'Amount',
            'Currency',
            'Status',
            'Pipeline',
            'Stage',
            'Company',
            'Contact Name',
            'Contact Email',
            'Assigned Rep',
            'Expected Close Date',
            'Created At',
        ];
    }

    /**
     * @param  Deal  $deal
     */
    public function map($deal): array
    {
        return [
            $deal->id,
            $deal->name,
            (float) $deal->amount,
            $deal->currency,
            $deal->status,
            $deal->pipeline?->name ?? 'N/A',
            $deal->stage?->name ?? 'N/A',
            $deal->company?->name ?? 'N/A',
            $deal->contact?->name ?? 'N/A',
            $deal->contact?->email ?? 'N/A',
            $deal->assignedTo?->name ?? 'Unassigned',
            $deal->expected_close_date?->toDateString() ?? 'N/A',
            $deal->created_at?->toIso8601String(),
        ];
    }
}
