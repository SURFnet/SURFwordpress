<?php

namespace SURF\Helpers;

use SURF\Plugins\AcfPro\AcfPro;

/**
 * Class ACFHelper
 * @package SURF\Helpers
 */
class ACFHelper
{

	/**
	 * @return bool
	 */
	public static function usesPro(): bool
	{
		return ( new AcfPro() )->isActive();
	}

	/**
	 * @return bool
	 */
	public static function allowsRepeater(): bool
	{
		return class_exists( 'acf_field_repeater' );
	}

	/**
	 * @return array
	 */
	public static function listAllowedFontTypes(): array
	{
		return [ 'otf', 'ttf', 'woff', 'woff2', 'sfnt' ];
	}

	/**
	 * @return array
	 */
	public static function listAllowedFontExtensions(): array
	{
		return array_map( function ( $type )
		{
			return '.' . $type;
		}, static::listAllowedFontTypes() );
	}

}