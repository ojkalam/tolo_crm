<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Pipeline\CreatePipelineAction;
use App\Actions\Pipeline\ReorderStagesAction;
use App\Actions\Pipeline\UpdatePipelineAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pipeline\ReorderStagesRequest;
use App\Http\Requests\Pipeline\StorePipelineRequest;
use App\Http\Requests\Pipeline\UpdatePipelineRequest;
use App\Http\Resources\DealResource;
use App\Http\Resources\PipelineResource;
use App\Models\Deal;
use App\Models\Pipeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class PipelineController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orgId = $request->user()->organization_id;

        $pipelines = Pipeline::where('organization_id', $orgId)
            ->with(['stages' => fn ($q) => $q->orderBy('order_column')])
            ->withCount('deals')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return PipelineResource::collection($pipelines);
    }

    public function store(
        StorePipelineRequest $request,
        CreatePipelineAction $action
    ): JsonResponse {
        $pipeline = $action->execute($request->user()->organization, $request->validated());

        return response()->json([
            'message' => 'Pipeline created successfully',
            'data' => new PipelineResource($pipeline),
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request, Pipeline $pipeline): JsonResponse
    {
        abort_if($pipeline->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $pipeline->load(['stages' => fn ($q) => $q->orderBy('order_column')])->loadCount('deals');

        return response()->json([
            'data' => new PipelineResource($pipeline),
        ]);
    }

    public function update(
        UpdatePipelineRequest $request,
        Pipeline $pipeline,
        UpdatePipelineAction $action
    ): JsonResponse {
        abort_if($pipeline->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $updatedPipeline = $action->execute($pipeline, $request->validated());

        return response()->json([
            'message' => 'Pipeline updated successfully',
            'data' => new PipelineResource($updatedPipeline),
        ]);
    }

    public function destroy(Request $request, Pipeline $pipeline): JsonResponse
    {
        abort_if(! $request->user()->can('pipelines.delete'), Response::HTTP_FORBIDDEN);
        abort_if($pipeline->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $pipeline->delete();

        return response()->json([
            'message' => 'Pipeline deleted successfully',
        ]);
    }

    public function reorderStages(
        ReorderStagesRequest $request,
        Pipeline $pipeline,
        ReorderStagesAction $action
    ): JsonResponse {
        abort_if($pipeline->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $updatedPipeline = $action->execute($pipeline, $request->validated('stages'));

        return response()->json([
            'message' => 'Stages reordered successfully',
            'data' => new PipelineResource($updatedPipeline),
        ]);
    }

    public function kanban(Request $request, Pipeline $pipeline): JsonResponse
    {
        abort_if($pipeline->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $stages = $pipeline->stages()->orderBy('order_column')->get();

        $kanbanColumns = $stages->map(function ($stage) use ($pipeline) {
            $deals = Deal::where('pipeline_id', $pipeline->id)
                ->where('stage_id', $stage->id)
                ->with(['company', 'contact', 'assignedTo'])
                ->orderByDesc('created_at')
                ->get();

            $totalValue = (float) $deals->sum('amount');
            $weightedValue = (float) $deals->sum(fn ($d) => ((float) $d->amount) * ($stage->win_probability / 100));

            return [
                'id' => $stage->id,
                'name' => $stage->name,
                'win_probability' => $stage->win_probability,
                'order_column' => $stage->order_column,
                'color_code' => $stage->color_code,
                'deals_count' => $deals->count(),
                'total_value' => $totalValue,
                'weighted_value' => $weightedValue,
                'deals' => DealResource::collection($deals),
            ];
        });

        $totalPipelineValue = (float) Deal::where('pipeline_id', $pipeline->id)->where('status', 'open')->sum('amount');

        return response()->json([
            'pipeline' => [
                'id' => $pipeline->id,
                'name' => $pipeline->name,
                'is_default' => $pipeline->is_default,
                'total_pipeline_value' => $totalPipelineValue,
            ],
            'columns' => $kanbanColumns,
        ]);
    }
}
