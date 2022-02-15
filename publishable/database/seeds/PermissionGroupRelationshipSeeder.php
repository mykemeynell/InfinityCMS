<?php

use Illuminate\Database\Seeder;
use Infinity\Facades\Infinity;

class PermissionGroupRelationshipSeeder extends Seeder
{
    public function run()
    {
        $defaultPermissions = __('infinity::seeders.defaultPermissions');

        foreach($defaultPermissions as $name => $permissions) {
            /** @var \Infinity\Models\Group $group */
            $group = \Infinity\Facades\Infinity::model('Group')->where('name', $name)->firstOrFail();

            foreach($permissions as $permissionString) {
                /** @var \Infinity\Models\Permission $permission */
                $permission = \Infinity\Facades\Infinity::model('Permission')->where('key', $permissionString)->firstOrFail();

                Infinity::model('GroupPermissionRelationship')->fill([
                    'group_id' => $group->getKey(),
                    'permission_id' => $permission->getKey(),
                ])->save();
            }
        }
    }

    public function relationship(string $field, string $group_id)
    {
        return \Infinity\Models\GroupPermissionRelationship::where($field, $group_id)->firstOrNew();
    }
}
