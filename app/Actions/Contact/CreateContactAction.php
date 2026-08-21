<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Models\Contact;
use App\Models\Organization;
use App\Models\User;

class CreateContactAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(Organization $organization, array $data, ?User $creator = null): Contact
    {
        $data['organization_id'] = $organization->id;

        if (! isset($data['assigned_user_id']) && $creator) {
            $data['assigned_user_id'] = $creator->id;
        }

        return Contact::create($data);
    }
}
