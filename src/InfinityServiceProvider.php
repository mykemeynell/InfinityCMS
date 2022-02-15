<?php

namespace Infinity;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Infinity\Events\ModelChangedEvent;
use Infinity\Exceptions\InfinityAuthorizationException;
use Infinity\Facades\Infinity as InfinityFacade;
use Infinity\Http\Middleware\InfinityAdminMiddleware;
use Infinity\Listeners\SettingsUpdatedListener;
use Infinity\Models\Permission;
use Infinity\Resources;

/**
 * @property \Illuminate\Foundation\Application $application
 */
class InfinityServiceProvider extends ServiceProvider
{
    /**
     * Registered policies.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Suppoerted gates.
     *
     * @var array|string[]
     */
    public static array $gates = [
        "app.access",
        "dashboard.browse",
    ];

    /**
     * Register the application services.
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Infinity', InfinityFacade::class);

        $this->app->singleton(Infinity::PACKAGE_NAME, function () {
            return new Infinity();
        });

        $this->app->singleton('InfinityGuard', function () {
            return config('auth.defaults.guard', 'web');
        });

        $this->loadHelpers();
        $this->registerConfig();
        $this->registerResources();

        // TODO: Register field types.
        $this->registerFormFields();

        if($this->app->runningInConsole()) {
            $this->registerPublishableResources();
             $this->registerConsoleCommands();
        }

         if (!$this->app->runningInConsole() || env('INFINITY_DEV', false)) {
             $this->registerDevelopmentCommands();
         }

         $this->registerCoreCommands();
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot(Router $router)
    {
        self::checkFontAwesome();

        if(!$this->app->runningInConsole()) {
            $this->registerAuthorizationExceptionHandler();
        }

        $this->loadViewsFrom(__DIR__ . '/../resources/views', Infinity::PACKAGE_NAME);
        if(!InfinityFacade::routesHaveLoaded()) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/infinity.php');
        }
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../publishable/lang', Infinity::PACKAGE_NAME);

        if(InfinityFacade::isInfinityInstalled()) {
            $this->loadAuth();
            $router->aliasMiddleware('admin.user',
                InfinityAdminMiddleware::class);
        }

        $this->registerViewComposer();

        $this->registerEvents();
    }

    /**
     * Register the unauthorized exception handler.
     *
     * @return void
     */
    public function registerAuthorizationExceptionHandler(): void
    {
        /** @var ExceptionHandler $exceptionHandler */
        $exceptionHandler = resolve(ExceptionHandler::class);
        $exceptionHandler->renderable(function (InfinityAuthorizationException $e, $request) {
            return $request->wantsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.'
                ], 401)
                : response()->redirectToRoute('infinity.dashboard.show_dashboard')->with([
                    'message'    => __('infinity::generic.not_allowed_to_do_that'),
                    'alert-type' => 'error',
                ]);
        });
    }

    /**
     * Check the FontAwesome config.
     *
     * @throws \Exception
     */
    private static function checkFontAwesome(): void
    {
        if(
            trim(Str::lower(infinity_config('fontawesome.licence', 'free'))) == 'pro' &&
            !Str::contains(infinity_config('fontawesome.src'), 'pro')
        ) {
            throw new \Exception(sprintf("It looks like you're trying to use FontAwesome %s - check your FontAwesome source config [%s].", Str::upper(infinity_config('fontawesome.licence', 'free')), infinity_config('fontawesome.src')));
        }

        if(!Str::endsWith(infinity_config('fontawesome.src'), '.js'))
        {
            throw new \Exception("Please use the SVG version of FontAwesome - the CDN URL should end with .js");
        }

        if(!Str::contains(infinity_config('fontawesome.src'), 'fontawesome.com')) {
            throw new \Exception(sprintf("It doesn't look like you're loading FontAwesome from fontawesome.com... are you sure that [%s] is correct?", infinity_config('fontawesome.src')));
        }
    }

    /**
     * Load the Infinity helpers.
     *
     * @return void
     */
    protected function loadHelpers(): void
    {
        foreach(glob(__DIR__ . '/../Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Register the resources from within the application.
     *
     * @return void
     */
    protected function registerResources()
    {
        // Core resources are unlikely to change, so we can specify those here.
        InfinityFacade::addResource([
            Resources\User::class,
            Resources\Group::class,
            Resources\Setting::class,
        ], 'core');

        foreach(InfinityFacade::findResourceClasses(app_path('Infinity')) as $resource) {
            InfinityFacade::addResource($resource);
        }
    }

    /**
     * Register the form fields.
     *
     * @return void
     */
    protected function registerFormFields()
    {
        $formFields = [
            'text',
            'boolean',
            'relationship',
            'password',
            'wysiwyg',
            'fluid',
//            'number',
//            'hidden'
        ];

        foreach ($formFields as $formField) {
            $className = Str::start(Str::studly("{$formField}_handler"),
                "Infinity\\Resources\\Handlers\\");

            // Test if the handler exists, if it doesn't then we use the Handler class as default and the field is handled as text.
            $class = class_exists($className)
                ? $className
                : Str::start(Str::studly("handler"),
                    "Infinity\\Resources\\Handlers\\");

            if (!InfinityFacade::formFieldRegistered($class)) {
                InfinityFacade::addFormField($class);
            }
        }
    }

    /**
     * Register the view composer.
     *
     * @return void
     */
    protected function registerViewComposer(): void
    {
        View::composer(Infinity::PACKAGE_NAME.'::*', function(\Illuminate\View\View $view) {
            $view->with('coreSidebarResources', collect(InfinityFacade::resources('core')));
            $view->with('sidebarResources', collect(InfinityFacade::resources())->reject(function ($resource) {
                return !auth()->check() || !auth()->user()->can("{$resource->getIdentifier()}.browse");
            }));

            return $view;
        });
    }

    /**
     * Register the configurations.
     *
     * @return void
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../publishable/config/infinity.php',
            Infinity::PACKAGE_NAME
        );
    }

    /**
     * Register publishable resources.
     *
     * @return void
     */
    private function registerPublishableResources(): void
    {
        $publishablePath = dirname(__DIR__).'/publishable';

        $publishable = [
            'config' => [
                "{$publishablePath}/config/infinity.php" => config_path('infinity.php'),
            ],
            'seeds' => [
                "{$publishablePath}/database/seeds/" => database_path(Seed::getFolderName()),
            ],
        ];

        foreach ($publishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }

    /**
     * Load authentication into application.
     *
     * @return void
     */
    private function loadAuth(): void
    {
        foreach (array_merge_unique(self::$gates, $this->fetchGatesFromDatabase()) as $gate) {
            Gate::define($gate, function ($user) use ($gate) {
                return $user->hasPermission($gate);
            });
        }
    }

    /**
     * Fetch all the permissions from the database.
     *
     * @return array
     */
    private function fetchGatesFromDatabase(): array
    {
        /** @var \Infinity\Models\Permission $model */
        $model = app(\Infinity\Facades\Infinity::modelClass('Permission'));

        return $model->newQuery()->select('key')->get()->map(function (Permission $permission) {
            return $permission->getPermissionKey();
        })->toArray();
    }

    /**
     * Register listeners for events throughout infinity.
     *
     * @return void
     */
    private function registerEvents(): void
    {
        Event::listen(ModelChangedEvent::class, SettingsUpdatedListener::class);
    }

    /**
     * Register the commands accessible from the Console.
     *
     * @return void
     */
    private function registerConsoleCommands(): void
    {
        $this->commands(Commands\InfinityInstallCommand::class);
        $this->commands(Commands\CreateAdminUser::class);
    }

    /**
     * Register commands that are required whether the application is running in console or not.
     *
     * @return void
     */
    private function registerCoreCommands(): void
    {
        $this->commands(Commands\MakeResourceCommand::class);
        $this->commands(Commands\MakeModelCommand::class);
        $this->commands(Commands\MakeCardCommand::class);
    }

    /**
     * Register commands for development of Infinity.
     *
     * @return void
     */
    private function registerDevelopmentCommands(): void
    {
        $this->commands(Commands\DebugFlushPermissions::class);
        $this->commands(Commands\CreatePermissionsCommand::class);
        $this->commands(Commands\AssignAllPermissionsToAdminGroupCommand::class);
        $this->commands(Commands\ResetSettingsCommand::class);
    }
}
