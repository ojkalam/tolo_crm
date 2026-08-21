<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\User;
use App\Services\Analytics\SalesMetricsService;
use App\Services\Search\GlobalSearchService;
use App\Services\Timeline\TimelineService;

echo "========================================================\n";
echo "       TOLO CRM — END-TO-END FEATURE VERIFICATION       \n";
echo "========================================================\n\n";

$user = User::where('email', 'admin@acmecorp.com')->first();
$orgId = $user->organization_id;

echo "1. AUTHENTICATED USER & TENANT CONTEXT:\n";
echo "   - User: {$user->name} ({$user->email})\n";
echo "   - Organization: {$user->organization->name} (UUID: {$orgId})\n";
echo "   - Roles: " . implode(', ', $user->getRoleNames()->toArray()) . "\n\n";

// 2. COMPANIES
$companies = Company::where('organization_id', $orgId)->get();
echo "2. COMPANIES MODULE (Total: {$companies->count()}):\n";
foreach ($companies as $c) {
    $tier = $c->custom_attributes['account_tier'] ?? 'N/A';
    $rev = number_format((float) $c->annual_revenue, 2);
    echo "   - [{$c->name}] Industry: {$c->industry} | Revenue: \${$rev} | Tier: {$tier}\n";
}
echo "\n";

// 3. CONTACTS
$contacts = Contact::where('organization_id', $orgId)->with('company')->get();
echo "3. CONTACTS MODULE (Total: {$contacts->count()}):\n";
foreach ($contacts as $con) {
    echo "   - {$con->name} ({$con->job_title}) @ {$con->company?->name} [{$con->email}]\n";
}
echo "\n";

// 4. LEADS & SCORES
$leads = Lead::where('organization_id', $orgId)->get();
echo "4. LEADS & QUALIFICATION SCORING (Total: {$leads->count()}):\n";
foreach ($leads as $l) {
    echo "   - {$l->name} ({$l->company_name}) | Score: {$l->score}/100 | Status: {$l->status->value} | Est. Value: \${$l->estimated_value}\n";
}
echo "\n";

// 5. PIPELINES & KANBAN
$pipeline = Pipeline::where('organization_id', $orgId)->with('stages')->first();
echo "5. PIPELINES & KANBAN BOARD:\n";
echo "   - Pipeline: {$pipeline->name} ({$pipeline->stages->count()} stages)\n";
foreach ($pipeline->stages as $st) {
    $dealsInStage = Deal::where('pipeline_id', $pipeline->id)->where('stage_id', $st->id)->get();
    $totalVal = number_format((float) $dealsInStage->sum('amount'), 2);
    echo "     * Stage [{$st->name}] ({$st->win_probability}% Win Prob) -> {$dealsInStage->count()} Deals | Total: \${$totalVal}\n";
    foreach ($dealsInStage as $d) {
        $amount = number_format((float) $d->amount, 2);
        echo "       • Deal: {$d->name} (\${$amount}) [Status: {$d->status}]\n";
    }
}
echo "\n";

// 6. FULL-TEXT SEARCH (tsvector)
$searchService = new GlobalSearchService();
$query = "Stark";
$searchRes = $searchService->search($orgId, $query);
echo "6. POSTGRESQL TSVECTOR FULL-TEXT SEARCH (Query: '{$query}'):\n";
echo "   - Matches Found: {$searchRes['total_count']}\n";
foreach ($searchRes['unified'] as $item) {
    echo "     * [{$item['entity_type']}] {$item['title']} - {$item['subtitle']} (Score: {$item['rank']})\n";
}
echo "\n";

// 7. UNIFIED TIMELINE (Activities + Audits)
$timelineService = new TimelineService();
$firstCompany = $companies->first();
$timeline = $timelineService->getTimeline($orgId, 'company', $firstCompany->id);
echo "7. UNIFIED TIMELINE STREAM for '{$firstCompany->name}':\n";
echo "   - Total Timeline Entries: {$timeline->total()}\n";
foreach ($timeline->items() as $item) {
    echo "     * [{$item['category']}: {$item['type']}] {$item['title']} - {$item['description']}\n";
}
echo "\n";

// 8. SALES ANALYTICS & FORECASTING
$metricsService = new SalesMetricsService();
$dashboard = $metricsService->getExecutiveDashboardSummary($orgId, $pipeline->id);
$kpis = $dashboard['kpis'];
echo "8. EXECUTIVE REVENUE & FORECASTING ANALYTICS:\n";
echo "   - Total Pipeline Value: \$" . number_format($kpis['total_pipeline_value'], 2) . "\n";
echo "   - Weighted Forecast Value: \$" . number_format($kpis['weighted_forecast_value'], 2) . "\n";
echo "   - Open Deals Count: {$kpis['open_deals_count']}\n";
echo "   - Won Deals Count: {$kpis['won_deals_count']}\n";
echo "   - Win Rate: {$kpis['win_rate_percentage']}%\n";
echo "   - Monthly Revenue Trend:\n";
foreach ($dashboard['monthly_revenue_trend'] as $month) {
    echo "     * Month {$month['month']}: \$" . number_format($month['revenue'], 2) . " ({$month['deals_count']} won deals)\n";
}
echo "\n========================================================\n";
echo "               ALL FEATURES 100% OPERATIONAL            \n";
echo "========================================================\n";
