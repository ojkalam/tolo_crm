<?php

declare(strict_types=1);

namespace App\Actions\Activity;

use App\Models\Activity;
use App\Models\Organization;
use App\Models\User;

class LogActivityAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(Organization $organization, array $data, ?User $creator = null): Activity
    {
        $data['organization_id'] = $organization->id;

        if (! isset($data['user_id']) && $creator) {
            $data['user_id'] = $creator->id;
        }

        return Activity::create($data);
    }
}
