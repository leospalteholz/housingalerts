<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'slug' => null,
            'organization_id' => Organization::factory(),
            'comments_email' => $this->faker->safeEmail(),
            'remote_instructions' => $this->faker->optional()->sentence(),
            'inperson_instructions' => $this->faker->optional()->sentence(),
        ];
    }
}
