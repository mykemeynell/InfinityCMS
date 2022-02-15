<?php

if(!function_exists('infinity_route_is')) {
    /**
     * Test if the current route is a given value.
     *
     * @param array|string $name
     *
     * @return bool
     */
    function infinity_route_is(array|string $name): bool
    {
        $currentRoute = Illuminate\Support\Facades\Route::getCurrentRoute()->getName();

        $names = collect((array)$name)->map(function($n) {
            return sprintf("infinity.%s", $n);
        })->toArray();

        foreach($names as $routeName) {
            if(fnmatch($routeName, $currentRoute)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('infinity_route')) {
    /**
     * Generate the URL to a named route within Infinity.
     *
     * @param string $name
     * @param mixed|array  $parameters
     * @param bool         $absolute
     *
     * @return string
     */
    function infinity_route(
        string $name,
        array $parameters = [],
        bool $absolute = true
    ): string {
        return route(sprintf("infinity.%s", $name), $parameters, $absolute);
    }
}
