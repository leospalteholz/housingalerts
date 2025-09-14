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

        $victoriaRegion = \App\Models\Region::updateOrCreate(
            [ 'name' => 'Victoria', 'organization_id' => $hfl->id ],
            [ 
                'name' => 'Victoria', 
                'organization_id' => $hfl->id,
                'comments_email' => 'comments@victoria.ca',
                'remote_instructions' => 'Join via Zoom: https://zoom.us/j/victoria-hearings
Phone: 1-778-907-2071
Meeting ID: Victoria Hearings
Passcode: housing2025',
                'inperson_instructions' => 'Victoria City Hall Council Chambers
1 Centennial Square, Victoria, BC
Doors open 30 minutes before meeting
Public parking available in surrounding area'
            ]
        );

        $saanichRegion = \App\Models\Region::updateOrCreate(
            [ 'name' => 'Saanich', 'organization_id' => $hfl->id ],
            [ 
                'name' => 'Saanich', 
                'organization_id' => $hfl->id,
                'comments_email' => 'planning@saanich.ca',
                'remote_instructions' => 'Remote participation not typically available.
For exceptional circumstances, contact planning@saanich.ca',
                'inperson_instructions' => 'Saanich Municipal Hall
770 Vernon Avenue, Saanich, BC
Committee Room A
Free parking in municipal lot
Wheelchair accessible entrance on north side'
            ]
        );

        \App\Models\Hearing::updateOrCreate(
            [ 'street_address' => '123 Douglas Street', 'organization_id' => $hfl->id ],
            [ 
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
                'start_date' => now()->addDays(30),
                'start_time' => '19:00:00',
                'end_time' => '21:00:00',
                'more_info_url' => 'https://victoria.ca/hearings/123-douglas'
            ]
        );

        \App\Models\Hearing::updateOrCreate(
            [ 'street_address' => '456 Quadra Street', 'organization_id' => $hfl->id ],
            [ 
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
                'start_date' => now()->addDays(45),
                'start_time' => '19:30:00',
                'end_time' => '22:00:00',
                'more_info_url' => 'https://saanich.ca/planning/456-quadra'
            ]
        );
    }
}
