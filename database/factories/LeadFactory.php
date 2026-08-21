<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'assigned_user_id' => null,
            'title' => fake()->jobTitle(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company_name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'status' => fake()->randomElement(LeadStatus::cases()),
            'source' => fake()->randomElement(LeadSource::cases()),
            'score' => fake()->numberBetween(10, 95),
            'estimated_value' => fake()->randomFloat(2, 1000, 100000),
            'notes' => fake()->paragraph(),
            'custom_attributes' => [
                'budget_range' => '$50k-$100k',
                'timeline' => 'Q3',
            ],
            'converted_at' => null,
        ];
    }
}
