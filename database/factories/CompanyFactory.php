<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'organization_id' => Organization::factory(),
            'name' => $name,
            'domain' => Str::slug($name).'.com',
            'industry' => fake()->randomElement(['Technology', 'Healthcare', 'Finance', 'Manufacturing', 'Retail', 'Education']),
            'annual_revenue' => fake()->randomFloat(2, 50000, 50000000),
            'employees_count' => fake()->numberBetween(5, 5000),
            'phone' => fake()->phoneNumber(),
            'website' => 'https://www.'.Str::slug($name).'.com',
            'address_street' => fake()->streetAddress(),
            'address_city' => fake()->city(),
            'address_state' => fake()->state(),
            'address_country' => fake()->country(),
            'address_postal_code' => fake()->postcode(),
            'custom_attributes' => [
                'tier' => fake()->randomElement(['Tier 1', 'Tier 2', 'Tier 3']),
                'lead_source' => fake()->randomElement(['Inbound', 'Outbound', 'Referral', 'Partner']),
            ],
        ];
    }
}
