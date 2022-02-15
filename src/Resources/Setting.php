<?php

namespace Infinity\Resources;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Infinity\Actions;
use Infinity\Resources\Fields\Boolean;
use Infinity\Resources\Fields\Fluid;
use Infinity\Resources\Fields\ID;
use Infinity\Resources\Fields\Text;
use Infinity\Resources\Handlers\FluidHandler;

class Setting extends Resource
{
    public static string $model = 'Infinity\Models\Setting';
    public static ?string $controller = 'Infinity\Http\Controllers\InfinitySettingsController';
    public static string $icon = 'fas fa-cogs / fad fa-sliders-v-square';

    /**
     * @inheritDoc
     */
    public function fields(): array
    {
        return [
            ID::make()->hidden(),
            Text::make('key')->view('infinity::fields.badge'),
            Boolean::make('cached')->setDisplayName('Cached')
                ->setHandler(function (\Infinity\Models\Setting $setting) {
                    return true;
//                        Cache::has("infinity.{$setting->getSettingKey()}") &&
//                        Cache::get("infinity.{$setting->getSettingKey()}") === $setting->getValue();
                })->view('infinity::fields.icon', [
                    'conditional' => [
                        'class' => [
                            'cached:true' => fa('fas fa-check', 'fad fa-check'),
                            'cached:false' => fa('fas fa-times', 'fad fa-times'),
                        ]
                    ]
                ]),
            Text::make('name'),
            Text::make('value')->setHandler(function (\Infinity\Models\Setting $model) {
                return Str::limit($model->getValue(), 50);
            })
        ];
    }

    public function formFields(): array
    {
        return [
            ID::make()->hidden(),
            Text::make('key')->readOnly(),
            Text::make('name'),
            Text::make('value')->setHandler(FluidHandler::class),
            Text::make('default')->readOnly()
        ];
    }

    /**
     * @inheritDoc
     */
    public function excludedActions(): array
    {
        return [
            Actions\DeleteAction::class,
            Actions\ViewAction::class,
            Actions\CreateAction::class
        ];
    }
}
