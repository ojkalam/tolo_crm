<?php

declare(strict_types=1);

namespace App\Actions\Pipeline;

use App\Models\Pipeline;
use Illuminate\Support\Facades\DB;

class UpdatePipelineAction
{
    /**
     * @param  array{name?: string, is_default?: bool}  $data
     */
    public function execute(Pipeline $pipeline, array $data): Pipeline
    {
        return DB::transaction(function () use ($pipeline, $data) {
            if (! empty($data['is_default'])) {
                Pipeline::where('organization_id', $pipeline->organization_id)
                    ->where('id', '!=', $pipeline->id)
                    ->update(['is_default' => false]);
            }

            $pipeline->update($data);

            return $pipeline->fresh(['stages']);
        });
    }
}
