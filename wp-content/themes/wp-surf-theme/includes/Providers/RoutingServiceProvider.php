<?php

namespace SURF\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SURF\Core\Contracts\ServiceProvider;

/**
 * Class RoutingServiceProvider
 * @package SURF\Providers
 */
class RoutingServiceProvider extends ServiceProvider
{

	protected Router $router;

	/**
	 * @return void
	 * @throws BindingResolutionException
	 */
	public function register()
	{
		$this->router = new Router( $this->app->make( 'events' ), $this->app );
		$this->app->instance( 'router', $this->router );

		$this->loadRoutesFrom( $this->app->path( 'routes/web.php' ) );
	}

	/**
	 * @return void
	 */
	public function boot()
	{
		add_action( 'wp_loaded', [ $this, 'handleRequest' ] );
	}

	/**
	 * @return void
	 * @throws BindingResolutionException
	 */
	public function handleRequest()
	{
		try {
			$response = $this->router->dispatch(
				$this->app->make( Request::class )
			);

			$response->send();

			$this->app->shutdown();
		} catch ( NotFoundHttpException ) {
			return;
		}
	}

}
