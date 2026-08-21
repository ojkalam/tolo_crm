<?php

declare(strict_types=1);

namespace App\Actions\Lead;

use App\Models\Lead;

class UpdateLeadAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Lead $lead, array $data): Lead
    {
        if (isset($data['custom_attributes']) && is_array($data['custom_attributes'])) {
            $existing = $lead->custom_attributes ?? [];
            $data['custom_attributes'] = array_merge($existing, $data['custom_attributes']);
        }

        $lead->update($data);

        return $lead->fresh(['assignedUser']);
    }
}
