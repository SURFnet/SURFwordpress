<?php

use SURF\Providers\AcfServiceProvider;
use SURF\Providers\AppServiceProvider;
use SURF\Providers\BladeCacheServiceProvider;
use SURF\Providers\BlockServiceProvider;
use SURF\Providers\CacheServiceProvider;
use SURF\Providers\DefaultContentServiceProvider;
use SURF\Providers\GithubReleaseServiceProvider;
use SURF\Providers\ImposterServiceProvider;
use SURF\Providers\PdfIndexServiceProvider;
use SURF\Providers\PluginServiceProvider;
use SURF\Providers\PostTypeServiceProvider;
use SURF\Providers\PublicationServiceProvider;
use SURF\Providers\RoleServiceProvider;
use SURF\Providers\ShortcodeServiceProvider;
use SURF\Providers\TaxonomyServiceProvider;
use SURF\Providers\TemplateControllerServiceProvider;
use SURF\Providers\ThemeServiceProvider;
use SURF\Providers\ViewServiceProvider;
use SURF\Providers\ViteServiceProvider;
use SURF\Providers\WidgetServiceProvider;
use SURF\Providers\WordPressServiceProvider;

return [
	'environment' => function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production',
	'debug'       => defined( 'WP_DEBUG' ) && WP_DEBUG,
	'providers'   => [
		WordPressServiceProvider::class,
		AppServiceProvider::class,
		CacheServiceProvider::class,
		ThemeServiceProvider::class,
		PostTypeServiceProvider::class,
		TaxonomyServiceProvider::class,
		BlockServiceProvider::class,
		WidgetServiceProvider::class,
		ShortcodeServiceProvider::class,
		TemplateControllerServiceProvider::class,
		ViewServiceProvider::class,
		BladeCacheServiceProvider::class,
		AcfServiceProvider::class,
		ViteServiceProvider::class,
		GithubReleaseServiceProvider::class,
		PluginServiceProvider::class,
		DefaultContentServiceProvider::class,
		RoleServiceProvider::class,
		PublicationServiceProvider::class,
		PdfIndexServiceProvider::class,
		ImposterServiceProvider::class,

		/*
		 * Uncomment to enable the custom routes in routes/web.php
		 */
		// \SURF\Providers\RoutingServiceProvider::class
	],
	'paths'       => [
		'post_types' => [ 'includes/PostTypes' ],
		'taxonomies' => [ 'includes/Taxonomies' ],
		'blocks'     => [ 'includes/Blocks' ],
		'widgets'    => [ 'includes/Widgets' ],
		'shortcodes' => [ 'includes/Shortcodes' ],
		'imposters'  => [ 'includes/Imposters' ],
	],
];
