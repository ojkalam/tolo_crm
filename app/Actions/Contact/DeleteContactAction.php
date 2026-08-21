<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Models\Contact;

class DeleteContactAction
{
    public function execute(Contact $contact): bool
    {
        return (bool) $contact->delete();
    }
}
