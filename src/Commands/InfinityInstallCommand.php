<?php

namespace Infinity\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Infinity\Facades\Infinity;
use Infinity\InfinityServiceProvider;
use Infinity\Seed;

class InfinityInstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'infinity:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Infinity admin package';

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Seed Folder name.
     *
     * @var string
     */
    protected string $seedFolder;

    /**
     * @param \Illuminate\Support\Composer $composer
     */
    public function __construct(Composer $composer)
    {
        parent::__construct();

        $this->composer = $composer;
        $this->composer->setWorkingPath(base_path());

        $this->seedFolder = Seed::getFolderName();
    }

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function handle(Filesystem $filesystem): void
    {
        if(!Infinity::isInfinityDevModeEnabled()) {
            $this->error("Please enable Infinity development mode by adding INFINITY_DEV=true to your .env file");
            exit;
        }

        if(empty(env('APP_KEY'))) {
            if(!$this->confirm("It looks like you haven't set your APP_KEY, would you like to do this now?", true)) {
                $this->error("Cannot proceed without APP_KEY. Please set APP_KEY before installing Infinity");
                exit;
            } else {
                $this->call('key:generate');
                $this->newLine();
            }
        }

        $this->info("Publishing the Infinity assets, database and config files");

        $tags = ['config', 'seeds'];

        $this->call("vendor:publish", ['--provider' => InfinityServiceProvider::class, '--tag' => $tags]);

        $this->newLine();
        $this->info('Migrating the database tables into your application');
        $this->call('migrate');

        $publishablePath = dirname(__DIR__).'/../publishable';

        $this->addNamespaceIfNeeded(
            collect($filesystem->files("{$publishablePath}/database/seeds/")),
            $filesystem
        );

        $this->newLine();
        $this->info('Dumping the autoloaded files and reloading all new files');
        $this->composer->dumpAutoloads();
        require_once base_path('vendor/autoload.php');

        $this->newLine();
        $this->info('Seeding data into the database');
        $this->call('db:seed', ['--class' => 'InfinityDatabaseSeeder']);

        $this->newLine();
        $this->info("Creating permissions");
        $this->call('infinity:debug:flush-permissions', ['--no-confirm' => true]);

        $this->newLine();
        $this->info("Assigning permissions to admin group");
        $this->call('infinity:debug:assign-all-permissions-to-admin-group', ['--no-confirm' => true]);

        $this->newLine();
        $this->info('Adding the storage symlink to your public folder');
        $this->call('storage:link');

        $this->newLine();
        if($this->confirm("Create first admin user too?", true)) {
            $this->call('infinity:admin', ['email' => $this->ask("What is the administrator email?"), '--create' => true]);
        }

        $this->newLine();
        $this->info('Successfully installed Infinity! Enjoy!');
        $this->info(sprintf('==> Go to %s to log in', infinity_route('login')));
    }

    private function addNamespaceIfNeeded($seeds, Filesystem $filesystem)
    {
        if ($this->seedFolder != 'seeders') {
            return;
        }

        $seeds->each(function ($file) use ($filesystem) {
            $path = database_path('seeders').'/'.$file->getFilename();
            $stub = str_replace(
                ["<?php\n\nuse", "<?php".PHP_EOL.PHP_EOL."use"],
                "<?php".PHP_EOL.PHP_EOL."namespace Database\\Seeders;".PHP_EOL.PHP_EOL."use",
                $filesystem->get($path)
            );

            $filesystem->put($path, $stub);
        });
    }
}
