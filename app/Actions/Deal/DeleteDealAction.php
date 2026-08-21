<?php

declare(strict_types=1);

namespace App\Actions\Deal;

use App\Models\Deal;

class DeleteDealAction
{
    public function execute(Deal $deal): bool
    {
        return (bool) $deal->delete();
    }
}
