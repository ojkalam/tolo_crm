<?php

declare(strict_types=1);

namespace App\Actions\Pipeline;

use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Support\Facades\DB;

class CreatePipelineAction
{
    /**
     * @param array{name: string, is_default?: bool, stages?: list<array{name: string, win_probability: int, order_column?: int, color_code?: string}>} $data
     */
    public function execute(Organization $organization, array $data): Pipeline
    {
        return DB::transaction(function () use ($organization, $data) {
            if (! empty($data['is_default'])) {
                Pipeline::where('organization_id', $organization->id)->update(['is_default' => false]);
            }

            $pipeline = Pipeline::create([
                'organization_id' => $organization->id,
                'name' => $data['name'],
                'is_default' => (bool) ($data['is_default'] ?? false),
            ]);

            $stages = $data['stages'] ?? [
                ['name' => 'Discovery', 'win_probability' => 10, 'order_column' => 1, 'color_code' => '#60A5FA'],
                ['name' => 'Demo / Presentation', 'win_probability' => 30, 'order_column' => 2, 'color_code' => '#818CF8'],
                ['name' => 'Proposal / Pricing', 'win_probability' => 60, 'order_column' => 3, 'color_code' => '#FBBF24'],
                ['name' => 'Negotiation', 'win_probability' => 80, 'order_column' => 4, 'color_code' => '#FB923C'],
                ['name' => 'Closed Won', 'win_probability' => 100, 'order_column' => 5, 'color_code' => '#34D399'],
                ['name' => 'Closed Lost', 'win_probability' => 0, 'order_column' => 6, 'color_code' => '#F87171'],
            ];

            foreach ($stages as $index => $stage) {
                PipelineStage::create([
                    'pipeline_id' => $pipeline->id,
                    'name' => $stage['name'],
                    'win_probability' => (int) ($stage['win_probability'] ?? 0),
                    'order_column' => (int) ($stage['order_column'] ?? ($index + 1)),
                    'color_code' => $stage['color_code'] ?? '#3B82F6',
                ]);
            }

            return $pipeline->fresh(['stages']);
        });
    }
}
