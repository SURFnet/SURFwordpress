<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;
use SURF\Services\PluginService;

/**
 * Class PluginServiceProvider
 * @package SURF\Providers
 */
class PluginServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function boot() {}

	/**
	 * @return void
	 */
	public function register()
	{
		/** @var PluginService $pluginService */
		$pluginService = surfApp( PluginService::class );

		foreach ( $pluginService->plugins() as $plugin ) {
			$plugin->register();
		}
	}

}
