<?php

namespace Infinity\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Infinity\Models\Permission;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreatePermissionsCommand extends Command
{
    protected $signature = 'infinity:debug:make-permissions {slug} {--no-confirm}';
    protected $description = 'Create default permissions for a resource slug';

    const DEFAULT_PERMISSIONS = [
        'index', 'store', 'create', 'showDelete', 'show', 'update', 'delete', 'edit'
    ];

    public function handle()
    {
        $slug = $this->argument('slug');

        if(!$this->option('no-confirm')) {
            if (!$this->confirm("Create permissions for resource slug {$slug}?")) {
                $this->info("Exiting.");
                return;
            }
        }

        try {
            $this->info("==> Starting transaction");
            DB::beginTransaction();
            foreach (self::DEFAULT_PERMISSIONS as $permission) {
                $key = sprintf("%s.%s", $slug, $permission);
                $name = sprintf("%s permission for %s", ucfirst($permission), $slug);

                if((new Permission(compact('key', 'name')))->saveOrFail()) {
                    $this->info(sprintf("Created permission %s", $key));
                }
            }
            $this->info("==> Committing changes to database");
            DB::commit();
            $this->info("Done.");
        } catch (\Exception $exception) {
            $this->error("Failed to create permissions, because: " . $exception->getMessage());
        } catch (\Throwable $exception) {
            $this->error("Failed to create permissions, because: " . $exception->getMessage());
        }
    }

    /** @inheritDoc */
    protected function getArguments()
    {
        return [
            ['slug', InputArgument::REQUIRED, 'The slug of the resource'],
        ];
    }

    /** @inheritDoc */
    protected function getOptions()
    {
        return [
            ['no-confirm', null, InputOption::VALUE_NONE, 'Do not confirm permission creation', null],
        ];
    }
}
