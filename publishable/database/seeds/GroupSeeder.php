<?php

use Infinity\Facades\Infinity;

class GroupSeeder extends \Illuminate\Database\Seeder
{
    public function run()
    {
        $groups = __('infinity::seeders.groups');

        foreach($groups as $name) {
            /** @var \Illuminate\Database\Eloquent\Model $group */
            $group = Infinity::model('Group')::firstOrNew(compact('name'));
            $group->fill(compact('name'))->save();
        }
    }
}
