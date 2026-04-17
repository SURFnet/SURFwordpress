<?php

namespace SURF\Core\Taxonomies;

use SURF\Core\ClassLoader;

/**
 * Class TaxonomyRepository
 * @package SURF\Core\Taxonomies
 */
class TaxonomyRepository
{

	protected array $taxonomies = [];

	/**
	 * @param array $paths
	 */
	public function __construct( array $paths )
	{
		$classes = surfApp( ClassLoader::class )->loadDirectories( $paths );
		foreach ( $classes as $class ) {
			if ( !class_exists( $class ) ) {
				continue;
			}

			if ( is_a( $class, Taxonomy::class, true ) ) {
				$this->taxonomies[ $class::getName() ] = $class;
			}
		}
	}

	/**
	 * @return array
	 */
	public function all(): array
	{
		return $this->taxonomies;
	}

}
