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
    protected $signature = 'user:create {--name=} {--email=} {--moderator} {--contributor}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user depending on the role';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(EntityManager $em): bool
    {
        // Get variables from input scrypt
        $name = $this->option('name');
        $email = $this->option('email');
        $moderator = $this->option('moderator');
        $contributor = $this->option('contributor');
        $role = null;

        // Check to see if a role has been passed
        if (!$moderator and !$contributor) {
            $this->info('Moderator or Contributor was not passed');
            return false;
        }
        if ($moderator and $contributor) {
            $this->info('Moderator and Contributor were both passed. Please pass only one');
            return false;
        }

        // Assign $role the correct information
        if ($moderator) {
            $role = Role::MODERATOR();
        } else if($contributor) {
            $role = Role::CONTRIBUTOR();
        }

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
        $isDetailsCorrect = $this->choice('Are the details displayed above correct?', ['No', 'Yes'], 0);

        if ($isDetailsCorrect === 'Yes') {
            $password = bcrypt($password);
            // Create a new user with the information supplied
            $user = new User($role, $name, $email, $password);
            $em->persist($user);
            $em->flush();
        } else {
            return false;
        }
        return true;
    }
}
