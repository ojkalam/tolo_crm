<?php

declare(strict_types=1);

namespace App\Actions\Lead;

use App\Models\Lead;

class DeleteLeadAction
{
    public function execute(Lead $lead): bool
    {
        return (bool) $lead->delete();
    }
}
