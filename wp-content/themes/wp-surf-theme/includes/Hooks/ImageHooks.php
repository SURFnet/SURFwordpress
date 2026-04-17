<?php

namespace SURF\Hooks;

use SURF\Enums\Theme;

/**
 * Class ImageHooks
 * @package SURF\Hooks
 */
class ImageHooks
{

	/**
	 * Register image hooks.
	 */
	public static function register()
	{
		add_filter( 'image_resize_dimensions', [ static::class, 'imageUpscale' ], 10, 6 );
		add_filter( 'image_size_names_choose', [ static::class, 'customSizes' ] );
		add_image_size( 'hero-large', 1200, 460, true );
		add_image_size( 'hero-small', 600, 450, true );
		add_image_size( 'post-image-large', 685, 295, true );
		add_image_size( 'post-image-full', 995, 520, true );
		add_image_size( 'avatar', 200, 200, true );
		if ( Theme::isSURF() ) {
			add_image_size( 'post-image', 376, 220, true );
		} else {
			add_image_size( 'post-image', 376, 260, true );
		}
	}

	/**
	 * Always force up scaling images to be able to crop them
	 * @param $default , $origW, $origH, $newW, $newH, $crop
	 * @param $origW
	 * @param $origH
	 * @param $newW
	 * @param $newH
	 * @param $crop
	 * @return int[]|null
	 */
	public static function imageUpscale( $default, $origW, $origH, $newW, $newH, $crop )
	{
		if ( !$crop ) {
			return null;
		}

		$size_ratio = max( $newW / $origW, $newH / $origH );
		$crop_w     = round( $newW / $size_ratio );
		$crop_h     = round( $newH / $size_ratio );
		$s_x        = floor( ( $origW - $crop_w ) / 2 );
		$s_y        = floor( ( $origH - $crop_h ) / 2 );

		return [ 0, 0, (int) $s_x, (int) $s_y, (int) $newW, (int) $newH, (int) $crop_w, (int) $crop_h ];
	}

	/**
	 * Make custom image sizes selectable
	 * @param $sizes
	 * @return array
	 */
	public static function customSizes( $sizes ): array
	{
		return array_merge( $sizes, [
			'hero-large' => _x( 'Hero Large', 'admin', 'wp-surf-theme' ),
			'hero-small' => _x( 'Hero Small', 'admin', 'wp-surf-theme' ),
		] );
	}

}
