<?php

namespace SURF;

use Dotenv\Dotenv;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory;
use JetBrains\PhpStorm\NoReturn;
use SURF\Core\Config;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

/**
 * Class Application
 * @package SURF
 */
class Application extends Container
{

	/** @var ServiceProvider[] */
	protected array  $providers;
	protected bool   $booted               = false;
	protected string $themePath;
	protected array  $composerJson;
	protected array  $terminatingCallbacks = [];

	/**
	 * @return void
	 */
	public function boot()
	{
		$this->themePath = SURF_THEME_DIR;
		$this->registerEnvironment();
		$this->registerFacades();
		$this->registerBindings();
		$this->registerProviders( surfConfig( 'app.providers' ) );
		$this->bootProviders();
	}

	/**
	 * @return void
	 */
	public function registerEnvironment(): void
	{
		$base = ABSPATH;
		$path = $base . '/.env';
		if ( file_exists( $path ) ) {
			$dotenv = Dotenv::createImmutable( $base );
			$dotenv->safeLoad(); // load only if .env exists
		}
	}

	/**
	 * @return void
	 */
	public function registerFacades(): void
	{
		Facade::clearResolvedInstances();
		Facade::setFacadeApplication( $this );
	}

	/**
	 * @return void
	 */
	public function registerBindings(): void
	{
		$this->instance( 'app', $this );

		$this->singleton( 'config', function ()
		{
			return Config::load( [ SURF_THEME_DIR . '/config' ] );
		} );

		$this->singleton( 'request', function ()
		{
			return Request::capture();
		} );

		$this->singleton( 'events', function ()
		{
			return new Dispatcher( $this );
		} );

		$this->singleton( 'files', function ()
		{
			return new Filesystem();
		} );

		foreach (
			[
				'app'     => [ static::class, Container::class, ApplicationContract::class ],
				'config'  => [ Config::class, Repository::class ],
				'events'  => [ Dispatcher::class, \Illuminate\Contracts\Events\Dispatcher::class ],
				'files'   => [ Filesystem::class ],
				'request' => [ Request::class, \Symfony\Component\HttpFoundation\Request::class ],
				'view'    => [ Factory::class, \Illuminate\Contracts\View\Factory::class ],
			] as $key => $aliases
		) {
			foreach ( $aliases as $alias ) {
				$this->alias( $key, $alias );
			}
		}
	}

	/**
	 * @param array $providers
	 * @return void
	 */
	public function registerProviders( array $providers = [] ): void
	{
		if ( isset( $this->providers ) ) {
			return;
		}

		foreach ( $providers as $provider ) {
			$this->register( $provider );
		}
	}

	/**
	 * @param callable $callback
	 * @return void
	 */
	public function terminating( callable $callback ): void
	{
		$this->terminatingCallbacks[] = $callback;
	}

	/**
	 * @return void
	 */
	public function runTerminatingCallbacks(): void
	{
		foreach ( $this->terminatingCallbacks as $callback ) {
			$callback();
		}
	}

	/**
	 * @param $provider
	 * @return ServiceProvider
	 */
	public function register( $provider ): ServiceProvider
	{
		if ( is_string( $provider ) ) {
			$provider = new $provider( $this );
		}

		$this->providers[] = $provider;
		$provider->register();

		return $provider;
	}

	/**
	 * @return void
	 */
	public function bootProviders(): void
	{
		if ( $this->isBooted() ) {
			return;
		}

		foreach ( $this->providers as $provider ) {
			if ( method_exists( $provider, 'boot' ) ) {
				$this->call( [ $provider, 'boot' ] );
			}
		}
	}

	/**
	 * @return bool
	 */
	public function isBooted(): bool
	{
		return $this->booted;
	}

	#[NoReturn]
	/**
	 * Shuts down the application
	 * @return void
	 */
	public function shutdown(): void
	{
		exit;
	}

	/**
	 * Gets the path to the theme directory
	 * @param string $path
	 * @return string
	 */
	public function path( string $path = '' )
	{
		return $this->themePath . ( $path ? '/' . $path : $path );
	}

	/**
	 * Gets the path to the WP root directory
	 * @param string $path
	 * @return string
	 */
	public function rootPath( string $path = '' )
	{
		return ABSPATH . $path;
	}

	/**
	 * @return Config
	 * @throws BindingResolutionException
	 */
	public function config(): Config
	{
		return $this->make( 'config' );
	}

	/**
	 * Get the current environment (local, development, staging, production)
	 * @return string
	 */
	public function environment(): string
	{
		if ( function_exists( 'wp_get_environment_type' ) ) {
			return wp_get_environment_type();
		}

		return 'console';
	}

	/**
	 * @param ...$environments
	 * @return bool
	 */
	public function isEnvironment( ...$environments ): bool
	{
		return in_array( $this->environment(), $environments );
	}

	/**
	 * @return bool
	 */
	public function isLocal(): bool
	{
		return $this->isEnvironment( 'local' );
	}

	/**
	 * @return bool
	 */
	public function isDevelopment(): bool
	{
		return $this->isEnvironment( 'development' );
	}

	/**
	 * @return bool
	 */
	public function isProduction(): bool
	{
		return $this->isEnvironment( 'production' );
	}

	/**
	 * @return bool
	 */
	public function isDebug(): bool
	{
		return surfConfig( 'app.debug', false );
	}

	/**
	 * @return string
	 */
	public function getNamespace(): string
	{
		$composer   = $this->getComposerJson();
		$namespaces = array_keys( $composer['autoload']['psr-4'] );

		return $namespaces[0] ?? __NAMESPACE__ . '\\';
	}

	/**
	 * @return string
	 */
	public function getNamespaceDirectory(): string
	{
		$composer    = $this->getComposerJson();
		$directories = array_values( $composer['autoload']['psr-4'] );

		if ( !isset( $directories[0] ) ) {
			return get_template_directory() . '/includes';
		}

		return SURF_THEME_DIR . '/' . $directories[0];
	}

	/**
	 * @return array
	 */
	public function getComposerJson(): array
	{
		if ( !isset( $this->composerJson ) ) {
			$this->composerJson = json_decode(
				file_get_contents( $this->path( 'composer.json' ) ),
				true
			);
		}

		return $this->composerJson;
	}

}
