<?php

declare(strict_types=1);

namespace App\Actions\Lead;

use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use App\Services\Lead\LeadScoringService;

class CreateLeadAction
{
    public function __construct(
        protected LeadScoringService $scoringService
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Organization $organization, array $data, ?User $creator = null): Lead
    {
        $data['organization_id'] = $organization->id;

        if (! isset($data['assigned_user_id']) && $creator) {
            $data['assigned_user_id'] = $creator->id;
        }

        $lead = new Lead($data);

        if (! isset($data['score']) || (int) $data['score'] === 0) {
            $lead->score = $this->scoringService->calculateScore($lead);
        }

        $lead->save();

        return $lead->fresh(['assignedUser']);
    }
}
