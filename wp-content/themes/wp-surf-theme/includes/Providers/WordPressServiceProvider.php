<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;
use WP;
use WP_Query;

/**
 * Class WordPressServiceProvider
 * @package SURF\Providers
 */
class WordPressServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register(): void
	{
		global $wp, $wp_query;
		$this->app->instance( WP::class, $wp );
		$this->app->instance( WP_Query::class, $wp_query );
	}

	/**
	 * @return void
	 */
	public function boot(): void {}

}
