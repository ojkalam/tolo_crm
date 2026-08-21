<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Search\GlobalSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request, GlobalSearchService $searchService): JsonResponse
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'in:all,contact,contacts,company,companies,lead,leads,deal,deals'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $orgId = $request->user()->organization_id;
        $query = (string) $request->input('q', '');
        $type = $request->input('type');
        $limit = $request->integer('limit', 10);

        $results = $searchService->search($orgId, $query, $type, $limit);

        return response()->json($results);
    }
}
