<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        $type = fake()->randomElement(ActivityType::cases());

        return [
            'organization_id' => Organization::factory(),
            'user_id' => User::factory(),
            'subjectable_type' => Contact::class,
            'subjectable_id' => Contact::factory(),
            'type' => $type,
            'title' => ucfirst($type->value).': '.fake()->sentence(4),
            'description' => fake()->paragraph(),
            'due_date' => fake()->dateTimeBetween('-1 week', '+2 weeks'),
            'completed_at' => fake()->boolean(40) ? now() : null,
            'metadata' => [
                'outcome' => fake()->randomElement(['Connected', 'Left Voicemail', 'Rescheduled', 'Follow-up Needed']),
                'duration_minutes' => fake()->numberBetween(5, 60),
            ],
        ];
    }
}
