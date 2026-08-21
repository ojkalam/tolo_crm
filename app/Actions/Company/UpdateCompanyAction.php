<?php

declare(strict_types=1);

namespace App\Actions\Company;

use App\Models\Company;

class UpdateCompanyAction
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Company $company, array $data): Company
    {
        if (isset($data['custom_attributes']) && is_array($data['custom_attributes'])) {
            $existing = $company->custom_attributes ?? [];
            $data['custom_attributes'] = array_merge($existing, $data['custom_attributes']);
        }

        $company->update($data);

        return $company->fresh(['owner']);
    }
}
