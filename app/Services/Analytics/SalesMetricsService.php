<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SalesMetricsService
{
    /**
     * @return array<string, mixed>
     */
    public function getExecutiveDashboardSummary(string $orgId, ?string $pipelineId = null): array
    {
        return [
            'kpis' => $this->getPipelineKPIs($orgId, $pipelineId),
            'stage_funnel' => $this->getFunnelBreakdown($orgId, $pipelineId),
            'lead_velocity' => $this->getLeadConversionVelocity($orgId),
            'rep_performance' => $this->getRepPerformanceMatrix($orgId),
            'monthly_revenue_trend' => $this->getMonthlyRevenueTrend($orgId),
        ];
    }

    /**
     * @return array{total_pipeline_value: float, weighted_forecast_value: float, average_deal_size: float, open_deals_count: int, won_deals_count: int, lost_deals_count: int, win_rate_percentage: float}
     */
    public function getPipelineKPIs(string $orgId, ?string $pipelineId = null): array
    {
        $query = DB::table('deals')
            ->leftJoin('pipeline_stages', 'deals.stage_id', '=', 'pipeline_stages.id')
            ->where('deals.organization_id', $orgId)
            ->whereNull('deals.deleted_at');

        if ($pipelineId) {
            $query->where('deals.pipeline_id', $pipelineId);
        }

        $stats = $query->selectRaw("
            COALESCE(SUM(CASE WHEN deals.status = 'open' THEN deals.amount ELSE 0 END), 0) as total_pipeline_value,
            COALESCE(SUM(CASE WHEN deals.status = 'open' THEN deals.amount * (COALESCE(pipeline_stages.win_probability, 0)::numeric / 100.0) ELSE 0 END), 0) as weighted_forecast_value,
            COALESCE(AVG(CASE WHEN deals.status = 'open' THEN deals.amount ELSE NULL END), 0) as average_deal_size,
            COUNT(CASE WHEN deals.status = 'open' THEN 1 ELSE NULL END) as open_deals_count,
            COUNT(CASE WHEN deals.status = 'won' THEN 1 ELSE NULL END) as won_deals_count,
            COUNT(CASE WHEN deals.status = 'lost' THEN 1 ELSE NULL END) as lost_deals_count
        ")->first();

        $openDeals = (int) ($stats->open_deals_count ?? 0);
        $wonDeals = (int) ($stats->won_deals_count ?? 0);
        $lostDeals = (int) ($stats->lost_deals_count ?? 0);
        $closedTotal = $wonDeals + $lostDeals;
        $winRate = $closedTotal > 0 ? round(($wonDeals / $closedTotal) * 100, 2) : 0.0;

        return [
            'total_pipeline_value' => (float) ($stats->total_pipeline_value ?? 0),
            'weighted_forecast_value' => round((float) ($stats->weighted_forecast_value ?? 0), 2),
            'average_deal_size' => round((float) ($stats->average_deal_size ?? 0), 2),
            'open_deals_count' => $openDeals,
            'won_deals_count' => $wonDeals,
            'lost_deals_count' => $lostDeals,
            'win_rate_percentage' => $winRate,
        ];
    }

    /**
     * @return list<array{stage_id: string, stage_name: string, win_probability: int, order_column: int, color_code: string, deals_count: int, total_value: float, weighted_value: float}>
     */
    public function getFunnelBreakdown(string $orgId, ?string $pipelineId = null): array
    {
        $query = DB::table('pipeline_stages')
            ->join('pipelines', 'pipeline_stages.pipeline_id', '=', 'pipelines.id')
            ->leftJoin('deals', function ($join) {
                $join->on('pipeline_stages.id', '=', 'deals.stage_id')
                    ->whereNull('deals.deleted_at');
            })
            ->where('pipelines.organization_id', $orgId)
            ->whereNull('pipeline_stages.deleted_at');

        if ($pipelineId) {
            $query->where('pipelines.id', $pipelineId);
        } else {
            $query->where('pipelines.is_default', true);
        }

        $rows = $query->selectRaw("
            pipeline_stages.id as stage_id,
            pipeline_stages.name as stage_name,
            pipeline_stages.win_probability,
            pipeline_stages.order_column,
            pipeline_stages.color_code,
            COUNT(deals.id) as deals_count,
            COALESCE(SUM(deals.amount), 0) as total_value,
            COALESCE(SUM(deals.amount * (pipeline_stages.win_probability::numeric / 100.0)), 0) as weighted_value
        ")
            ->groupBy(
                'pipeline_stages.id',
                'pipeline_stages.name',
                'pipeline_stages.win_probability',
                'pipeline_stages.order_column',
                'pipeline_stages.color_code'
            )
            ->orderBy('pipeline_stages.order_column')
            ->get();

        return $rows->map(function ($row) {
            return [
                'stage_id' => (string) $row->stage_id,
                'stage_name' => (string) $row->stage_name,
                'win_probability' => (int) $row->win_probability,
                'order_column' => (int) $row->order_column,
                'color_code' => (string) ($row->color_code ?? '#3B82F6'),
                'deals_count' => (int) $row->deals_count,
                'total_value' => (float) $row->total_value,
                'weighted_value' => round((float) $row->weighted_value, 2),
            ];
        })->all();
    }

    /**
     * @return array{total_leads: int, converted_leads: int, conversion_rate: float, average_conversion_days: float, average_conversion_hours: float}
     */
    public function getLeadConversionVelocity(string $orgId): array
    {
        $stats = DB::table('leads')
            ->where('organization_id', $orgId)
            ->whereNull('deleted_at')
            ->selectRaw("
                COUNT(*) as total_leads,
                COUNT(CASE WHEN status = 'converted' THEN 1 ELSE NULL END) as converted_leads,
                COALESCE(AVG(CASE WHEN status = 'converted' AND converted_at IS NOT NULL
                    THEN EXTRACT(EPOCH FROM (converted_at - created_at)) / 86400.0 ELSE NULL END), 0) as avg_days
            ")
            ->first();

        $totalLeads = (int) ($stats->total_leads ?? 0);
        $convertedLeads = (int) ($stats->converted_leads ?? 0);
        $conversionRate = $totalLeads > 0 ? round(($convertedLeads / $totalLeads) * 100, 2) : 0.0;
        $avgDays = round((float) ($stats->avg_days ?? 0), 2);

        return [
            'total_leads' => $totalLeads,
            'converted_leads' => $convertedLeads,
            'conversion_rate' => $conversionRate,
            'average_conversion_days' => $avgDays,
            'average_conversion_hours' => round($avgDays * 24, 1),
        ];
    }

    /**
     * @return list<array{user_id: string, name: string, email: string, won_deals_count: int, won_revenue: float, open_deals_count: int, open_pipeline_value: float, completed_activities_count: int}>
     */
    public function getRepPerformanceMatrix(string $orgId): array
    {
        $users = User::where('organization_id', $orgId)->get();

        return $users->map(function (User $user) {
            $dealStats = DB::table('deals')
                ->where('assigned_to', $user->id)
                ->whereNull('deleted_at')
                ->selectRaw("
                    COUNT(CASE WHEN status = 'won' THEN 1 ELSE NULL END) as won_count,
                    COALESCE(SUM(CASE WHEN status = 'won' THEN amount ELSE 0 END), 0) as won_revenue,
                    COUNT(CASE WHEN status = 'open' THEN 1 ELSE NULL END) as open_count,
                    COALESCE(SUM(CASE WHEN status = 'open' THEN amount ELSE 0 END), 0) as open_revenue
                ")->first();

            $completedActivities = Activity::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count();

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'won_deals_count' => (int) ($dealStats->won_count ?? 0),
                'won_revenue' => (float) ($dealStats->won_revenue ?? 0),
                'open_deals_count' => (int) ($dealStats->open_count ?? 0),
                'open_pipeline_value' => (float) ($dealStats->open_revenue ?? 0),
                'completed_activities_count' => $completedActivities,
            ];
        })->sortByDesc('won_revenue')->values()->all();
    }

    /**
     * @return list<array{month: string, revenue: float, deals_count: int}>
     */
    public function getMonthlyRevenueTrend(string $orgId): array
    {
        $rows = DB::table('deals')
            ->where('organization_id', $orgId)
            ->where('status', 'won')
            ->whereNull('deleted_at')
            ->selectRaw("
                TO_CHAR(DATE_TRUNC('month', updated_at), 'YYYY-MM') as month,
                COALESCE(SUM(amount), 0) as revenue,
                COUNT(*) as deals_count
            ")
            ->groupBy(DB::raw("DATE_TRUNC('month', updated_at), TO_CHAR(DATE_TRUNC('month', updated_at), 'YYYY-MM')"))
            ->orderBy(DB::raw("DATE_TRUNC('month', updated_at)"))
            ->limit(12)
            ->get();

        return $rows->map(function ($row) {
            return [
                'month' => (string) $row->month,
                'revenue' => (float) $row->revenue,
                'deals_count' => (int) $row->deals_count,
            ];
        })->all();
    }
}
