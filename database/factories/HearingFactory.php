<?php

namespace Database\Factories;

use App\Models\Hearing;
use App\Models\Organization;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Hearing>
 */
class HearingFactory extends Factory
{
    protected $model = Hearing::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'region_id' => Region::factory(),
            'type' => $this->faker->randomElement(['development', 'policy']),
            'title' => $this->faker->sentence(4),
            'street_address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'rental' => $this->faker->boolean(),
            'units' => $this->faker->numberBetween(10, 200),
            'below_market_units' => $this->faker->numberBetween(0, 50),
            'replaced_units' => $this->faker->numberBetween(0, 10),
            'subject_to_vote' => $this->faker->boolean(),
            'approved' => false,
            'description' => $this->faker->optional()->paragraph(),
            'image_url' => null,
            'start_datetime' => $this->faker->dateTimeBetween('+1 days', '+2 weeks'),
            'end_datetime' => $this->faker->dateTimeBetween('+2 weeks', '+3 weeks'),
            'more_info_url' => $this->faker->optional()->url(),
            'remote_instructions' => $this->faker->optional()->sentence(),
            'inperson_instructions' => $this->faker->optional()->sentence(),
            'comments_email' => $this->faker->safeEmail(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Hearing $hearing) {
            if ($hearing->relationLoaded('region') && $hearing->region && $hearing->region->organization_id !== $hearing->organization_id) {
                $hearing->region->organization_id = $hearing->organization_id;
            }
        })->afterCreating(function (Hearing $hearing) {
            if ($hearing->relationLoaded('region') && $hearing->region && $hearing->region->organization_id !== $hearing->organization_id) {
                $hearing->region->organization_id = $hearing->organization_id;
                $hearing->region->save();
            }
        });
    }
}
