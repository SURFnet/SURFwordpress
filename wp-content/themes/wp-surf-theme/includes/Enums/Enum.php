<?php

namespace SURF\Enums;

use Exception;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Class Enum
 * @package SURF\Enums
 */
class Enum
{

	/**
	 * @return array
	 */
	public static function values(): array
	{
		$reflection = new ReflectionClass( static::class );

		return array_values( $reflection->getConstants() );
	}

	/**
	 * @param string $value
	 * @return string
	 * @throws Exception
	 */
	public static function label( string $value ): string
	{
		return static::options()[ $value ] ?? throw new Exception( 'Enum value not found.' );
	}

	/**
	 * @return array
	 */
	public static function labels(): array
	{
		$reflection = new ReflectionClass( static::class );

		return array_map(
			fn( $c ) => Str::of( $c )->replace( '_', ' ' )->title()->toString(),
			array_keys( $reflection->getConstants() )
		);
	}

	/**
	 * @return array
	 */
	public static function options(): array
	{
		return array_combine(
			static::values(),
			static::labels()
		);
	}

}
