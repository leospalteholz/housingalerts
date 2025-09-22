<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test organization
        $organization = \App\Models\Organization::firstOrCreate([
            'slug' => 'test-org'
        ], [
            'name' => 'Test Organization',
        ]);

        // Create a subscribed test user
        \App\Models\User::firstOrCreate([
            'email' => 'subscribed@test.com'
        ], [
            'name' => 'Subscribed User',
            'password' => bcrypt('password'),
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
            'unsubscribed_at' => null,
        ]);

        // Create an unsubscribed test user
        \App\Models\User::firstOrCreate([
            'email' => 'unsubscribed@test.com'
        ], [
            'name' => 'Unsubscribed User',
            'password' => bcrypt('password'),
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
            'unsubscribed_at' => now()->subDays(1),
        ]);
    }
}
