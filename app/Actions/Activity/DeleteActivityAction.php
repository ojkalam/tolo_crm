<?php

declare(strict_types=1);

namespace App\Actions\Activity;

use App\Models\Activity;

class DeleteActivityAction
{
    public function execute(Activity $activity): bool
    {
        return (bool) $activity->delete();
    }
}
