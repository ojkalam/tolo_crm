<?php

declare(strict_types=1);

namespace App\Actions\Activity;

use App\Models\Activity;

class UpdateActivityAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Activity $activity, array $data): Activity
    {
        if (isset($data['metadata']) && is_array($data['metadata'])) {
            $existing = $activity->metadata ?? [];
            $data['metadata'] = array_merge($existing, $data['metadata']);
        }

        $activity->update($data);

        return $activity->fresh(['user', 'subjectable']);
    }
}
