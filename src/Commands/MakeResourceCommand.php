<?php

namespace Infinity\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeResourceCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'infinity:make:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Infinity resource';

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/resource.stub';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Infinity';
    }

    public function handle()
    {
        $name = $this->argument('name');

        if(!$this->option('no-model')) {
            $this->info(sprintf("Creating model %s...", $name));
            $this->call('infinity:make:model', ['name' => Str::singular($name)]);
        }

        if(!$this->option('no-migration')) {
            $this->info(sprintf("Creating migration create_%s_table...",
                Str::slug(Str::lower(Str::plural($name)))));
            $this->call('make:migration', [
                'name' => sprintf("create_%s_table",
                    Str::slug(Str::lower(Str::plural($name)))),
                '--create' => Str::slug(Str::lower(Str::plural($name))),
            ]);
        }

        parent::handle();

        if(!$this->option('no-migration')) {
            $this->info(sprintf("Dont forget to run %s once you have populated your migration",
                'php artisan migrate'));
        }

        $slug = Str::slug($name);
        if(
            env('INFINITY_DEV', false) &&
            $this->confirm(sprintf("Would you like to add the default permission to the database for resource slug [%s] now?", $slug))
        ) {
            $this->call('infinity:debug:make-permissions', [
                'slug' => $slug,
                '--no-confirm' => true
            ]);
        }
    }

    protected function replaceClass($stub, $name)
    {
        $stub = str_replace(['DummyModel'], $this->argument('name'), $stub);

        return parent::replaceClass($stub, $name);
    }

    protected function getOptions()
    {
        return [
            ['no-model', null, InputOption::VALUE_NONE, 'Do not create a model', null],
            ['no-migration', null, InputOption::VALUE_NONE, 'Do not create a migration', null],
        ];
    }
}
