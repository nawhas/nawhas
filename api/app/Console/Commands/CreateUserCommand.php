<?php

namespace App\Console\Commands;

use App\Modules\Authentication\Enum\Role;
use App\Modules\Authentication\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {name} {email} {--moderator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provision a user account.';

    public function handle(): int
    {
        // Get variables from input scrypt
        $name = $this->argument('name');
        $email = $this->argument('email');
        $role = $this->option('moderator') ? Role::MODERATOR : Role::CONTRIBUTOR;

        $password = $this->ask('What is the password? Press [Enter] if you would like to generate a random password');
        if (!$password) {
            $password = Str::random(24);
        }

        // Output to the suer to make sure the details are correct
        $this->info("The following user is being created");
        $this->comment("Name: $name");
        $this->comment("Email: $email");
        $this->comment("Role: $role->value");
        $this->comment("Password: $password");

        $confirmed = $this->confirm('Are the details displayed above correct?');
        if (!$confirmed) {
            return 1;
        }

        // Create a new user with the information supplied
        User::create($role, $name, $email, $password);

        return 0;
    }
}
