<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;
use SURF\Core\Taxonomies\TaxonomyRepository;
use WP_Term;

/**
 * Class TaxonomyServiceProvider
 * @package SURF\Providers
 */
class TaxonomyServiceProvider extends ServiceProvider
{

	protected array $taxonomies = [];

	/**
	 * @return void
	 */
	public function register()
	{
		$this->app->bind( TaxonomyRepository::class, function ()
		{
			return new TaxonomyRepository(
				array_map( fn( $path ) => $this->app->path( $path ), surfConfig( 'app.paths.taxonomies' ) )
			);
		} );

		$repo = $this->app[ TaxonomyRepository::class ];

		foreach ( $repo->all() as $taxonomy ) {
			$this->taxonomies[] = $taxonomy;
			$this->app->bind( $taxonomy, function () use ( $taxonomy )
			{
				$object = get_queried_object();

				return is_a( $object, WP_Term::class )
					? $taxonomy::fromTerm( $object )
					: new $taxonomy();
			} );
		}
	}

	/**
	 * @return void
	 */
	public function boot()
	{
		foreach ( $this->taxonomies as $taxonomy ) {
			if ( method_exists( $taxonomy, 'register' ) ) {
				$taxonomy::register();
			}

			if ( method_exists( $taxonomy, 'registerFields' ) ) {
				$taxonomy::registerFields();
			}
		}
	}

}
