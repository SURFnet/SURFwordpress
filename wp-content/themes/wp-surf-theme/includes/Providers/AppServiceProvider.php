<?php

namespace SURF\Providers;

use SURF\Core\ClassLoader;
use SURF\Core\Contracts\ServiceProvider;
use Whoops\Handler\HandlerInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\RunInterface;

/**
 * Class AppServiceProvider
 * @package SURF\Providers
 */
class AppServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register(): void
	{
		$this->app->bind( RunInterface::class, Run::class );
		$this->app->bind( HandlerInterface::class, PrettyPageHandler::class );

		$this->app->singleton( ClassLoader::class, ClassLoader::class );
	}

	/**
	 * @return void
	 */
	public function boot(): void
	{
		$this->registerErrorHandler();
	}

	/**
	 * @return void
	 */
	public function registerErrorHandler(): void
	{
		if ( $this->app->isEnvironment( 'staging', 'production' ) ) {
			return;
		}

		/** @var RunInterface $run */
		$run = surfApp( RunInterface::class )->pushHandler( surfApp( HandlerInterface::class ) );

		set_error_handler( [ $run, 'handleError' ],
			E_ERROR | E_PARSE | E_USER_ERROR | E_COMPILE_ERROR | E_RECOVERABLE_ERROR );
		set_exception_handler( [ $run, 'handleException' ] );
		register_shutdown_function( [ $run, 'handleShutdown' ] );
	}

}
