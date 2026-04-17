<?php

namespace SURF\Helpers;

use GFAPI;

/**
 * Class GFHelper
 * @package SURF\Helpers
 */
class GFHelper
{

	/**
	 * @return bool
	 */
	public static function isEnabled(): bool
	{
		return class_exists( 'GFAPI' );
	}

	/**
	 * @return array
	 */
	public static function getAll(): array
	{
		if ( !static::isEnabled() ) {
			return [];
		}

		return GFAPI::get_forms();
	}

	/**
	 * @return array
	 */
	public static function getChoices(): array
	{
		if ( !static::isEnabled() ) {
			return [];
		}

		$forms = static::getAll();

		$choices = array_reduce( $forms, function ( $carry, $form )
		{
			$carry[ $form['id'] ] = $form['title'];

			return $carry;
		}, [] );

		uasort( $choices, static fn( $a, $b ) => strtolower( $a ) > strtolower( $b ) ? 1 : -1 );

		return $choices;
	}

}
