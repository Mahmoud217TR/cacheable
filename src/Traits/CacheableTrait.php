<?php

namespace Mahmoud217TR\Cacheable\Traits;

use DateInterval;
use DateTimeInterface;
use Mahmoud217TR\Cacheable\Facades\Cacheable;

trait CacheableTrait
{
    public function cache(string $key, int|DateInterval|DateTimeInterface|null $ttl = null): static
    {
        Cacheable::set($key, $this, $ttl);

        return $this;
    }
}
