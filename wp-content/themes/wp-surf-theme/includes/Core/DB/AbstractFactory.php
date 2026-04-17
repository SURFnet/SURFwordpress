<?php

namespace SURF\Core\DB;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Illuminate\Support\Collection;
use SURF\Core\PostTypes\BasePost;

/**
 * Class AbstractFactory
 * @package SURF\Core\DB
 */
abstract class AbstractFactory
{

	protected string $postType;

	protected int $count = 1;

	protected array $defaults = [
		'meta'     => [ 'made_in_factory' => true ],
		'taxonomy' => [],
	];

	/**
	 * @param Generator $faker
	 * @return array
	 */
	abstract public function definition( Generator $faker ): array;

	/**
	 * Set the amount of posts the factory should create.
	 * @param int $count
	 * @return $this
	 */
	public function count( int $count ): self
	{
		$this->count = $count;

		return $this;
	}

	/**
	 * Create posts using the factory definitions and provided attributes.
	 * @param array $attributes Overwrites default factory definitions
	 * @return Collection|null
	 */
	public function make( array $attributes = [] ): ?Collection
	{
		if ( !method_exists( $this->postType, 'create' ) ) {
			return null;
		}

		$faker = FakerFactory::create( get_locale() );

		// Create a collection of posts
		return collect( array_map( function () use ( $attributes, $faker )
		{
			// Merge Factory definition with attributes parameter
			$merged = array_merge_recursive( $this->defaults, $this->definition( $faker ), $attributes );

			[ $merged, $meta, $taxonomy ] = $this->separate( $merged, 'meta', 'taxonomy' );

			// Create array for the final attributes
			$final = [ 'meta' => [] ];

			// Fill final array with attributes
			foreach ( $merged as $key => $value ) {
				$final[ $key ] = is_callable( $value ) ? $value() : $value;
			}

			// Fill final array with meta data
			foreach ( $meta as $key => $value ) {
				$final['meta'][ $key ] = is_callable( $value ) ? $value() : $value;
			}

			foreach ( $taxonomy as $key => $value ) {
				$final['taxonomy'][ $key ] = is_callable( $value ) ? $value() : $value;
			}

			if (
				class_exists( $this->postType ) &&
				is_a( $this->postType, BasePost::class, true )
			) {
				return $this->postType::create( $final );
			}
		}, range( 1, $this->count ) ) );
	}

	/**
	 * @param array $array
	 * @param ...$keys
	 * @return array
	 */
	public function separate( array $array, ...$keys ): array
	{
		$result = [];
		foreach ( $keys as $key ) {
			$result[] = $array[ $key ];
			unset( $array[ $key ] );
		}
		array_unshift( $result, $array );

		return $result;
	}

}
