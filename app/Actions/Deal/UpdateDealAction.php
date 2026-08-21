<?php

declare(strict_types=1);

namespace App\Actions\Deal;

use App\Models\Deal;

class UpdateDealAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(Deal $deal, array $data): Deal
    {
        if (isset($data['custom_attributes']) && is_array($data['custom_attributes'])) {
            $existing = $deal->custom_attributes ?? [];
            $data['custom_attributes'] = array_merge($existing, $data['custom_attributes']);
        }

        $deal->update($data);

        return $deal->fresh(['pipeline', 'stage', 'company', 'contact', 'assignedTo']);
    }
}
