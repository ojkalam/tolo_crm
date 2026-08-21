<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Analytics\SalesMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsDashboardController extends Controller
{
    public function __construct(
        protected SalesMetricsService $metricsService
    ) {}

    public function dashboard(Request $request): JsonResponse
    {
        abort_if(! $request->user()->can('reports.view'), 403);

        $orgId = $request->user()->organization_id;
        $pipelineId = $request->string('pipeline_id')->toString() ?: null;

        $summary = $this->metricsService->getExecutiveDashboardSummary($orgId, $pipelineId);

        return response()->json([
            'data' => $summary,
        ]);
    }

    public function pipelineForecast(Request $request): JsonResponse
    {
        abort_if(! $request->user()->can('reports.view'), 403);

        $orgId = $request->user()->organization_id;
        $pipelineId = $request->string('pipeline_id')->toString() ?: null;

        return response()->json([
            'kpis' => $this->metricsService->getPipelineKPIs($orgId, $pipelineId),
            'funnel' => $this->metricsService->getFunnelBreakdown($orgId, $pipelineId),
        ]);
    }

    public function repPerformance(Request $request): JsonResponse
    {
        abort_if(! $request->user()->can('reports.view'), 403);

        $orgId = $request->user()->organization_id;

        return response()->json([
            'data' => $this->metricsService->getRepPerformanceMatrix($orgId),
        ]);
    }
}
