<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;

/**
 * Class AcfServiceProvider
 * @package SURF\Providers
 */
class AcfServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register(): void
	{
		add_filter( 'acf/settings/show_admin', fn() => surfConfig( 'acf.show_admin' ) );
	}

}
