<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Default Cache Store
	|--------------------------------------------------------------------------
	|
	| This option controls the default cache connection that gets used while
	| using this caching library. This connection is used when another is
	| not explicitly specified when executing a given caching function.
	|
	*/
	'default'   => 'file',

	/*
	|--------------------------------------------------------------------------
	| Blade Cache TTL
	|--------------------------------------------------------------------------
	|
	| Default TTL for caching in blade. Value in seconds
	|
	*/
	'blade_ttl' => defined( 'SURF_BLADE_CACHE_TTL' ) ? SURF_BLADE_CACHE_TTL : 1800,

	/*
	|--------------------------------------------------------------------------
	| Cache Stores
	|--------------------------------------------------------------------------
	|
	| Here you may define all the cache "stores" for your application as
	| well as their drivers. You may even define multiple stores for the
	| same cache driver to group types of items stored in your caches.
	|
	| Supported drivers: "file"
	|
	*/
	'stores'    => [
		'file' => [
			'driver' => 'file',
			'path'   => SURF_THEME_DIR . '/cache/data',
		],
	],

];
