<?php

namespace Mahmoud217TR\Cacheable\Models\Traits;

use DateInterval;
use DateTimeInterface;
use Mahmoud217TR\Cacheable\Collections\Collection as CacheableCollection;
use Mahmoud217TR\Cacheable\Facades\Cacheable;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;
use Mahmoud217TR\Cacheable\Traits\CacheableTrait;

trait CacheableModelTrait
{
    use CacheableTrait;

    public static function bootCacheableModelTrait(): void
    {
        if (static::isAutoCacheSyncEnabled()) {
            static::created(function (CacheableModel $model) {
                $model->syncCache();
            });

            static::updated(function (CacheableModel $model) {
                $model->syncCache();
            });

            static::deleted(function (CacheableModel $model) {
                $model->syncCache();
            });
        }
    }

    public static function getCacheKey(): string
    {
        return static::class;
    }

    public static function syncCache(): void
    {
        static::flushCache();
        static::setCache(static::getDataForCaching(), static::getCacheTTL());
        if (static::usesCachedRouteBinding()) {
            static::syncBindingCache();
        }
    }

    public static function flushCache(): void
    {
        Cacheable::forget(static::getCacheKey());
    }

    public static function setCache($data, int|DateInterval|DateTimeInterface|null $ttl = null): void
    {
        Cacheable::set(
            static::getCacheKey(),
            $data,
            $ttl
        );
    }

    public static function getCached()
    {
        return Cacheable::cached(
            static::getCacheKey(),
            fn () => static::getDataForCaching(),
            static::getCacheTTL()
        );
    }

    public static function isAutoCacheSyncEnabled(): bool
    {
        return Cacheable::config('auto_model_caching');
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new CacheableCollection($models);
    }

    protected static function getDataForCaching()
    {
        return static::all();
    }

    protected static function getCacheTTL(): null|int|DateInterval|DateTimeInterface
    {
        return null;
    }

    protected static function usesCachedRouteBinding(): bool
    {
        return in_array(
            CachedRouteBinding::class,
            array_keys(class_uses(static::class))
        );
    }
}
