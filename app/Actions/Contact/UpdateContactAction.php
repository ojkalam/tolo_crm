<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Models\Contact;

class UpdateContactAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(Contact $contact, array $data): Contact
    {
        if (isset($data['custom_attributes']) && is_array($data['custom_attributes'])) {
            $existing = $contact->custom_attributes ?? [];
            $data['custom_attributes'] = array_merge($existing, $data['custom_attributes']);
        }

        $contact->update($data);

        return $contact->fresh(['company', 'assignedUser']);
    }
}
