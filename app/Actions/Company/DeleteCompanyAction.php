<?php

declare(strict_types=1);

namespace App\Actions\Company;

use App\Models\Company;

class DeleteCompanyAction
{
    public function execute(Company $company): bool
    {
        return (bool) $company->delete();
    }
}
