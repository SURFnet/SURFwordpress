<?php

namespace SURF\Core\PostTypes;

use Exception;
use SURF\Core\DB\AbstractFactory;

/**
 * Trait HasFactory
 * @package SURF\Core\PostTypes
 */
trait HasFactory
{

	protected static string $factoryNamespace = 'SURF\\DB\\Factories';

	/**
	 * Get the factory for the post type.
	 * @return AbstractFactory
	 * @throws Exception
	 */
	public static function factory(): AbstractFactory
	{
		$parts = explode( '\\', static::class );
		$parts = preg_split( '/(?=[A-Z])/', end( $parts ) );
		$name  = implode( '', $parts );

		$factory = static::$factoryNamespace . '\\' . $name . 'Factory';

		if ( !class_exists( $factory ) ) {
			throw new Exception( "Factory '{$factory}' does not exist." );
		}

		return new $factory();
	}

}
