<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deal>
 */
class DealFactory extends Factory
{
    protected $model = Deal::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'pipeline_id' => Pipeline::factory(),
            'stage_id' => PipelineStage::factory(),
            'company_id' => null,
            'contact_id' => null,
            'assigned_to' => null,
            'name' => fake()->catchPhrase() . ' Deal',
            'amount' => fake()->randomFloat(2, 5000, 250000),
            'currency' => 'USD',
            'expected_close_date' => fake()->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'status' => 'open',
            'custom_attributes' => [
                'deal_type' => fake()->randomElement(['New Business', 'Renewal', 'Upsell', 'Cross-sell']),
                'lead_source' => fake()->randomElement(['Inbound', 'Outbound', 'Partner']),
            ],
        ];
    }
}
