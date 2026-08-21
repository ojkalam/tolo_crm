<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\PipelineStage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PipelineStage
 */
class PipelineStageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pipeline_id' => $this->pipeline_id,
            'name' => $this->name,
            'win_probability' => (int) $this->win_probability,
            'order_column' => (int) $this->order_column,
            'color_code' => $this->color_code,
            'deals_count' => $this->whenCounted('deals'),
            'deals' => DealResource::collection($this->whenLoaded('deals')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
