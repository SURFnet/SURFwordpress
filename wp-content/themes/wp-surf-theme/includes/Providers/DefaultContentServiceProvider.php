<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;
use SURF\Services\DefaultContentService;

/**
 * Class DefaultContentServiceProvider
 * @package SURF\Providers
 */
class DefaultContentServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function boot(): void {}

	/**
	 * @return void
	 */
	public function register(): void
	{
		/** @var DefaultContentService $service */
		$service = surfApp( DefaultContentService::class );
		if ( !get_option( 'surf_default_config_added' ) ) {
			$service->createPages();
			$service->createMenus();
			$service->setupOptions();
			$service->setupRewriteRules();

			update_option( 'surf_default_config_added', true );
		}
	}

}
