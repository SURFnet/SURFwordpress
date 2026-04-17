<?php

namespace SURF\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use SURF\Core\Contracts\ServiceProvider;
use SURF\Services\GithubReleaseService;
use SURF\Services\UpdateService;

/**
 * Class GithubReleaseServiceProvider
 * @package SURF\Providers
 */
class GithubReleaseServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register(): void
	{
		// Bind the GitHub provider singleton
		$this->app->singleton( GithubReleaseService::class, function ()
		{
			// GH_PAT is optional; without it, the GitHub API allows 60 unauthenticated
			// requests per hour. Setting a Personal Access Token raises this to 5.000/hour.
			$pat = $_ENV['GH_PAT'] ?? null;

			return new GithubReleaseService( $pat );
		} );

		// Register the UpdateService with the GitHub provider injected
		$this->app->singleton( UpdateService::class, function ( $app )
		{
			$release_service = $app->make( GithubReleaseService::class );
			$theme           = wp_get_theme();
			$theme_slug      = $theme->get_stylesheet();
			$zip_name        = $theme_slug . '.zip';

			return new UpdateService( $release_service, $zip_name, $theme_slug );
		} );
	}

	/**
	 * @return void
	 * @throws BindingResolutionException
	 */
	public function boot(): void
	{
		// Init the UpdateService hooks
		$service = $this->app->make( UpdateService::class );
		$service->init();
	}

}
