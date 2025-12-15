<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Opprett en admin-bruker';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Check if any user already exists
        if (User::query()->exists()) {
            $this->error('En admin-bruker eksisterer allerede. Kun én admin er tillatt.');

            return self::FAILURE;
        }

        $email = $this->option('email');
        $password = $this->option('password');

        // Validate email and password are provided
        if (! $email || ! $password) {
            $this->error('Både --email og --password er påkrevd.');

            return self::FAILURE;
        }

        // Validate email format
        $validator = Validator::make(
            ['email' => $email],
            ['email' => 'required|email']
        );

        if ($validator->fails()) {
            $this->error('Ugyldig e-postformat.');

            return self::FAILURE;
        }

        // Validate password length
        if (strlen($password) < 8) {
            $this->error('Passordet må være minst 8 tegn.');

            return self::FAILURE;
        }

        // Create the admin user
        User::create([
            'name' => 'Admin',
            'email' => $email,
            'password' => $password, // Will be automatically hashed due to 'hashed' cast
        ]);

        $this->info("Admin-bruker opprettet med e-post: {$email}");

        return self::SUCCESS;
    }
}
