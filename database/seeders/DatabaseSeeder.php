<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $rootOrg = \App\Models\Organization::updateOrCreate(
            [ 'name' => 'Housing Alerts' ],
            [ 
                'slug' => 'root',
                'user_visible' => false  // Hidden from signup - system organization
            ]
        );

        $hfl = \App\Models\Organization::updateOrCreate(
            [ 'name' => 'Homes for Living' ],
            [ 
                'slug' => 'homes-for-living',
                'areas_active' => 'Greater Victoria, BC',
                'contact-email' => 'hello@homesforliving.a',
                'user_visible' => true  // Visible in signup
            ]
        );

        // Create superuser (can manage all organizations)
        \App\Models\User::updateOrCreate(
            [ 'email' => 'root@housingalerts.ca' ],
            [
                'name' => 'Super User',
                'email' => 'root@housingalerts.ca',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'is_superuser' => true,
                'email_verified_at' => now(),
                'organization_id' => $rootOrg->id,
            ]
        );
        
        // Create regular admin (can only manage within their organization)
        \App\Models\User::updateOrCreate(
            [ 'email' => 'admin@housingalerts.ca' ],
            [
                'name' => 'Admin User',
                'email' => 'admin@housingalerts.ca',
                'password' => bcrypt('password'),
                'is_admin' => true,
                'is_superuser' => false,
                'email_verified_at' => now(),
                'organization_id' => $hfl->id,
            ]
        );
        \App\Models\User::updateOrCreate(
            [ 'email' => 'user@housingalerts.ca' ],
            [
                'name' => 'Regular User',
                'email' => 'user@housingalerts.ca',
                'password' => bcrypt('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
                'organization_id' => $hfl->id,
            ]
        );

        \App\Models\Region::updateOrCreate(
            [ 'name' => 'Victoria', 'organization_id' => $hfl->id ],
            [ 'name' => 'Victoria', 'organization_id' => $hfl->id ]
        );

        \App\Models\Region::updateOrCreate(
            [ 'name' => 'Saanich', 'organization_id' => $hfl->id ],
            [ 'name' => 'Saanich', 'organization_id' => $hfl->id ]
        );

        \App\Models\Hearing::updateOrCreate(
            [ 'title' => 'City Council Meeting', 'organization_id' => $hfl->id ],
            [ 'start_date' => now()->addDays(30) ]
        );
    }
}
