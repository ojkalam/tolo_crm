<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PipelineStage>
 */
class PipelineStageFactory extends Factory
{
    protected $model = PipelineStage::class;

    public function definition(): array
    {
        return [
            'pipeline_id' => Pipeline::factory(),
            'name' => fake()->randomElement(['Discovery', 'Demo Scheduled', 'Proposal Sent', 'Negotiation', 'Closed Won', 'Closed Lost']),
            'win_probability' => fake()->numberBetween(10, 100),
            'order_column' => fake()->numberBetween(1, 10),
            'color_code' => fake()->hexColor(),
        ];
    }
}
