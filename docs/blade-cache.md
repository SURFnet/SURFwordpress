# Blade Cache Directive

Cache chunks of your Blade markup with ease.

## Usage

We've added a new `@cache` Blade directive. It accepts 2 arguments - the cache key and a TTL.

```blade
@cache('current_time', 30)
    {{ now() }}
@endcache
```

When used inside of a Blade template, the content between the 2 directives will be cached using Laravel's application cache. If a TTL (in seconds) isn't provided, the default TTL of **30 minutes** will be used instead.

If you want to cache the content for a particular model, i.e. a `User` model, you can use string interpolation to change the key.

```blade
@cache("user_profile_{$user->ID}")
    {{ $user->name }}
@endcache
```

When a new user is passed to this view, a separate cache entry will be created.

## Configuration

In `config/cache.php` there is a `blade_ttl` settings which can be overwritten using either this settings file, or by defining `SURF_BLADE_CACHE_TTL` in the `wp-config.php`:

```php
return [
    'blade_ttl' => defined('SURF_BLADE_CACHE_TTL') ? SURF_BLADE_CACHE_TTL : 1800,
];
```
