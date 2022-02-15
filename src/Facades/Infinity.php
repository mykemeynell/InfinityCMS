<?php

namespace Infinity\Facades;

use Illuminate\Support\Facades\Facade;
use Infinity\Resources\Resource;

/**
 * @method static void routes()
 * @method static bool routesHaveLoaded()
 * @method static \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application view(string $view, array $data = [], array $mergeData = [])
 * @method static bool viewExists(string $view)
 * @method static \Illuminate\Contracts\Foundation\Application|\Illuminate\Database\Eloquent\Model model(string $name)
 * @method static string modelClass(string $name)
 * @method static array findResourceClasses(?string $searchDirectory = null)
 * @method static \Infinity\Infinity addResource(array|Resource|string $resource, string $group = 'default')
 * @method static array resources(string $group = 'default')
 * @method static \Infinity\Resources\Resource resource(string $resource, string $group = 'default')
 * @method static array actions()
 * @method static array formFields()
 * @method static void addFormField(string $handler)
 * @method static \Illuminate\View\View formField(\Infinity\Resources\Fields\Field $field)
 * @method static bool formFieldRegistered(string $handler)
 * @method static bool isInfinityInstalled()
 * @method static bool isInfinityDevModeEnabled()
 */
class Infinity extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \Infinity\Infinity::PACKAGE_NAME;
    }
}
