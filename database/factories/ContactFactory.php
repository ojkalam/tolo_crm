<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'company_id' => null,
            'assigned_user_id' => null,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'mobile' => fake()->phoneNumber(),
            'job_title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Sales', 'Engineering', 'Marketing', 'Executive', 'Operations']),
            'lifecycle_stage' => fake()->randomElement(['lead', 'prospect', 'customer', 'churned', 'other']),
            'custom_attributes' => [
                'linkedin_url' => 'https://linkedin.com/in/' . fake()->userName(),
                'preferred_contact_method' => fake()->randomElement(['email', 'phone', 'sms']),
            ],
        ];
    }
}
