<?php

use Mahmoud217TR\Cacheable\Facades\Cacheable;

it('caches data', function () {
    $cacheKey = 'data';
    $data = 1;

    $cache = Cacheable::get($cacheKey);
    expect($cache)->tobeNull();

    $cache = Cacheable::set($cacheKey, $data);
    $cache = Cacheable::get($cacheKey);
    expect($cache)->toEqual($data);
});

it('uses cached method correctly', function () {
    $cacheKey = 'data';
    $data = 'welcome';

    Cacheable::cached($cacheKey, $data);
    $cache = Cacheable::get($cacheKey);
    expect($cache)->toEqual($data);
});
