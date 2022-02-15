<?php

namespace Infinity\Listeners;

use Illuminate\Support\Facades\Cache;
use Infinity\Events\ModelChangedEvent;
use Infinity\Models\Setting;

class SettingsUpdatedListener
{
    public function handle(ModelChangedEvent $modelChangedEvent): bool
    {
        if(!$modelChangedEvent->model instanceof Setting)
        {
            return true;
        }

        return Cache::put("infinity", null,-20);
    }
}
