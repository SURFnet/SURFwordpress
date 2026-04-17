<?php

namespace SURF\Helpers;

/**
 * Class ColorHelper
 * @package SURF\Helpers
 */
class ColorHelper
{

	public const COLOR_BLACK    = 'black';
	public const COLOR_BLUE     = 'blue';
	public const COLOR_BLUE_5   = 'blue-5';
	public const COLOR_GREEN    = 'green';
	public const COLOR_GREEN_5  = 'green-5';
	public const COLOR_GREY     = 'grey';
	public const COLOR_ORANGE   = 'orange';
	public const COLOR_ORANGE_5 = 'orange-5';
	public const COLOR_PURPLE   = 'purple';
	public const COLOR_PURPLE_5 = 'purple-5';
	public const COLOR_RED      = 'red';
	public const COLOR_RED_5    = 'red-5';
	public const COLOR_WHITE    = 'white';
	public const COLOR_YELLOW   = 'yellow';

	public const COLOR_PRIMARY    = 'primary';
	public const COLOR_SECONDARY  = 'secondary';
	public const COLOR_TERTIARY   = 'tertiary';
	public const COLOR_QUATERNARY = 'quaternary';

	/**
	 * @param string $name
	 * @return string
	 */
	public static function getHexByName( string $name = '' ): string
	{
		return match ( $name ) {
			static::COLOR_BLACK                              => '#000000',
			static::COLOR_BLUE                               => '#007BC7',
			static::COLOR_BLUE_5                             => '#E6F3FA',
			static::COLOR_RED                                => '#E31E24',
			static::COLOR_RED_5                              => '#FCE9EA',
			static::COLOR_GREEN                              => '#1D8649',
			static::COLOR_GREEN_5                            => '#E8F4ED',
			static::COLOR_GREY                               => '#EFEFEF',
			static::COLOR_ORANGE                             => '#F18700',
			static::COLOR_ORANGE_5                           => '#FEF4E6',
			static::COLOR_PURPLE                             => '#7F1183',
			static::COLOR_PURPLE_5                           => '#F2E6F3',
			static::COLOR_WHITE                              => '#FFFFFF',
			static::COLOR_PRIMARY                            => '#0037dd',
			static::COLOR_SECONDARY                          => '#fedb00',
			static::COLOR_TERTIARY, static::COLOR_QUATERNARY => '#f9f5f2',
			default                                          => '#0077c8',
		};
	}

	/**
	 * @param $hex
	 * @param $percent
	 * @return string
	 */
	public static function colorBrightness( $hex, $percent )
	{
		// Remove hash when it's given
		$hash = '';
		if ( stristr( $hex, '#' ) ) {
			$hex  = str_replace( '#', '', $hex );
			$hash = '#';
		}

		// HEX TO RGB
		$rgb = [ hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) ];

		// Calculate Color
		for ( $i = 0; $i < 3; $i++ ) {
			if ( $percent > 0 ) {
				$rgb[ $i ] = round( $rgb[ $i ] * $percent ) + round( 255 * ( 1 - $percent ) );
			} else {
				$positivePercent = $percent - ( $percent * 2 );
				$rgb[ $i ]       = round( $rgb[ $i ] * ( 1 - $positivePercent ) ); // round($rgb[$i] * (1-$positivePercent));
			}
			if ( $rgb[ $i ] > 255 ) {
				$rgb[ $i ] = 255;
			}
		}

		// Create Hash
		$hex = '';
		for ( $i = 0; $i < 3; $i++ ) {
			$hexDigit = dechex( $rgb[ $i ] );
			if ( strlen( $hexDigit ) == 1 ) {
				$hexDigit = "0" . $hexDigit;
			}
			$hex .= $hexDigit;
		}

		return $hash . $hex;
	}

}
