<?php

declare(strict_types=1);

namespace App\Services\Search;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GlobalSearchService
{
    /**
     * @return array{query: string, total_count: int, results: array<string, list<array<string, mixed>>>, unified: list<array<string, mixed>>}
     */
    public function search(string $orgId, string $query, ?string $type = null, int $limit = 10): array
    {
        $cleanQuery = trim($query);

        if ($cleanQuery === '') {
            return [
                'query' => $query,
                'total_count' => 0,
                'results' => [
                    'contacts' => [],
                    'companies' => [],
                    'leads' => [],
                    'deals' => [],
                ],
                'unified' => [],
            ];
        }

        $results = [
            'contacts' => [],
            'companies' => [],
            'leads' => [],
            'deals' => [],
        ];

        // 1. Search Contacts
        if (! $type || in_array($type, ['all', 'contact', 'contacts'], true)) {
            $results['contacts'] = $this->searchContacts($orgId, $cleanQuery, $limit);
        }

        // 2. Search Companies
        if (! $type || in_array($type, ['all', 'company', 'companies'], true)) {
            $results['companies'] = $this->searchCompanies($orgId, $cleanQuery, $limit);
        }

        // 3. Search Leads
        if (! $type || in_array($type, ['all', 'lead', 'leads'], true)) {
            $results['leads'] = $this->searchLeads($orgId, $cleanQuery, $limit);
        }

        // 4. Search Deals
        if (! $type || in_array($type, ['all', 'deal', 'deals'], true)) {
            $results['deals'] = $this->searchDeals($orgId, $cleanQuery, $limit);
        }

        // Flatten into unified ranking list
        /** @var Collection<int, array<string, mixed>> $unified */
        $unified = collect($results)
            ->flatten(1)
            ->sortByDesc('rank')
            ->values()
            ->take($limit * 2)
            ->all();

        return [
            'query' => $query,
            'total_count' => count($unified),
            'results' => $results,
            'unified' => $unified,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchContacts(string $orgId, string $query, int $limit): array
    {
        // Try tsquery first
        $rows = DB::select("
            SELECT id, first_name, last_name, email, phone, job_title, department,
                   ts_rank(search_vector, plainto_tsquery('english', ?)) as rank
            FROM contacts
            WHERE organization_id = ?
              AND deleted_at IS NULL
              AND (search_vector @@ plainto_tsquery('english', ?)
                   OR first_name ILIKE ? OR last_name ILIKE ? OR email ILIKE ?)
            ORDER BY rank DESC, created_at DESC
            LIMIT ?
        ", [$query, $orgId, $query, "%{$query}%", "%{$query}%", "%{$query}%", $limit]);

        return array_map(function ($row) {
            $name = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));

            return [
                'id' => (string) $row->id,
                'entity_type' => 'contact',
                'title' => $name,
                'subtitle' => $row->job_title ? ($row->job_title . ($row->email ? " • {$row->email}" : '')) : ($row->email ?? $row->phone ?? ''),
                'email' => $row->email,
                'phone' => $row->phone,
                'rank' => (float) ($row->rank ?? 0.1),
            ];
        }, $rows);
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchCompanies(string $orgId, string $query, int $limit): array
    {
        $rows = DB::select("
            SELECT id, name, domain, industry, phone, website,
                   ts_rank(search_vector, plainto_tsquery('english', ?)) as rank
            FROM companies
            WHERE organization_id = ?
              AND deleted_at IS NULL
              AND (search_vector @@ plainto_tsquery('english', ?)
                   OR name ILIKE ? OR domain ILIKE ? OR industry ILIKE ?)
            ORDER BY rank DESC, created_at DESC
            LIMIT ?
        ", [$query, $orgId, $query, "%{$query}%", "%{$query}%", "%{$query}%", $limit]);

        return array_map(function ($row) {
            return [
                'id' => (string) $row->id,
                'entity_type' => 'company',
                'title' => (string) $row->name,
                'subtitle' => $row->industry ? ($row->industry . ($row->domain ? " • {$row->domain}" : '')) : ($row->domain ?? $row->website ?? ''),
                'domain' => $row->domain,
                'industry' => $row->industry,
                'rank' => (float) ($row->rank ?? 0.1),
            ];
        }, $rows);
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchLeads(string $orgId, string $query, int $limit): array
    {
        $rows = DB::select("
            SELECT id, first_name, last_name, company_name, email, phone, status, score,
                   ts_rank(search_vector, plainto_tsquery('english', ?)) as rank
            FROM leads
            WHERE organization_id = ?
              AND deleted_at IS NULL
              AND (search_vector @@ plainto_tsquery('english', ?)
                   OR first_name ILIKE ? OR last_name ILIKE ? OR company_name ILIKE ? OR email ILIKE ?)
            ORDER BY rank DESC, created_at DESC
            LIMIT ?
        ", [$query, $orgId, $query, "%{$query}%", "%{$query}%", "%{$query}%", "%{$query}%", $limit]);

        return array_map(function ($row) {
            $name = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''));

            return [
                'id' => (string) $row->id,
                'entity_type' => 'lead',
                'title' => $name,
                'subtitle' => $row->company_name ? ($row->company_name . ($row->status ? " • Status: {$row->status}" : '')) : "Status: {$row->status}",
                'status' => $row->status,
                'score' => (int) ($row->score ?? 0),
                'rank' => (float) ($row->rank ?? 0.1),
            ];
        }, $rows);
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function searchDeals(string $orgId, string $query, int $limit): array
    {
        $rows = DB::select("
            SELECT id, name, amount, currency, status,
                   ts_rank(search_vector, plainto_tsquery('english', ?)) as rank
            FROM deals
            WHERE organization_id = ?
              AND deleted_at IS NULL
              AND (search_vector @@ plainto_tsquery('english', ?)
                   OR name ILIKE ?)
            ORDER BY rank DESC, created_at DESC
            LIMIT ?
        ", [$query, $orgId, $query, "%{$query}%", $limit]);

        return array_map(function ($row) {
            $formattedAmount = number_format((float) ($row->amount ?? 0), 2);

            return [
                'id' => (string) $row->id,
                'entity_type' => 'deal',
                'title' => (string) $row->name,
                'subtitle' => "{$row->currency} {$formattedAmount} • Status: {$row->status}",
                'amount' => (float) ($row->amount ?? 0),
                'currency' => (string) ($row->currency ?? 'USD'),
                'status' => (string) ($row->status ?? 'open'),
                'rank' => (float) ($row->rank ?? 0.1),
            ];
        }, $rows);
    }
}
