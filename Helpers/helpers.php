<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

if (!function_exists('infinity_asset')) {
    /**
     * Get an Infinity asset URL.
     *
     * @param $path
     * @param $secure
     *
     * @return string
     */
    function infinity_asset($path, $secure = null): string
    {
        return route('infinity.infinity_assets') . '?path=' . urlencode($path);
    }
}

if(!function_exists('infinity_config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string|null $key
     * @param  mixed            $default
     *
     * @return mixed|\Illuminate\Config\Repository
     */
    function infinity_config(array|string|null $key, mixed $default = null): mixed
    {
        return config(sprintf("infinity.%s", $key), $default);
    }
}

if(!function_exists('glob_recursive'))
{
    /**
     * Recursive alternative to glob.
     *
     * @param $pattern
     * @param int $flags
     *
     * @return bool|array
     */
    function glob_recursive($pattern, int $flags = 0): bool|array
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }

        return $files;
    }
}

if(!function_exists('val'))
{
    /**
     * Transform a value to a casted type.
     *
     * @param $value
     *
     * @return bool|string|null
     */
    function val($value): bool|string|null
    {
        if(is_callable($value)) {
            return $value();
        }

        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $value,
        };
    }
}

if(!function_exists('settings'))
{
    /**
     * Get a setting value from the database.
     *
     * @param string $key
     * @param        $default
     *
     * @return mixed
     */
    function settings(string $key, $default = null): mixed
    {
        static $settings;
        $ttl = infinity_config('cache.config.ttl', 60*60*24);
        $model = \Infinity\Facades\Infinity::model('Setting');

        if(is_null($settings))
        {
            $settings = Cache::has('infinity') && infinity_config('infinity.cache.config.use_cache')
                ? Cache::get('infinity')
                : Cache::remember('infinity', $ttl, function() use ($model) {
                    $settings = $model->all()->map(function(\Infinity\Models\Setting $setting) {
                        $setting->value = $setting->getvalue();
                        return $setting;
                    })->toArray();
                    return Arr::pluck($settings, 'value', 'key');
                });
        }

        if(empty($key)) {
            return $settings;
        }

        return Arr::exists($settings, $key) ? $settings[$key] : $default;
    }
}

if(!function_exists('uploads_path'))
{
    /**
     * Get the uploaded file path with or without a filename.
     *
     * @param string|null $filename
     *
     * @return string
     */
    function uploads_path(?string $filename = null): string
    {
        $storagePath = storage_path('app/public/infinity/uploads');

        if(empty($filename)) {
            return $storagePath;
        }

        return sprintf("%s/%s", $storagePath, $filename);
    }
}

if(!function_exists('uploads_url'))
{
    /**
     * Get the URL to a filename within the uploads directory.
     *
     * @param string $filename
     *
     * @return string
     */
    function uploads_url(string $filename): string
    {
        return url('storage/infinity/uploads/' . $filename);
    }
}
