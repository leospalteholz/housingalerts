<?php

namespace App\Console\Commands;

use App\Models\User;
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

            $this->ensureAdminFlags($user);

            return self::SUCCESS;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->ensureAdminFlags($user);

        $this->info("Created admin user for {$email}.");

        return self::SUCCESS;
    }

    private function ensureAdminFlags(User $user): void
    {
        $dirty = false;

        if (Schema::hasColumn($user->getTable(), 'is_admin') && ! $user->is_admin) {
            $user->is_admin = true;
            $dirty = true;
        }

        if (Schema::hasColumn($user->getTable(), 'is_superuser') && ! $user->is_superuser) {
            $user->is_superuser = true;
            $dirty = true;
        }

        if ($dirty) {
            $user->save();
            $this->info('Updated admin privilege flags on existing user.');
        }
    }
}
