<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;
use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\PostCollection;
use SURF\Core\PostTypes\PostTypeRepository;
use WP_Query;

/**
 * Class PostTypeServiceProvider
 * @package SURF\Providers
 */
class PostTypeServiceProvider extends ServiceProvider
{

	protected array $postTypes = [];

	/**
	 * @return void
	 */
	public function register(): void
	{
		$this->app->singleton( PostTypeRepository::class, function ()
		{
			return new PostTypeRepository(
				array_map( fn( $path ) => $this->app->path( $path ), surfConfig( 'app.paths.post_types' ) )
			);
		} );

		$repo = $this->app[ PostTypeRepository::class ];

		foreach ( $repo->all() as $postType ) {
			$this->postTypes[] = $postType;
			$this->app->bind( $postType, function () use ( $postType )
			{
				return $postType::fromPost( get_post() );
			} );
		}

		$this->app->bind( PostCollection::class, function ()
		{
			return PostCollection::fromQuery(
				$this->app->make( WP_Query::class )
			);
		} );

		$this->app->bind( BasePost::class, function ()
		{
			return BasePost::fromPost( get_post() );
		} );
	}

	/**
	 * @return void
	 */
	public function boot(): void
	{
		// Loop over each post type and call it's register and registerFields function
		foreach ( $this->postTypes as $postType ) {
			if ( method_exists( $postType, 'isEnabled' ) && !$postType::isEnabled() ) {
				continue;
			}

			if ( method_exists( $postType, 'register' ) ) {
				$postType::register();
			}

			if ( method_exists( $postType, 'registerFields' ) ) {
				$postType::registerFields();
			}

			if ( method_exists( $postType, 'registerWidgetAreas' ) ) {
				$postType::registerWidgetAreas();
			}

			if ( method_exists( $postType, 'registerSettings' ) ) {
				$postType::registerSettings();
			}

			if ( method_exists( $postType, 'registerLocalizedSettings' ) ) {
				$postType::registerLocalizedSettings();
			}

			if ( method_exists( $postType, 'registerTaxonomySettings' ) ) {
				$postType::registerTaxonomySettings();
			}
		}

		add_action( 'admin_init', function ()
		{
			add_settings_section(
				'surf_custom_post_types',
				_x( 'Custom Post Types', 'admin', 'wp-surf-theme' ),
				function ()
				{
					echo _x( 'Select the pages that are used for custom post type archives.', 'admin', 'wp-surf-theme' );
				},
				'reading'
			);
		} );
	}

}
