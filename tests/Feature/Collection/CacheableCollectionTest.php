<?php

use Mahmoud217TR\Cacheable\Collections\Collection;
use Mahmoud217TR\Cacheable\Facades\Cacheable;

it('caches collection', function () {
    $cacheKey = 'collection_cache';

    $cache = Cacheable::get($cacheKey);
    expect($cache)->toBeNull();

    $collection = Collection::make([1, 2, 3]);
    $collection->cache($cacheKey);

    $cache = Cacheable::get($cacheKey);
    expect($cache)->toEqual($collection);
});
