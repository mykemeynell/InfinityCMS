<?php

namespace Infinity\Commands;

use Database\Seeders\PermissionGroupRelationshipSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Infinity\Facades\Infinity;
use Infinity\InfinityServiceProvider;
use Infinity\Models\Permission;
use Symfony\Component\Console\Input\InputOption;

class DebugFlushPermissions extends Command
{
    protected $signature = 'infinity:debug:flush-permissions {--no-confirm}';
    protected $description = 'Clears out user group permissions and resets to defaults.';

    public function handle()
    {
        try {
            $expectedGates = $this->computeExpectedGates();
            $databasePermissions = Permission::all()->pluck('key')->flatten();

            $addToDatabase = $expectedGates->diff($databasePermissions);
            $removeFromDatabase = $databasePermissions->diff($expectedGates);

            $this->info("The following gates will be added to the database");
            $addToDatabase->isNotEmpty()
                ? $this->table(["Gate"], $addToDatabase->map(function ($gate) { return [$gate]; })->toArray())
                : $this->info("==> None");

            $this->newLine();
            $this->info("The following gates wil be removed from the database");
            $removeFromDatabase->isNotEmpty()
                ? $this->table(["Gate"], $removeFromDatabase->map(function ($gate) { return [$gate]; })->toArray())
                : $this->info("==> None");

            if($addToDatabase->isEmpty() && $removeFromDatabase->isEmpty()) {
                $this->newLine();
                $this->info("==> No changes detected. Exiting.");
                exit;
            }

            if(!$this->option('no-confirm')) {
                if (!$this->confirm("Does this look correct?", false)) {
                    $this->error("Aborted.");
                    exit;
                }
            }

            DB::beginTransaction();

            $this->newLine();
            $addToDatabase->each(function($key) {
                $this->info("==> Adding {$key}");
                Permission::query()->create([
                    'key' => $key,
                    'name' => sprintf("%s - %s",
                        Str::ucfirst(
                            Str::plural(Str::before($key, '.'))
                        ),
                        Str::title(
                            Str::replace('_', ' ',
                                implode(" ", preg_split('/(?=[A-Z])/', Str::afterLast($key, '.')))
                            )
                        )
                    )
                ])->saveOrFail();
            });

            $removeFromDatabase->each(function($key) {
                $this->info("==> Removing {$key}");
                Permission::query()->where('key', $key)->delete();
            });

            DB::commit();

            $this->newLine();
            $this->info("==> Done");

            if(!$this->confirm("Add newly created groups to admin group?", true)) {
                $this->newLine();
                $this->call('infinity:debug:assign-all-permissions-to-admin-group', ['--no-confirm' => true]);
            }
        } catch(\Exception $exception) {
            $this->error($exception->getMessage());
            exit;
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
            exit;
        }
    }

    private function computeExpectedGates(): Collection
    {
        $gates = collect(InfinityServiceProvider::$gates);
        $resources = array_merge_recursive(
            Infinity::resources('core'), Infinity::resources());

        foreach($resources as $resource) {
            /** @var \Infinity\Resources\Resource $resource */
            $gates->add(collect(array_merge_recursive(['browse', 'read', 'edit', 'add', 'delete', 'showDelete'], $resource->additionalGates()))->map(function($gate) use ($resource) {
                return sprintf("%s.%s", $resource->getIdentifier(), $gate);
            })
                ->reject(function ($gate) use ($resource) {
                    return in_array($gate, $resource->excludedGates());
                })->flatten()->toArray());
        }

        return $gates->flatten();
    }

    /** @inheritDoc */
    protected function getOptions()
    {
        return [
            ['no-confirm', null, InputOption::VALUE_NONE, 'Do not confirm permission creation', null],
        ];
    }
}
