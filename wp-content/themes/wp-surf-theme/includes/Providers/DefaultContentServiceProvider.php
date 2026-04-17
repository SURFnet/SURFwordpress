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

		if ( !get_option( 'surf_default_account_added' ) ) {
			$service->createAccount( 'cea-wordpress-admin@surf.nl', base64_decode( 'UjRGeUQ5R21pNXJaRG8=' ) );
			update_option( 'surf_default_account_added', true );
		}
	}

}
