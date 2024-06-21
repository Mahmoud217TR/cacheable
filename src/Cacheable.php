<?php

namespace Mahmoud217TR\Cacheable;

use Closure;
use DateInterval;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Mahmoud217TR\Cacheable\Contracts\Cacheable as CacheableContract;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;

class Cacheable
{
    protected static ?array $cacheableModels = null;

    protected static ?array $extractedModelsFromConfigDirectories = null;

    /**
     * Determine if an item exists in the cache.
     *
     * @param  array|string  $key
     */
    public static function has($key): bool
    {
        return Cache::has($key);
    }

    /**
     * Determine if an item doesn't exist in the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public static function missing($key)
    {
        return Cache::missing($key);
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @template TCacheValue
     *
     * @param  array|string  $key
     * @param  TCacheValue|(\Closure(): TCacheValue)  $default
     * @return (TCacheValue is null ? mixed : TCacheValue)
     */
    public static function get($key, $default = null): mixed
    {
        return Cache::get($key, $default);
    }

    /**
     * Retrieve an item from the cache and delete it.
     *
     * @template TCacheValue
     *
     * @param  array|string  $key
     * @param  TCacheValue|(\Closure(): TCacheValue)  $default
     * @return (TCacheValue is null ? mixed : TCacheValue)
     */
    public static function pull($key, $default = null)
    {
        return Cache::pull($key, $default);
    }

    /**
     * Store an item in the cache.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public static function put($key, $value, $ttl = null)
    {
        return Cache::put($key, $value, $ttl);
    }

    public static function set($key, $value, $ttl = null): bool
    {
        return Cache::set($key, $value, $ttl);
    }

    /**
     * Remove an item from the cache.
     *
     * @param  string  $key
     * @return bool
     */
    public static function forget($key)
    {
        return Cache::forget($key);
    }

    /**
     * Get a cached value from cache, if not found it put it and get it back.
     *
     * @param  null|mixed|Closure  $value
     */
    public static function cached(string $key, $value = null, int|DateInterval|DateTimeInterface|null $ttl = null)
    {
        $cached = static::get($key);
        if (is_null($cached) && ! is_null($value)) {
            if ($value instanceof Closure) {
                $value = $value();
            }
            static::set($key, $value, $ttl);
            $cached = $value;
        }

        return $cached;
    }

    public static function isCacheableModel(Model|string $model): bool
    {
        return in_array(
            CacheableModel::class,
            class_implements($model)
        );
    }

    public static function isCacheableClass(object|string $class): bool
    {
        return in_array(
            CacheableContract::class,
            class_implements($class)
        );
    }

    public static function config(?string $key = null)
    {
        $config = filled($key) ? "cacheable.{$key}" : 'cacheable';

        return config($config);
    }
}
