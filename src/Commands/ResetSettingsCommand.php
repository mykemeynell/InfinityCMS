<?php

namespace Infinity\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Infinity\Events\ModelChangedEvent;
use Infinity\Facades\Infinity;

class ResetSettingsCommand extends Command
{
    protected $signature = 'infinity:debug:reset-settings';
    protected $description = 'Wipe and reset all settings to default values';

    public function handle()
    {
        /** @var \Infinity\Models\Setting $model */
        $model = Infinity::model('Setting');

        if(!$this->confirm("Really clear and reset all settings?", false)) {
            $this->error("Aborted.");
            exit;
        }

        DB::table($model->getTable())->truncate();

        $settings = __('infinity::seeders.settings');

        foreach($settings as $key => $setting) {
            /**
             * @var string $name
             * @var string $default
             * @var string $type
             */
            extract($setting);

            /** @var \Illuminate\Database\Eloquent\Model $setting */
            $setting = Infinity::model('Setting')::firstOrNew(compact('key', 'name', 'default', 'type'));
            $model = $setting->fill(collect($setting)->except(['key'])->toArray());

            if($model->save()) {
                ModelChangedEvent::dispatch($model);
            }
        }

        $this->newLine();
        $this->info("==> Done.");
    }
}
