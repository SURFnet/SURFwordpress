# Laravel's Cache

By default we've implemented Laravel's Caching system. This can be easiliy used instead of WP transients.

Check de full documentation [here](https://laravel.com/docs/9.x/cache#cache-usage)

## Cache Usage

### Obtaining A Cache Instance
To obtain a cache store instance, you may use the Cache facade, which is what we will use throughout this documentation. The Cache facade provides convenient, terse access to the underlying implementations of the Laravel cache contracts:
```php
use Illuminate\Support\Facades\Cache;

$value = Cache::get('key');
```

### Retrieving Items From The Cache
The Cache facade's get method is used to retrieve items from the cache. If the item does not exist in the cache, null will be returned. If you wish, you may pass a second argument to the get method specifying the default value you wish to be returned if the item doesn't exist:

```php
$value = Cache::get('key');
$value = Cache::get('key', 'default');
```
You may even pass a closure as the default value. The result of the closure will be returned if the specified item does not exist in the cache. Passing a closure allows you to defer the retrieval of default values from a database or other external service:

```php
$value = Cache::get("event_{$event->ID}", function () use ($event) {
    return Event::find($event->ID);
});
```

### Checking For Item Existence
The has method may be used to determine if an item exists in the cache. This method will also return false if the item exists but its value is null:

```php
if (Cache::has('key')) {
    // Has cache for 'key'
}
```

### Retrieve & Store
Sometimes you may wish to retrieve an item from the cache, but also store a default value if the requested item doesn't exist. For example, you may wish to retrieve all users from the cache or, if they don't exist, retrieve them from the database and add them to the cache. You may do this using the `Cache::remember` method:

```php
$value = Cache::remember('events', $seconds, function () {
    return Events::all();
});
```

If the item does not exist in the cache, the closure passed to the `remember` method will be executed and its result will be placed in the cache.

### Retrieve & Delete
If you need to retrieve an item from the cache and then delete the item, you may use the pull method. Like the get method, null will be returned if the item does not exist in the cache:

```php
$value = Cache::pull('key');
```

## Storing Items In The Cache
You may use the put method on the Cache facade to store items in the cache:

```php
Cache::put('key', 'value', $seconds = 10);
```
If the storage time is not passed to the put method, the item will be stored indefinitely:

```php
Cache::put('key', 'value');
```

## Removing Items From The Cache
You may remove items from the cache using the forget method:

```php
Cache::forget('key');
```
You may also remove items by providing a zero or negative number of expiration seconds:

```php
Cache::put('key', 'value', 0);
Cache::put('key', 'value', -5);
```
You may clear the entire cache using the flush method:

```php
Cache::flush();
```

