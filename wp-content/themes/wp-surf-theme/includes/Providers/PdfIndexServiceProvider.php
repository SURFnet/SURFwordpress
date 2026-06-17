<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;
use SURF\Services\PdfIndexService;

/**
 * Class PdfIndexServiceProvider
 * @package SURF\Providers
 */
class PdfIndexServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( PdfIndexService::class, PdfIndexService::class );
	}

	/**
	 * @return void
	 */
	public function boot()
	{
		/** @var PdfIndexService $service */
		$service = surfApp( PdfIndexService::class );
		$service->registerHooks();
	}

}
