<?php

namespace Infinity\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Infinity\Facades\Infinity;

class CreateAdminUser extends Command
{
    protected $signature = 'infinity:admin {email} {--create}';
    protected $description = 'Create an Infinity admin';

    public function handle()
    {
        /** @var \Infinity\Models\Users\User $user */
        $user = $this->getUser(
            $this->option('create')
        );

        // the user not returned
        if (!$user) {
            exit;
        }

        /** @var \Infinity\Models\Group $role */
        $role = $this->getAdministratorGroup();

        $user->group_id = $role->getKey();
        $user->save();

        $this->info(sprintf("The user [%s] now has full access to your site.", $user->getDisplayName()));
    }

    private function getUser(bool $create = false)
    {
        $email = $this->argument('email');

        /** @var \Infinity\Models\Users\User $model */
        $model = Auth::guard(app('InfinityGuard'))->getProvider()->getModel();
        $model = Str::start($model, '\\');

        // If we need to create a new user go ahead and create it
        if ($create) {
            $name = $this->ask('Enter the admin name');
            $password = $this->secret('Enter admin password');
            $confirmPassword = $this->secret('Confirm Password');

            // Ask for email if there wasnt set one
            if (!$email) {
                $email = $this->ask('Enter the admin email');
            }

            // check if user with given email exists

            if ($model::where('email', $email)->exists()) {
                $this->info("Can't create user. User with the email ".$email.' exists already.');

                return;
            }

            // Passwords don't match
            if ($password != $confirmPassword) {
                $this->info("Passwords don't match");

                return;
            }

            $this->info('Creating admin account');

            return call_user_func($model.'::forceCreate', [
                'name'     => $name,
                'email'    => $email,
                'password' => $password,
            ]);
        }

        return call_user_func($model.'::where', 'email', $email)->firstOrFail();
    }

    protected function getAdministratorGroup()
    {
        $role = Infinity::model('Group')->firstOrNew([
            'name' => 'admin',
        ]);

        if (!$role->exists) {
            $role->fill([
                'name' => 'admin',
            ])->save();
        }

        return $role;
    }
}
