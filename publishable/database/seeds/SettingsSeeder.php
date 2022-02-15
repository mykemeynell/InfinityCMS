<?php

use Infinity\Facades\Infinity;

class SettingsSeeder extends \Illuminate\Database\Seeder
{
    public function run()
    {
        $settings = __('infinity::seeders.settings');

        foreach($settings as $key => $setting) {
            $name = $setting['name'];
            $default = $setting['default'];
            $type = $setting['type'];

            /** @var \Illuminate\Database\Eloquent\Model $setting */
            $setting = Infinity::model('Setting')::firstOrNew(compact('key', 'name', 'default', 'type'));
            $setting->fill(collect($setting)->except(['key'])->toArray())->save();
        }
    }
}
