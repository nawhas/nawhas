<?php

namespace App\Console\Commands;

use App\Database\Doctrine\EntityManager;
use App\Entities\User;
use App\Enum\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {--name=} {--email=} {--moderator}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provision a user account.';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle(EntityManager $em): bool
    {
        // Get variables from input scrypt
        $name = $this->option('name');
        $email = $this->option('email');
        $role = $this->option('moderator') ? Role::MODERATOR() : Role::CONTRIBUTOR();

        $password = $this->ask('What is the password? Press [Enter] if you would like to generate a random password');
        if (!$password) {
            $password = Str::random(24);
        }

        // Output to the suer to make sure the details are correct
        $this->info("The following user is being created");
        $this->comment("Name: $name");
        $this->comment("Email: $email");
        $this->comment("Role: $role");
        $this->comment("Password: $password");

        $confirmed = $this->confirm('Are the details displayed above correct?');
        if (!$confirmed) {
            return false;
        }
        $password = bcrypt($password);
        // Create a new user with the information supplied
        $user = new User($role, $name, $email, $password);
        $em->persist($user);
        $em->flush();
        return true;
    }
}
