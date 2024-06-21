# Cacheable for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mahmoud217tr/cacheable.svg?style=flat-square)](https://packagist.org/packages/mahmoud217tr/cacheable)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mahmoud217tr/cacheable/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mahmoud217tr/cacheable/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mahmoud217tr/cacheable/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mahmoud217tr/cacheable/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mahmoud217tr/cacheable.svg?style=flat-square)](https://packagist.org/packages/mahmoud217tr/cacheable)

**Effortless and Enhanced Caching for Models and Classes**

Laravel package that provides a streamlined and powerful solution for implementing caching within your application. This package simplifies the process of caching Eloquent models and other classes, ensuring improved performance and scalability for your Laravel application.

![logo](/assets/cacheable.svg)

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
    1. [Cacheable Models](#1-cacheable-models)
        1. [Easy Caching for Model Records and Collections](#1-easy-caching-for-model-records-and-collections)
        2. [Auto-Caching Model Records](#2-auto-caching-model-records)
        3. [Cached Route Model Binding](#3-cached-route-model-binding)
    2. [Cacheable Interface & Trait](#2-cacheable-interface--trait)
    3. [Cacheable Facade](#3-cacheable-facade)
    4. [Helper Functions](#4-helper-functions)
- [Publishing](#publishing)
- [Testing](#testing)
- [Changelog](#changelog)
- [Security Vulnerabilities](#security-vulnerabilities)
- [Credits](#credits)
- [License](#license)


## Installation

To install the package to your Laravel project via Composer:

```bash
composer require mahmoud217tr/cacheable
```

And once the installation is complete, the package will be ready up and ready for usage.

## Usage

There is a lot of features and usecases we will summerize them with the following:

### 1. Cacheable Models

You can make the model cacheable by making it implement `CacheableModel` interface and use the `CacheableModelTrait` trait as the following example:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;
use Mahmoud217TR\Cacheable\Models\Traits\CacheableModelTrait;

class Post extends Model implements CacheableModel
{
    use CacheableModelTrait;
}
```

> Upon doing that your model will be using the `Cacheable Collection` as a collection which extends the `Eloquent Collection`, preserving the same logic and providing the model with caching features.

Making a model cacheable will provide you with caching features:

#### 1. Easy Caching for Model Records and Collections

You can easily cache individual model records or collections of records using the `cache` method as follows:

```php
<?php

use App\Models\Post;
use Illuminate\Support\Facades\Cache;
use Mahmoud217TR\Cacheable\Facades;

# Caching the first post for 120 seconds
Post::first()->cache('first_post', 120);

# Caching published posts indefinitely
Post::whereNotNull('published_at')
    ->get()
    ->cache('published_posts');

# Retrieve cached data using the Cache facade
Cache::get('first_post');
Cache::get('published_posts');

# Or using the Cacheable facade
Cacheable::get('first_post');
Cacheable::get('published_posts');
```

#### 2. Auto-Caching Model Records

Models can be auto-cached, meaning all model records will be cached and synchronized upon creation, updating, or deletion.

> **CAUTION**: This behavior may be unsuitable for large models or models with frequent changes. Use it wisely based on your use case.

To enable auto-caching, override the `isAutoCacheSyncEnabled` method in your model to return `true`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;
use Mahmoud217TR\Cacheable\Models\Traits\CacheableModelTrait;

class Post extends Model implements CacheableModel
{
    use CacheableModelTrait;

    public static function isAutoCacheSyncEnabled(): bool
    {
        return true;
    }
}
```

Now, the auto-caching feature will be enabled, and all model records will be cached and updated automatically.

> **IMPORTANT**: If you have modular Laravel application or you've changed your models default director, you'll need to do an [extra step](#model-directories).

You can manage the cached models as follows:

```php
<?php

use App\Models\Post;

# Get cached posts
$posts = Post::getCached();

# Update cached data manually
Post::syncCache();

# Flush cached data
Post::flushCache();

# Set cached data manually
Post::setCache(Post::limit(4)->get());
```

You can also control the auto-cached collection by overriding the `getDataForCaching` method as it's default behaviour is to return the `all` method result:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;
use Mahmoud217TR\Cacheable\Models\Traits\CacheableModelTrait;

class Post extends Model implements CacheableModel
{
    use CacheableModelTrait;

    public function scopePublished(Builder $query)
    {
        $query->whereNotNull('published_at');
    }

    public static function isAutoCacheSyncEnabled(): bool
    {
        return true;
    }

    protected static function getDataForCaching()
    {
        return static::published()
            ->latest()
            ->get();
    }
}
```

And now only the published posts will cached by auto-caching feature and in order of the latest.

You can also control the `TTL` of the cached data by overriding the `getCacheTTL` method:

```php
<?php

namespace App\Models;

use DateInterval;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;
use Mahmoud217TR\Cacheable\Models\Traits\CacheableModelTrait;

class Post extends Model implements CacheableModel
{
    use CacheableModelTrait;

    public function scopePublished(Builder $query)
    {
        $query->whereNotNull('published_at');
    }

    public static function isAutoCacheSyncEnabled(): bool
    {
        return true;
    }

    protected static function getDataForCaching()
    {
        return static::published()
            ->latest()
            ->get();
    }

    protected static function getCacheTTL(): null | int | DateInterval | DateTimeInterface
    {
        return 86400;
    }
}
```

#### 3. Cached Route Model Binding

You can also utilize the cached records to be used in route model binding by simply be using the `CachedRouteBinding` trait:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;
use Mahmoud217TR\Cacheable\Models\Traits\CacheableModelTrait;
use Mahmoud217TR\Cacheable\Models\Traits\CachedRouteBinding;

class Post extends Model implements CacheableModel
{
    use CacheableModelTrait;
    use CachedRouteBinding;

    public function scopePublished(Builder $query)
    {
        $query->whereNotNull('published_at');
    }

    public static function isAutoCacheSyncEnabled(): bool
    {
        return true;
    }

    protected static function getDataForCaching()
    {
        return static::published()
            ->latest()
            ->get();
    }
}
```

> **Note**: Modifying the auto-cached data by overriding the `getDataForCaching` method may result in `404 Not Found` for non-cached model records. Solutions for this scenario will be discussed further.

You can change the cached data that is used for route model binding by overriding 2 methods:
- You should override the `shouldUseDifferentDataForBinding` method to return `true` *(which by default it returns `false`)*.
- And you ou should also override the `getBindingData` method which represents the data collection to be cached *(by default it returns `null`)*.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;
use Mahmoud217TR\Cacheable\Models\Traits\CacheableModelTrait;
use Mahmoud217TR\Cacheable\Models\Traits\CachedRouteBinding;

class Post extends Model implements CacheableModel
{
    use CacheableModelTrait;
    use CachedRouteBinding;

    public function scopePublished(Builder $query)
    {
        $query->whereNotNull('published_at');
    }

    public static function isAutoCacheSyncEnabled(): bool
    {
        return true;
    }

    protected static function getDataForCaching()
    {
        return static::published()
            ->latest()
            ->get();
    }

    protected static function shouldUseDifferentDataForBinding(): bool
    {
        return true;
    }

    protected static function getBindingData()
    {
        return static::all();
    }
}
```

And now the data used for route model binding will be cached with a different key and have different values and will be synconized automatically.

**When** the usage of a different data for binding is enabled, you can control the `TTL` of the route binding cache by overriding the `getBindingCacheTTL` method:
```php
<?php

namespace App\Models;

use DateInterval;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;
use Mahmoud217TR\Cacheable\Models\Traits\CacheableModelTrait;
use Mahmoud217TR\Cacheable\Models\Traits\CachedRouteBinding;

class Post extends Model implements CacheableModel
{
    use CacheableModelTrait;
    use CachedRouteBinding;

    public function scopePublished(Builder $query)
    {
        $query->whereNotNull('published_at');
    }

    public static function isAutoCacheSyncEnabled(): bool
    {
        return true;
    }

    protected static function getDataForCaching()
    {
        return static::published()
            ->latest()
            ->get();
    }

    protected static function shouldUseDifferentDataForBinding(): bool
    {
        return true;
    }

    protected static function getBindingData()
    {
        return static::all();
    }

    protected static function getBindingCacheTTL(): null | int| DateInterval | DateTimeInterface
    {
        return 3600;
    }
}
```

You can also allow the model to use an alternative route binding method by overriding the `shouldUseAlternativeRouteBinding` method to return `true`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;
use Mahmoud217TR\Cacheable\Models\Traits\CacheableModelTrait;
use Mahmoud217TR\Cacheable\Models\Traits\CachedRouteBinding;

class Post extends Model implements CacheableModel
{
    use CacheableModelTrait;
    use CachedRouteBinding;

    public function scopePublished(Builder $query)
    {
        $query->whereNotNull('published_at');
    }

    public static function isAutoCacheSyncEnabled(): bool
    {
        return true;
    }

    protected static function getDataForCaching()
    {
        return static::published()
            ->latest()
            ->get();
    }

    protected static function shouldUseDifferentDataForBinding(): bool
    {
        return true;
    }

    protected static function shouldUseDifferentDataForBinding(): bool
    {
        return true;
    }

    protected static function getBindingData()
    {
        return static::all();
    }

    protected static function shouldUseAlternativeRouteBinding(): bool
    {
        return true;
    }
}
```

The default alternative route binding resolver is the `resolveRouteBinding` method, which can be customized by overriding the `alternativeRouteBinding` method:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Mahmoud217TR\Cacheable\Models\Contracts\CacheableModel;
use Mahmoud217TR\Cacheable\Models\Traits\CacheableModelTrait;
use Mahmoud217TR\Cacheable\Models\Traits\CachedRouteBinding;

class Post extends Model implements CacheableModel
{
    use CacheableModelTrait;
    use CachedRouteBinding;

    public function scopePublished(Builder $query)
    {
        $query->whereNotNull('published_at');
    }

    public static function isAutoCacheSyncEnabled(): bool
    {
        return true;
    }

    protected static function getDataForCaching()
    {
        return static::published()
            ->latest()
            ->get();
    }

    protected static function shouldUseDifferentDataForBinding(): bool
    {
        return true;
    }

    protected static function getBindingData()
    {
        return static::all();
    }

    protected static function shouldUseAlternativeRouteBinding(): bool
    {
        return true;
    }

    protected static function alternativeRouteBinding($value, $field = null)
    {
        return parent::customRouteBinding($value, $field);
    }
}
```

### 2. Cacheable Interface & Trait

You can easily make any thing cacheable by making it implements the `Cacheable` interface and use the `CacahableTrait` trait:

```php
<?php

namespace App\Classes;

use Mahmoud217TR\Cacheable\Contracts\Cacheable;
use Mahmoud217TR\Cacheable\Traits\CacheableTrait;

class MyCustomClass implements Cacheable
{
    use CacheableTrait;

    protected string $customAttribute;

    public function __construct(string $value)
    {
        $this->customAttribute = $value;
    }
}
```

And you can use the cache function to store the object as follows:

```php
<?php

use App\Classes\MyCustomClass;

$object = new MyCustomClass("data");

# Caching object indefinitely
$object->cache('cache_key_2');

# Caching object for 200 seconds
$object->cache('cache_key', 200);

```

### 3. Cacheable Facade

You can also utilize the `Cacheable` facade for some caching features, which has some drived features from the `Cache` facade:

```php
<?php

use App\Classes\MyCustomClass;
use App\Models\Post;
use Mahmoud217TR\Cacheable\Facades\Cacheable;

# --- Drived methods from the Cache facade ---

# Returns true if cached data was found
Cacheable::has('cache_key'); 

# Returns true if cached data was not found
Cacheable::missing('cache_key');

# Returns the cached data, if not found returns a default value
Cacheable::get('cache_key', 'default value');

# Caches data for with a given key for amount of time or indefinitely
Cacheable::put('cache_key', 'data', 20); # Caches data for 20 seconds
Cacheable::set('cache_key', 'data'); # Caches data indefinitely

# Invalidate cached data with a given key
Cacheable::forget('cache_key');

# --- New features ---

# Retrieves cached data by key
# Caches the value in the given value with the key if no data found
# You can also set a time to live for cache
Cacheable::cached('cache_key', 'data', 150);

# Returns true if the given model implements the CacheableModel interface
Cacheable::isCacheableModel(Post::class);
Cacheable::isCacheableModel(Post::first());

# Returns true if the given model implements the Cacheable interface
Cacheable::isCacheableClass(MyCustomClass::class);
Cacheable::isCacheableClass(new MyCustomClass('data'));

# Returns an array of the models that implements CacheableModel interface
Cacheable::getCacheableModels();
```

### 4. Helper Functions

The package will give you a new helper functions:
1. The `is_cacheable_model` helper function which checks if a mode or an instance of it implements the `CacheableModel` interface.
2. The `is_cacheable_class` helper function which checks if a class or an instance of it implements the `Cacheable` interface.
3. The `cached` helper function which retrieves cached data of a given key, but if not found, it will cache the given value by the given key with a given time to live if provided.

```php
<?php

use App\Classes\MyCustomClass;
use App\Models\Post;

# Returns true if the given model implements the CacheableModel interface
is_cacheable_model(Post::class);
is_cacheable_model(Post::first());

# Returns true if the given model implements the Cacheable interface
is_cacheable_class(MyCustomClass::class);
is_cacheable_class(new MyCustomClass('data'));

cached('cached_array_key', [1, 2, 3]) # Returns [1, 2, 3]
cached('cached_array_key') # Returns [1, 2, 3]
cached('cached_string_key', "Hello") # Returns "Hello"
cached('cached_string_key', "Ops") # Returns "Hello" because data was found

```

## Publishing

To customize the package caching behavior you can publish the configuration file by running the following command in your console to copy the config file to your application's config directory:

```bash
php artisan vendor:publish --provider="Mahmoud217TR\Cacheable\CacheableServiceProvider"
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.


## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Mahmoud Mahmoud](https://github.com/Mahmoud217TR)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
