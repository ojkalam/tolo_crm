<?php

declare(strict_types=1);

namespace App\Actions\Pipeline;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Support\Facades\DB;

class ReorderStagesAction
{
    /**
     * @param list<array{id: string, order_column: int}> $stages
     */
    public function execute(Pipeline $pipeline, array $stages): Pipeline
    {
        DB::transaction(function () use ($pipeline, $stages) {
            foreach ($stages as $stageData) {
                PipelineStage::where('id', $stageData['id'])
                    ->where('pipeline_id', $pipeline->id)
                    ->update(['order_column' => $stageData['order_column']]);
            }
        });

        return $pipeline->fresh(['stages']);
    }
}
