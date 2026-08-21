<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Timeline\TimelineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimelineController extends Controller
{
    public function index(Request $request, TimelineService $timelineService): JsonResponse
    {
        $request->validate([
            'subject_type' => ['required', 'string'],
            'subject_id' => ['required', 'uuid'],
        ]);

        $orgId = $request->user()->organization_id;
        $subjectType = $request->string('subject_type')->toString();
        $subjectId = $request->string('subject_id')->toString();
        $page = $request->integer('page', 1);
        $perPage = $request->integer('per_page', 20);

        $timeline = $timelineService->getTimeline($orgId, $subjectType, $subjectId, $page, $perPage);

        return response()->json($timeline);
    }
}
