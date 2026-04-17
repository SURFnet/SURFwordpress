<?php

namespace SURF\Hooks;

use SURF\Enums\Theme;
use SURF\Helpers\ACFHelper;
use WP_Error;

/**
 * Class ConfigHooks
 * @package SURF\Hooks
 */
class AcfHooks
{

	/**
	 * Register configuration hooks
	 * @return void
	 */
	public static function register(): void
	{
		add_filter( 'acf/load_field/name=surf_theme_font', [ static::class, 'populateFontOptions' ] );
		add_filter( 'acf/load_field/name=surf_theme_heading_font', [ static::class, 'populateFontOptions' ] );
		add_filter( 'acf/validate_attachment/key=field_theme_settings_fonts_file', [
			static::class,
			'allowFontUploads',
		], 10, 2 );

		$headings = HeadingHooks::$headings;
		foreach ( $headings as $heading ) {
			$field_key = 'field_theme_settings_site_heading_font_' . $heading;
			add_filter( 'acf/load_field/key=' . $field_key, [ static::class, 'populateFontOptions' ] );
		}
	}

	/**
	 * @param array $field
	 * @return array
	 */
	public static function populateFontOptions( array $field = [] ): array
	{
		$field['choices'] = [
			false => _x( 'Default', 'admin', 'wp-surf-theme' ),
		];

		$fonts = Theme::getGlobalOption( 'surf_fonts' );
		if ( empty( $fonts ) || !is_array( $fonts ) ) {
			return $field;
		}

		foreach ( $fonts as $font ) {
			$font_name = $font['name'];
			if ( empty( $font_name ) ) {
				continue;
			}

			$field['choices'][ surfSlugify( $font_name ) ] = $font_name;
		}

		return $field;
	}

	/**
	 * @param $errors
	 * @param mixed|null $attachment_id
	 * @return mixed
	 */
	public static function allowFontUploads( $errors, $attachment_id = null )
	{
		if ( empty( $attachment_id ) ) {
			return $errors;
		}

		$allowed   = ACFHelper::listAllowedFontTypes();
		$mime      = get_post_mime_type( $attachment_id );
		$file_name = get_post( $attachment_id )->post_title . '.' . pathinfo( get_attached_file( $attachment_id ), PATHINFO_EXTENSION );
		foreach ( $allowed as $type ) {
			if ( str_ends_with( $mime, '-' . $type ) || str_ends_with( $mime, '/' . $type ) ) {
				return $errors;
			}
			if ( str_ends_with( strtolower( $file_name ), '.' . $type ) ) {
				return $errors;
			}
		}

		return [ 'error' => _x( 'Invalid font file type.', 'error', 'wp-surf-theme' ) ];
	}

}
