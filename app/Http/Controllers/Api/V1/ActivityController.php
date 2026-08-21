<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Activity\CompleteActivityAction;
use App\Actions\Activity\DeleteActivityAction;
use App\Actions\Activity\LogActivityAction;
use App\Actions\Activity\UpdateActivityAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Activity\CompleteActivityRequest;
use App\Http\Requests\Activity\StoreActivityRequest;
use App\Http\Requests\Activity\UpdateActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;

class ActivityController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $orgId = $request->user()->organization_id;

        $activities = QueryBuilder::for(Activity::class)
            ->where('organization_id', $orgId)
            ->allowedFilters(
                'type',
                'title',
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('subjectable_type'),
                AllowedFilter::exact('subjectable_id'),
                AllowedFilter::callback('is_completed', function ($query, $value) {
                    if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
                        $query->whereNotNull('completed_at');
                    } else {
                        $query->whereNull('completed_at');
                    }
                }),
            )
            ->allowedSorts('due_date', 'completed_at', 'created_at')
            ->allowedIncludes('user', 'subjectable')
            ->with(['user', 'subjectable'])
            ->defaultSort('-created_at')
            ->paginate($request->integer('per_page', 15));

        return ActivityResource::collection($activities);
    }

    public function store(
        StoreActivityRequest $request,
        LogActivityAction $action
    ): JsonResponse {
        $data = $request->validated();
        $data['subjectable_type'] = $request->getResolvedSubjectableType();

        $activity = $action->execute($request->user()->organization, $data, $request->user());

        return response()->json([
            'message' => 'Activity logged successfully',
            'data' => new ActivityResource($activity->load(['user', 'subjectable'])),
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request, Activity $activity): JsonResponse
    {
        abort_if($activity->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $activity->load(['user', 'subjectable']);

        return response()->json([
            'data' => new ActivityResource($activity),
        ]);
    }

    public function update(
        UpdateActivityRequest $request,
        Activity $activity,
        UpdateActivityAction $action
    ): JsonResponse {
        abort_if($activity->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $updatedActivity = $action->execute($activity, $request->validated());

        return response()->json([
            'message' => 'Activity updated successfully',
            'data' => new ActivityResource($updatedActivity),
        ]);
    }

    public function complete(
        CompleteActivityRequest $request,
        Activity $activity,
        CompleteActivityAction $action
    ): JsonResponse {
        abort_if($activity->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $completedActivity = $action->execute($activity, $request->validated());

        return response()->json([
            'message' => 'Activity marked as completed',
            'data' => new ActivityResource($completedActivity),
        ]);
    }

    public function destroy(
        Request $request,
        Activity $activity,
        DeleteActivityAction $action
    ): JsonResponse {
        abort_if(! $request->user()->can('activities.delete'), Response::HTTP_FORBIDDEN);
        abort_if($activity->organization_id !== $request->user()->organization_id, Response::HTTP_NOT_FOUND);

        $action->execute($activity);

        return response()->json([
            'message' => 'Activity deleted successfully',
        ]);
    }
}
