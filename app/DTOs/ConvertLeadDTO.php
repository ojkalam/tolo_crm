<?php

declare(strict_types=1);

namespace App\DTOs;

class ConvertLeadDTO
{
    /**
     * @param  array<string, mixed>  $companyData
     * @param  array<string, mixed>  $contactData
     * @param  array<string, mixed>  $dealData
     */
    public function __construct(
        public array $companyData = [],
        public array $contactData = [],
        public bool $createDeal = true,
        public array $dealData = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            companyData: $data['company'] ?? [],
            contactData: $data['contact'] ?? [],
            createDeal: (bool) ($data['create_deal'] ?? true),
            dealData: $data['deal'] ?? [],
        );
    }
}
