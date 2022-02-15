<?php

use Illuminate\Database\Seeder;
use Infinity\Facades\Infinity;
use Infinity\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = __('infinity::seeders.permissions');

        foreach($permissions as $key => $name) {
            /** @var \Illuminate\Database\Eloquent\Model $permission */
            $permission = Infinity::model('Permission')::firstOrNew(['key' => $key]);
            $permission->fill(compact('key', 'name'))->save();
        }
    }

    /**
     * [dataType description].
     *
     * @param $field
     * @param $for
     *
     * @return mixed
     */
    protected function permission($field, $for)
    {
        return Permission::firstOrNew([$field => $for]);
    }
}
