<?php

namespace Mahmoud217TR\Cacheable\Collections;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Mahmoud217TR\Cacheable\Contracts\Cacheable;
use Mahmoud217TR\Cacheable\Traits\CacheableTrait;

class Collection extends EloquentCollection implements Cacheable
{
    use CacheableTrait;
}
