<?php

namespace Infinity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Infinity\Actions\DeleteAction;
use Infinity\Actions\EditAction;
use Infinity\Actions\ViewAction;
use Infinity\Models\Group;
use Infinity\Models\GroupPermissionRelationship;
use Infinity\Models\Permission;
use Infinity\Models\Setting;
use Infinity\Models\Users\User;
use Infinity\Resources\Fields\Field;
use Infinity\Resources\Handlers\HandlerInterface;
use Infinity\Resources\Resource;

final class Infinity
{
    const PACKAGE_NAME = 'infinity';
    private bool $routesHaveLoaded = false;

    protected array $models = [
        'User' => User::class,
        'Group' => Group::class,
        'Permission' => Permission::class,
        'GroupPermissionRelationship' => GroupPermissionRelationship::class,
        'Setting' => Setting::class,
    ];

    /**
     * Default actions to be registered.
     *
     * @var array|string[]
     */
    protected array $actions = [
        ViewAction::class,
        EditAction::class,
        DeleteAction::class,
    ];

    /**
     * @var string|null
     */
    private string|null $version = null;

    /**
     * @var \Illuminate\Contracts\Foundation\Application|mixed
     */
    private mixed $filesystem;

    /**
     * @var array
     */
    protected array $formFields = [];

    /**
     * Default and discovered resources used by Infinity.
     *
     * @var array
     */
    protected array $resources = [];

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct()
    {
        $this->filesystem = app(Filesystem::class);
        $this->findVersion();
    }

    /**
     * Load Infinity routes.
     *
     * @return void
     */
    public function routes(): void
    {
        require __DIR__ . '/../routes/infinity.php';
        $this->routesHaveLoaded = true;
    }

    /**
     * Check if the routes have been loaded.
     *
     * @return bool
     */
    public function routesHaveLoaded(): bool
    {
        return $this->routesHaveLoaded;
    }

    /**
     * Find the version of Infinity.
     *
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function findVersion(): void
    {
        if (!is_null($this->version)) {
            return;
        }

        if ($this->filesystem->exists(base_path('composer.lock'))) {
            // Get the composer.lock file
            $file = json_decode(
                $this->filesystem->get(base_path('composer.lock'))
            );

            // Loop through all the packages and get the version of Infinity
            foreach ($file->packages as $package) {
                if ($package->name == 'mykemeynell/infinity') {
                    $this->version = $package->version;
                    break;
                }
            }
        }
    }

    /**
     * Get the version string.
     *
     * @return string|null
     */
    public function getVersion(): string|null
    {
        return $this->version;
    }

    /**
     * Get a model.
     *
     * @param string $name
     *
     * @return \Illuminate\Foundation\Application|\Illuminate\Database\Eloquent\Model
     */
    public function model(string $name): Application|Model
    {
        return app($this->models[Str::studly($name)]);
    }

    /**
     * Get the model class.
     *
     * @param string $name
     *
     * @return string
     */
    public function modelClass(string $name): string
    {
        return $this->models[Str::studly($name)];
    }

    /**
     * Return a view with the Infinity namespace.
     *
     * @param string $view
     * @param array  $data
     * @param array  $mergeData
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
     */
    public static function view(string $view, array $data = [], array $mergeData = []): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view(sprintf("infinity::%s", $view), $data, $mergeData);
    }

    /**
     * Test if a view exists in the Infinity namespace.
     *
     * @param string $view
     *
     * @return bool
     */
    public static function viewExists(string $view): bool
    {
        return view()->exists(sprintf("infinity::%s", $view));
    }

    /**
     * Add a new action.
     *
     * @param $action
     *
     * @return void
     */
    public function addAction($action): void
    {
        $this->actions[] = $action;
    }

    /**
     * Replace an action.
     *
     * @param $actionToReplace
     * @param $action
     *
     * @return void
     */
    public function replaceAction($actionToReplace, $action): void
    {
        $key = array_search($actionToReplace, $this->actions);
        $this->actions[$key] = $action;
    }

    /**
     * Get all registered actions.
     *
     * @return array
     */
    public function actions(): array
    {
        return $this->actions;
    }

    /**
     * Find the resources that are available within the application directory.
     *
     * @param string $resourcesPath
     *
     * @return array
     */
    public function findResourceClasses(string $resourcesPath): array
    {
        $resourceFiles = glob_recursive(rtrim($resourcesPath, '/') . '/*.php');

        $resources = [];

        foreach($resourceFiles as $resource) {
            $namespace = get_file_namespace($resource);
            $class = $namespace . Str::start(pathinfo($resource, PATHINFO_FILENAME), '\\');

            if(!class_exists($class) || !is_subclass_of($class, Resource::class)) continue;

            $resources[] = $class;
        }

        return $resources;
    }

    /**
     * Add a resource to the application.
     *
     * @param array|\Infinity\Resources\Resource $resource
     * @param string                             $group
     *
     * @return $this
     * @throws \Exception
     */
    public function addResource(array|Resource|string $resource, string $group = 'default'): Infinity
    {
        if(!is_array($resource)) {
            $resource = [$resource];
        }

        foreach($resource as $resourceClass) {
            if(!class_exists($resourceClass)) continue;

            if(is_string($resourceClass)) {
                /** @var Resource $concrete */
                $resourceClass = app($resourceClass);
            }

            if(!is_subclass_of($resourceClass, Resource::class)) continue;

            if(array_key_exists($resourceClass->getIdentifier(), $this->resources)) {
                throw new \Exception(sprintf("Resource of [%s] has already been registered in group [%s].", $resourceClass::class, $group));
            }

            $this->resources[$group][$resourceClass->getIdentifier()] = $resourceClass;
        }

        return $this;
    }

    /**
     * Get the resources.
     *
     * @param string $group
     *
     * @return array
     */
    public function resources(string $group = 'default'): array
    {
        return array_key_exists($group, $this->resources)
            ? $this->resources[$group]
            : [];
    }

    /**
     * Scan all registered resources for a given identifier and return the first instance.
     *
     * @throws \Exception
     */
    protected function findResourceInGroups(string $resource)
    {
        foreach($this->resources as $group => $resources) {
            if(array_key_exists($resource, $resources)) {
                return $resources[$resource];
            }
        }

        throw new \Exception(sprintf("Resource [%s] was not found to be registered in any group", $resource));
    }

    /**
     * Create a resource class from its resource name.
     *
     * @param string      $resource
     * @param string|null $group
     *
     * @return \Infinity\Resources\Resource
     * @throws \Exception
     */
    public function resource(string $resource, ?string $group = null): Resource
    {
        if(!empty($group) && !array_key_exists($resource, $this->resources[$group])) {
            throw new \Exception(sprintf("Resource class for [%s] not found.", $resource));
        }

        $class = !empty($group)
            ? $this->resources[$group][$resource]
            : $this->findResourceInGroups($resource);

        if($class instanceof Resource) {
            return $class;
        }

        return app($resource);
    }

    /**
     * Test if a form field has already been registered.
     *
     * @param string $handler
     *
     * @return bool
     */
    public function formFieldRegistered(string $handler): bool
    {
        if (!$handler instanceof HandlerInterface) {
            $handler = app($handler);
        }

        return array_key_exists($handler->fieldDataType(), $this->formFields);
    }

    /**
     * Add a new form field.
     *
     * @param string $handler
     *
     * @return \Infinity\Infinity
     */
    public function addFormField(string $handler): Infinity
    {
        if (!$handler instanceof HandlerInterface) {
            $handler = app($handler);
        }

        $this->formFields[$handler->fieldDataType()] = $handler;

        return $this;
    }

    /**
     * Pass the field to the appropriate handler.
     *
     * @param \Infinity\Resources\Fields\Field $field
     * @param \Infinity\Resources\Resource     $resource
     *
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function formField(Field $field, Resource $resource): \Illuminate\View\View
    {
        $formFieldType = Str::lower(class_basename($field));

        if($field->hasHandler()) {
            /** @var HandlerInterface $handler */
            $handler = app($field->getHandler());
        } else {
            $handlerType = !array_key_exists($formFieldType, $this->formFields)
                ? "text"
                : $formFieldType;

            /** @var HandlerInterface $handler */
            $handler = $this->formFields[$handlerType];
        }

        if(!$handler instanceof HandlerInterface) {
            throw new \Exception(sprintf("Handler must be type of [%s], [%s] was found instead.", HandlerInterface::class, gettype($handle)));
        }

        return $handler->handle($field, $resource);
    }

    /**
     * Get all form fields.
     *
     * @return array
     */
    public function formFields(): array
    {
        return $this->formFields;
    }

    /**
     * Check that infinity has been installed.
     *
     * @throws \Exception
     */
    public static function isInfinityInstalled(): bool
    {
        $expectedTables = ['permissions', 'users', 'groups'];
        $missingTables = [];

        foreach($expectedTables as $expectedTable) {
            if(!Schema::hasTable($expectedTable)) {
                $missingTables[] = $expectedTable;
            }
        }

        return empty($missingTables);
    }

    /**
     * Test if Infinity development mode is enabled.
     *
     * @return bool
     */
    public static function isInfinityDevModeEnabled(): bool
    {
        return env('INFINITY_DEV', false);
    }
}
