<?php

namespace Mahmoud217TR\Cacheable\Models\Contracts;

use DateInterval;
use DateTimeInterface;
use Mahmoud217TR\Cacheable\Contracts\Cacheable;

interface CacheableModel extends Cacheable
{
    public static function getCacheKey(): string;

    public static function syncCache(): void;

    public static function flushCache(): void;

    public static function setCache($data, int|DateInterval|DateTimeInterface|null $ttl = null): void;

    public static function getCached();

    public static function isAutoCacheSyncEnabled(): bool;
}
