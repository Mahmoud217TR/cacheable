<?php

namespace Mahmoud217TR\Cacheable\Contracts;

use DateInterval;
use DateTimeInterface;

interface Cacheable
{
    public function cache(string $key, int|DateInterval|DateTimeInterface|null $ttl = null);
}
