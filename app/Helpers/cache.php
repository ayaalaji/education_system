<?php

use Illuminate\Support\Facades\Cache;

if (!function_exists('cacheData')) {
    /**
     * Cache data with a given key or retrieve it if already cached.
     *
     * @param string $key
     * @param \Closure $callback
     * @param int $minutes
     * @return mixed
     */
    function cacheData(string $key, \Closure $callback, int $minutes = 30)
    {
        return Cache::remember($key, now()->addMinutes($minutes), $callback);
    }
}


