<?php

declare(strict_types=1);

namespace App\Actions\Company;

use App\Models\Company;
use App\Models\Organization;
use App\Models\User;

class CreateCompanyAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(Organization $organization, array $data, ?User $creator = null): Company
    {
        $data['organization_id'] = $organization->id;

        if (! isset($data['owner_id']) && $creator) {
            $data['owner_id'] = $creator->id;
        }

        return Company::create($data);
    }
}
