<?php

namespace Mahmoud217TR\Cacheable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool has(array|string $key)
 * @method static bool missing(string $key)
 * @method static mixed get(array|string $key, mixed|\Closure $default = null)
 * @method static bool put(array|string $key, mixed $value, \DateTimeInterface|\DateInterval|int|null $ttl = null)
 * @method static bool set(string $key, mixed $value, \DateTimeInterface|\DateInterval|int|null $ttl = null)
 * @method static bool forget(string $key)
 * @method static mixed cached(string $key, mixed|Closure $value = null, \DateTimeInterface|\DateInterval|int|null $ttl = null)
 * @method static bool isCacheableModel(Illuminate\Database\Eloquent\Model|string $model)
 * @method static bool isCacheableClass(object|string $class)
 * @method static mixed config(string $key = null)
 * @see \Mahmoud217TR\Cacheable\Cacheable
 */
class Cacheable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mahmoud217TR\Cacheable\Cacheable::class;
    }
}
