<?php

namespace SURF\Core\View;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\Factory;

/**
 * Class Template
 * @package SURF\Core\View
 */
class Template
{

	/**
	 * Check if a view exists.
	 * @param string $name
	 * @return bool
	 * @throws BindingResolutionException
	 */
	public static function exists( string $name ): bool
	{
		return surfApp( Factory::class )->exists( $name );
	}

	/**
	 * Render a view.
	 * @param string $name - The filename of the file (relative from the theme root and without extension)
	 * @param array $args  - The arguments that will be made available in the template part
	 * @param bool $return - Returns the output instead of echoing if true
	 * @return string|null
	 */
	public static function render( string $name, array $args = [], bool $return = false ): ?string
	{
		$output = (string) surfApp( Factory::class )->make( $name, $args );
		if ( $return ) {
			return $output;
		}

		echo $output;

		return null;
	}

}
