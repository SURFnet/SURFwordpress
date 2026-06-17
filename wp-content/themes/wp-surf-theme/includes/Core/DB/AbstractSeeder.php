<?php

namespace SURF\Core\DB;

use Exception;

/**
 * Class AbstractSeeder
 * @package SURF\Core\DB
 */
abstract class AbstractSeeder
{

	/**
	 * Run the seeder.
	 */
	abstract public function run(): void;

	/**
	 * Call other seeders.
	 * @param string|string[] $classes
	 * @throws Exception
	 */
	public function call( $classes )
	{
		$classes = is_array( $classes ) ? $classes : [ $classes ];

		foreach ( $classes as $class ) {
			$seeder = $this->resolve( $class );
			$seeder->run();
		}
	}

	/**
	 * Create a AbstractSeeder instance from the class name.
	 * @param string $class
	 * @return AbstractSeeder
	 * @throws Exception
	 */
	public function resolve( string $class ): self
	{
		if ( !class_exists( $class ) ) {
			throw new Exception( "Class {$class} does not exist." );
		}

		if ( !is_a( $class, static::class, true ) ) {
			throw new Exception( "Class {$class} does not extend " . static::class );
		}

		if ( $class === static::class ) {
			throw new Exception( 'Seeder cannot call itself.' );
		}

		return new $class;
	}

}
