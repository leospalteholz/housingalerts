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
                'contact_email' => 'leo.spalteholz@gmail.com',
                'website_url' => 'https://housingalerts.ca',
                'about' => 'Housing Alerts is the platform that powers this site.  We are dedicated to providing a way for housing advocates and members of the public to stay informed about upcoming housing-related hearings in their communities.',
                'user_visible' => false  // Hidden from signup - system organization
            ]
        );

        $hfl = \App\Models\Organization::updateOrCreate(
            [ 'name' => 'Homes for Living' ],
            [ 
                'slug' => 'homes-for-living',
                'areas_active' => 'Greater Victoria, BC',
                'about' => 'Homes for Living is a non-profit volunteer organization dedicated to advocating for affordable housing solutions in the Greater Victoria area. We raise awareness of barriers to housing at the local and provincial level, and advocate for policy changes to end the housing shortage and ensure access to housing for all.',
                'website_url' => 'https://homesforliving.ca',
                'contact_email' => 'hello@homesforliving.ca',
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

        $victoriaRegion = \App\Models\Region::updateOrCreate(
            [ 'name' => 'Victoria', 'organization_id' => $hfl->id ],
            [ 
                'name' => 'Victoria', 
                'organization_id' => $hfl->id,
                'comments_email' => 'publichearings@victoria.ca',
                'remote_instructions' => 'Phone: 778-698-2440 participation code 1551794#',
                'inperson_instructions' => 'Victoria City Hall Council Chambers, 1 Centennial Square, Victoria, BC'
            ]
        );

        $saanichRegion = \App\Models\Region::updateOrCreate(
            [ 'name' => 'Saanich', 'organization_id' => $hfl->id ],
            [ 
                'name' => 'Saanich', 
                'organization_id' => $hfl->id,
                'comments_email' => 'council@saanich.ca',
                'remote_instructions' => 'Find the teams link at https://www.saanich.ca/EN/main/local-government/mayor-council/schedule-agendas-minutes.html',
                'inperson_instructions' => 'Saanich Municipal Hall, 770 Vernon Avenue, Saanich, BC'
            ]
        );

        \App\Models\Hearing::updateOrCreate(
            [ 'street_address' => '123 Douglas Street', 'organization_id' => $hfl->id ],
            [ 
                'title' => '123 Douglas Street',
                'street_address' => '123 Douglas Street',
                'postal_code' => 'V8W 2E6',
                'rental' => true,
                'units' => 45,
                'description' => 'Proposed rental development at 123 Douglas Street. The proposal includes 45 rental units in a 6-story building with ground-floor commercial space. This project will provide much-needed affordable housing in downtown Victoria.',
                'remote_instructions' => 'Join via Zoom: https://zoom.us/j/123456789. Meeting ID: 123 456 789. Phone: 1-778-907-2071',
                'inperson_instructions' => 'Attend in person at Victoria City Hall Council Chambers, 1 Centennial Square. Doors open at 6:30 PM. Public parking available.',
                'comments_email' => 'comments@victoria.ca',
                'organization_id' => $hfl->id,
                'region_id' => $victoriaRegion->id,
                'start_datetime' => now()->addDays(30)->setTime(19, 0, 0),
                'end_datetime' => now()->addDays(30)->setTime(21, 0, 0),
                'more_info_url' => 'https://victoria.ca/hearings/123-douglas'
            ]
        );

        \App\Models\Hearing::updateOrCreate(
            [ 'street_address' => '456 Quadra Street', 'organization_id' => $hfl->id ],
            [ 
                'title' => '456 Quadra Street',
                'street_address' => '456 Quadra Street',
                'postal_code' => 'V8T 4E2',
                'rental' => false,
                'units' => 24,
                'description' => 'Development variance permit application for 24-unit condominium development on Quadra Street. Features sustainable design, rooftop gardens, and family-friendly amenities.',
                'remote_instructions' => 'Remote participation not available for this hearing.',
                'inperson_instructions' => 'In-person only at Saanich Municipal Hall, 770 Vernon Avenue. Doors open at 6:30 PM. Free parking available in municipal lot.',
                'comments_email' => 'planning@saanich.ca',
                'organization_id' => $hfl->id,
                'region_id' => $saanichRegion->id,
                'start_datetime' => now()->addDays(45)->setTime(19, 30, 0),
                'end_datetime' => now()->addDays(45)->setTime(22, 0, 0),
                'more_info_url' => 'https://saanich.ca/planning/456-quadra'
            ]
        );
    }
}
