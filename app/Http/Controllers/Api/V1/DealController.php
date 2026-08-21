<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Deal\CreateDealAction;
use App\Actions\Deal\DeleteDealAction;
use App\Actions\Deal\MoveDealStageAction;
use App\Actions\Deal\UpdateDealAction;
use App\Exports\DealsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Deal\MoveStageRequest;
use App\Http\Requests\Deal\StoreDealRequest;
use App\Http\Requests\Deal\UpdateDealRequest;
use App\Http\Resources\DealResource;
use App\Models\Deal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class DealController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orgId = $request->user()->organization_id;

        $deals = QueryBuilder::for(Deal::class)
            ->where('organization_id', $orgId)
            ->allowedFilters(
                'name',
                'status',
                'currency',
                AllowedFilter::exact('pipeline_id'),
                AllowedFilter::exact('stage_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('contact_id'),
                AllowedFilter::exact('assigned_to'),
                AllowedFilter::callback('min_amount', fn ($query, $value) => $query->where('amount', '>=', (float) $value)),
                AllowedFilter::callback('max_amount', fn ($query, $value) => $query->where('amount', '<=', (float) $value)),
            )
            ->allowedSorts('name', 'amount', 'expected_close_date', 'status', 'created_at')
            ->allowedIncludes('pipeline', 'stage', 'company', 'contact', 'assignedTo')
            ->with(['pipeline', 'stage', 'company', 'contact', 'assignedTo'])
            ->defaultSort('-created_at')
            ->paginate($request->integer('per_page', 15));

        return DealResource::collection($deals);
    }

    public function store(
        StoreDealRequest $request,
        CreateDealAction $action
    ): JsonResponse {
        $user = $request->user();
        $deal = $action->execute($user->organization, $request->validated(), $user);

        return response()->json([
            'message' => 'Deal created successfully',
            'data' => new DealResource($deal->load(['pipeline', 'stage', 'company', 'contact', 'assignedTo'])),
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request, Deal $deal): JsonResponse
    {
        abort_if($deal->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $deal->load(['pipeline', 'stage', 'company', 'contact', 'assignedTo']);

        return response()->json([
            'data' => new DealResource($deal),
        ]);
    }

    public function update(
        UpdateDealRequest $request,
        Deal $deal,
        UpdateDealAction $action
    ): JsonResponse {
        abort_if($deal->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $updatedDeal = $action->execute($deal, $request->validated());

        return response()->json([
            'message' => 'Deal updated successfully',
            'data' => new DealResource($updatedDeal),
        ]);
    }

    public function destroy(
        Request $request,
        Deal $deal,
        DeleteDealAction $action
    ): JsonResponse {
        abort_if(! $request->user()->can('deals.delete'), Response::HTTP_FORBIDDEN);
        abort_if($deal->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $action->execute($deal);

        return response()->json([
            'message' => 'Deal deleted successfully',
        ]);
    }

    public function moveStage(
        MoveStageRequest $request,
        Deal $deal,
        MoveDealStageAction $action
    ): JsonResponse {
        abort_if($deal->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $updatedDeal = $action->execute($deal, $request->validated('stage_id'), $request->user());

        return response()->json([
            'message' => 'Deal moved to new stage successfully',
            'data' => new DealResource($updatedDeal),
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        abort_if(! $request->user()->can('reports.export'), Response::HTTP_FORBIDDEN);

        $orgId = $request->user()->organization_id;
        $filename = 'deals_export_'.now()->format('Y_m_d_His').'.csv';

        return Excel::download(new DealsExport($orgId), $filename);
    }
}
