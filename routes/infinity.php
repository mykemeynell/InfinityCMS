<?php

// Infinity CMS routes.

/** @var \Illuminate\Routing\Router $router */

use Illuminate\Support\Str;

$router = app('router');

$options = [
    'prefix' => '/infinity',
    'as' => 'infinity.',
    'middleware' => ['web'],
];

$namespacePrefix = sprintf("\\%s\\", infinity_config('controllers.namespace'));

$router->group($options, function () use ($router, $namespacePrefix) {
    $router->get('/login', [Infinity\Http\Controllers\InfinityLoginController::class, 'showLogin'])
        ->name('login');
    $router->post('/login', [Infinity\Http\Controllers\InfinityLoginController::class, 'handleLogin'])
        ->name('handleLogin');
    $router->post('/logout', [\Infinity\Http\Controllers\InfinityLoginController::class, 'logout'])
        ->name('handleLogout');

    $router->group(['middleware' => 'admin.user'],
        function () use ($router, $namespacePrefix) {

            $router->get('/', [
                \Infinity\Http\Controllers\InfinityDashboardController::class,
                'showDashboard',
            ])
                ->name('dashboard.show_dashboard');

            try {
                $resources = array_merge(
                    \Infinity\Facades\Infinity::resources(),
                    \Infinity\Facades\Infinity::resources('core')
                );

                // TODO: Move resource search into service provider and bind to service container so can be used throughout
                // The above is because of an issue when using Infinity::resource - the namespace might not always be "App\\Infinity"
                // so could do with creating an instance and then storing it in the application container for later reference.

                foreach ($resources as $resource) {
                    /** @var \Infinity\Resources\Resource $resource */
                    $controller = Str::start($resource->getController(), '\\');

                    $resourceExcludedRoutes = $resource->excludedRoutes();

                    $router->resource($resource->getIdentifier(), $controller, ['parameters' => [$resource->getIdentifier() => 'id']])
                        ->except($resourceExcludedRoutes);

                    $router->group(['prefix' => $resource->getIdentifier()], function () use ($router, $resource, $controller, $resourceExcludedRoutes) {
                        if(!in_array('showDelete', $resourceExcludedRoutes)) {
                            $router->get("delete/{id}", [$controller, 'showDelete'])
                                ->name("{$resource->getIdentifier()}.showDelete");
                        }

                        // Additional routes
                        foreach($resource->additionalRoutes() as $additionalRoute)
                        {
                            /** @var \Infinity\Resources\Routes\AdditionalRoute $additionalRoute */
                            call_user_func_array([$router, $additionalRoute->getMethod()], [
                                $additionalRoute->getUri(),
                                [$controller, $additionalRoute->getAction()]
                            ])->name("{$resource->getIdentifier()}.{$additionalRoute->getName()}");
                        }
                    });
                }
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException(sprintf("Custom routes hasn't been configured because: %s",
                    $e->getMessage()), 1);
            } catch (\Exception $e) {
                // do nothing, might just be because table not yet migrated.
            }

            return $router;
        });

    $router->get('infinity-assets',
        [\Infinity\Http\Controllers\InfinityController::class, 'assets'])
        ->name('infinity_assets');

    return $router;
});
