<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Lead\ConvertLeadAction;
use App\Actions\Lead\CreateLeadAction;
use App\Actions\Lead\DeleteLeadAction;
use App\Actions\Lead\UpdateLeadAction;
use App\DTOs\ConvertLeadDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lead\ConvertLeadRequest;
use App\Http\Requests\Lead\StoreLeadRequest;
use App\Http\Requests\Lead\UpdateLeadRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\ContactResource;
use App\Http\Resources\DealResource;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class LeadController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orgId = $request->user()->organization_id;

        $leads = QueryBuilder::for(Lead::class)
            ->where('organization_id', $orgId)
            ->allowedFilters(
                'first_name',
                'last_name',
                'company_name',
                'email',
                'status',
                'source',
                AllowedFilter::exact('assigned_user_id'),
                AllowedFilter::callback('min_score', fn ($query, $value) => $query->where('score', '>=', (int) $value)),
            )
            ->allowedSorts('first_name', 'last_name', 'score', 'estimated_value', 'status', 'created_at')
            ->allowedIncludes('assignedUser')
            ->with('assignedUser')
            ->withCount('activities')
            ->defaultSort('-created_at')
            ->paginate($request->integer('per_page', 15));

        return LeadResource::collection($leads);
    }

    public function store(
        StoreLeadRequest $request,
        CreateLeadAction $action
    ): JsonResponse {
        $user = $request->user();
        $lead = $action->execute($user->organization, $request->validated(), $user);

        return response()->json([
            'message' => 'Lead created successfully',
            'data' => new LeadResource($lead),
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request, Lead $lead): JsonResponse
    {
        abort_if($lead->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $lead->load('assignedUser')->loadCount('activities');

        return response()->json([
            'data' => new LeadResource($lead),
        ]);
    }

    public function update(
        UpdateLeadRequest $request,
        Lead $lead,
        UpdateLeadAction $action
    ): JsonResponse {
        abort_if($lead->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $updatedLead = $action->execute($lead, $request->validated());

        return response()->json([
            'message' => 'Lead updated successfully',
            'data' => new LeadResource($updatedLead),
        ]);
    }

    public function destroy(
        Request $request,
        Lead $lead,
        DeleteLeadAction $action
    ): JsonResponse {
        abort_if(! $request->user()->can('leads.delete'), Response::HTTP_FORBIDDEN);
        abort_if($lead->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $action->execute($lead);

        return response()->json([
            'message' => 'Lead deleted successfully',
        ]);
    }

    public function convert(
        ConvertLeadRequest $request,
        Lead $lead,
        ConvertLeadAction $action
    ): JsonResponse {
        abort_if($lead->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $dto = ConvertLeadDTO::fromArray($request->validated());
        $result = $action->execute($lead, $dto, $request->user());

        return response()->json([
            'message' => 'Lead converted successfully',
            'data' => [
                'company' => new CompanyResource($result['company']),
                'contact' => new ContactResource($result['contact']),
                'deal' => $result['deal'] ? new DealResource($result['deal']) : null,
                'lead' => new LeadResource($result['lead']),
            ],
        ]);
    }
}
