<?php

if (! function_exists('is_cacheable_class')) {
    function is_cacheable_class(object|string $class): bool
    {
        return Mahmoud217TR\Cacheable\Facades\Cacheable::isCacheableClass($class);
    }
}

if (! function_exists('is_cacheable_model')) {
    function is_cacheable_model(Illuminate\Database\Eloquent\Model|string $model): bool
    {
        return Mahmoud217TR\Cacheable\Facades\Cacheable::isCacheableModel($model);
    }
}

if (! function_exists('cached')) {
    function cached(string $key, $value = null, int|DateInterval|DateTimeInterface|null $ttl = null)
    {
        return Mahmoud217TR\Cacheable\Facades\Cacheable::cached($key, $value, $ttl);
    }
}
