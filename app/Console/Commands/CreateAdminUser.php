<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure an admin user exists; create one if missing.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $name = env('ADMIN_NAME', 'Admin');

        if (empty($email) || empty($password)) {
            $this->line('INFO: ADMIN_EMAIL and ADMIN_PASSWORD not set; skipping admin bootstrap.');

            return self::SUCCESS;
        }

        $userModel = new User();
        $usersTable = $userModel->getTable();
        if (! Schema::hasTable($usersTable)) {
            $this->warn("Users table '{$usersTable}' does not exist yet; skipping admin creation.");

            return self::SUCCESS;
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $this->info("Admin user already exists for {$email}.");

            return self::SUCCESS;
        }

        // if the user doesn't already exist, create the root organization and the user. 
        $rootOrg = Organization::updateOrCreate(
            ['name' => 'Housing Alerts'],
            [
                'slug' => 'root',
                'contact_email' => 'leo.spalteholz@gmail.com',
                'website_url' => 'https://housingalerts.ca',
                'about' => 'Housing Alerts is the platform that powers this site.  We are dedicated to providing a way for housing advocates and members of the public to stay informed about upcoming housing-related hearings in their communities.',
                'user_visible' => false,
            ]
        );

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'is_superuser' => true,
            'is_admin' => true,
            'organization_id' => $rootOrg->id,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->info("Created admin user for {$email}.");

        return self::SUCCESS;
    }

    
}
