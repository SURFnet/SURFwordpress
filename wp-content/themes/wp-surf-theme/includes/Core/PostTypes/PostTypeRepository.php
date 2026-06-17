<?php

namespace SURF\Core\PostTypes;

use SURF\Core\ClassLoader;

/**
 * Class PostTypeRepository
 * @package SURF\Core\PostTypes
 */
class PostTypeRepository
{

	protected array $postTypes = [];

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

			if ( is_a( $class, BasePost::class, true ) ) {
				$this->postTypes[ $class::getName() ] = $class;
			}
		}
	}

	/**
	 * @return array
	 */
	public function all(): array
	{
		return $this->postTypes;
	}

	/**
	 * @param string $name
	 * @return null|string
	 */
	public function find( string $name ): ?string
	{
		return collect( $this->all() )->first(
			fn( $value, $key ) => $key === $name
		);
	}

}
