<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'slug' => null,
            'contact_email' => $this->faker->unique()->safeEmail(),
            'website_url' => $this->faker->optional()->url(),
            'about' => $this->faker->optional()->paragraph(),
            'areas_active' => $this->faker->optional()->city(),
            'user_visible' => true,
        ];
    }
}
