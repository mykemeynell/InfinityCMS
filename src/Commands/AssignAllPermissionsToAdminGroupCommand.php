<?php

namespace Infinity\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infinity\Models\Group;
use Infinity\Models\GroupPermissionRelationship;
use Infinity\Models\Permission;
use Symfony\Component\Console\Input\InputOption;

class AssignAllPermissionsToAdminGroupCommand extends Command
{
    protected $signature = 'infinity:debug:assign-all-permissions-to-admin-group {--no-confirm}';
    protected $description = 'Assigns all permissions in the database to the default admin group.';

    public function handle()
    {
        try {
            $adminGroup = $this->getAdminGroup();
            $permissions = $this->getAllPermissions()->reject(function ($permission) use ($adminGroup) {
                return GroupPermissionRelationship::query()
                    ->where('group_id', $adminGroup->getKey())
                    ->where('permission_id', $permission->getKey())
                    ->exists();
            });

            if($permissions->isEmpty()) {
                $this->info("No permissions to assign. Exiting.");
                exit;
            }

            if(!$this->option('no-confirm')) {
                $this->table(["Permission Key"], $permissions->map(function ($permission) {
                    /** @var \Infinity\Models\Permission $permission */
                    return [$permission->getPermissionKey()];
                }));

                if(!$this->confirm("Really add these permission to the admin group?", false)) {
                    $this->error("Aborted.");
                    exit;
                }
            }

            DB::beginTransaction();
            foreach($permissions as $permission) {
                /** @var \Infinity\Models\Permission $permission */
                GroupPermissionRelationship::query()->updateOrCreate([
                    'group_id' => $adminGroup->getKey(),
                    'permission_id' => $permission->getKey(),
                ]);
            }
            DB::commit();

            $this->newLine();
            $this->info("==> Done.");
        } catch(\Exception $exception) {
            $this->error($exception->getMessage());
            exit;
        }
    }

    /**
     * Get the admin group.
     *
     * @return \Infinity\Models\Group
     * @throws \Exception
     */
    private function getAdminGroup(): Model
    {
        $query = Group::query()->where('name', 'admin');

        if(!$query->exists()) {
            throw new \Exception("No admin group could be found.");
        }

        return $query->first();
    }

    /**
     * Get all permissions from the database.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    /** @inheritDoc */
    protected function getOptions()
    {
        return [
            ['no-confirm', null, InputOption::VALUE_NONE, 'Do not confirm permission creation', null],
        ];
    }
}
