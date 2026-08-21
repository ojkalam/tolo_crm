<?php

declare(strict_types=1);

namespace App\Actions\Activity;

use App\Models\Activity;

class CompleteActivityAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Activity $activity, array $data = []): Activity
    {
        $metadata = $activity->metadata ?? [];

        if (! empty($data['outcome'])) {
            $metadata['outcome'] = $data['outcome'];
        }
        if (! empty($data['notes'])) {
            $metadata['completion_notes'] = $data['notes'];
        }

        $activity->update([
            'completed_at' => $data['completed_at'] ?? now(),
            'metadata' => $metadata,
        ]);

        return $activity->fresh(['user', 'subjectable']);
    }
}
