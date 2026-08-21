<?php

declare(strict_types=1);

namespace App\Actions\Deal;

use App\Events\DealStageUpdated;
use App\Models\Deal;
use App\Models\PipelineStage;
use App\Models\User;
use Illuminate\Support\Str;

class MoveDealStageAction
{
    public function execute(Deal $deal, string $newStageId, ?User $actor = null): Deal
    {
        $previousStageId = $deal->stage_id;

        if ($previousStageId === $newStageId) {
            return $deal;
        }

        /** @var PipelineStage $newStage */
        $newStage = PipelineStage::where('id', $newStageId)
            ->where('pipeline_id', $deal->pipeline_id)
            ->firstOrFail();

        $stageNameLower = strtolower($newStage->name);
        $status = 'open';

        if (Str::contains($stageNameLower, 'won') || $newStage->win_probability === 100) {
            $status = 'won';
        } elseif (Str::contains($stageNameLower, 'lost') || $newStage->win_probability === 0) {
            $status = 'lost';
        }

        $deal->update([
            'stage_id' => $newStage->id,
            'status' => $status,
        ]);

        $freshDeal = $deal->fresh(['pipeline', 'stage', 'company', 'contact', 'assignedTo']);

        // Dispatch WebSocket Real-time Broadcast
        event(new DealStageUpdated($freshDeal, $previousStageId, $actor));

        return $freshDeal;
    }
}
