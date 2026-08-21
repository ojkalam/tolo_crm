<?php

declare(strict_types=1);

namespace App\Actions\Deal;

use App\Models\Deal;
use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;

class CreateDealAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(Organization $organization, array $data, ?User $creator = null): Deal
    {
        $data['organization_id'] = $organization->id;

        if (! isset($data['assigned_to']) && $creator) {
            $data['assigned_to'] = $creator->id;
        }

        if (empty($data['pipeline_id'])) {
            $pipeline = Pipeline::firstOrCreate(
                ['organization_id' => $organization->id, 'is_default' => true],
                ['name' => 'Standard Sales Pipeline']
            );
            $data['pipeline_id'] = $pipeline->id;
        }

        if (empty($data['stage_id'])) {
            $stage = PipelineStage::where('pipeline_id', $data['pipeline_id'])
                ->orderBy('order_column')
                ->first();

            if (! $stage) {
                $stage = PipelineStage::create([
                    'pipeline_id' => $data['pipeline_id'],
                    'name' => 'Prospecting',
                    'win_probability' => 10,
                    'order_column' => 1,
                    'color_code' => '#3B82F6',
                ]);
            }
            $data['stage_id'] = $stage->id;
        }

        return Deal::create($data);
    }
}
