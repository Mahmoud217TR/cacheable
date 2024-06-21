<?php

namespace Mahmoud217TR\Cacheable\Models\Traits;

use DateInterval;
use DateTimeInterface;
use Mahmoud217TR\Cacheable\Facades\Cacheable;

trait CachedRouteBinding
{
    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $records = static::getCachedBindingData();
        $model = null;

        if (filled($records)) {
            $model = $records
                ->where($field ?? $this->getRouteKeyName(), $value)
                ->first();
        }

        if (is_null($model) && static::shouldUseAlternativeRouteBinding()) {
            $model = static::alternativeRouteBinding($value, $field);
        }

        return $model;
    }

    public static function syncBindingCache(): void
    {
        if (static::shouldUseDifferentDataForBinding()) {
            Cacheable::forget(static::getBindingCacheKey());
            static::getCachedBindingData();
        }
    }

    public static function getBindingCacheKey(): string
    {
        return static::getCacheKey().'.ForBinding';
    }

    protected static function getCachedBindingData()
    {
        if (static::shouldUseDifferentDataForBinding()) {
            return Cacheable::cached(
                static::getBindingCacheKey(),
                static::getBindingData(),
                static::getBindingCacheTTL()
            );
        }

        return static::getCached();
    }

    protected static function shouldUseDifferentDataForBinding(): bool
    {
        return false;
    }

    protected static function getBindingData()
    {
        return null;
    }

    protected static function getBindingCacheTTL(): null|int|DateInterval|DateTimeInterface
    {
        return null;
    }

    protected static function shouldUseAlternativeRouteBinding(): bool
    {
        return false;
    }

    protected static function alternativeRouteBinding($value, $field = null)
    {
        return parent::resolveRouteBinding($value, $field);
    }
}
