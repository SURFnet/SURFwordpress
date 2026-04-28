<?php

namespace SURF\Hooks;

use SURF\Enums\Theme;
use SURF\Helpers\ACFHelper;

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
		], 10, 3 );

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

		$fonts = Theme::getGlobalRepeaterOption( 'surf_fonts', [ 'name' ] );
		if ( empty( $fonts ) ) {
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
	 * @param array $file
	 * @param array $attachment
	 * @return mixed
	 */
	public static function allowFontUploads( $errors, array $file, array $attachment )
	{
		if ( empty( $file ) ) {
			return $errors;
		}

		$attachment_id = $attachment['id'] ?? 0;
		if ( empty( $attachment_id ) ) {
			return $errors;
		}

		$attachment = get_post( $attachment_id );
		if ( empty( $attachment ) ) {
			return $errors;
		}

		// Simply checking on extension, as we have a strict upload policy
		$file_path = get_attached_file( $attachment_id );
		$extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
		$allowed   = ACFHelper::listAllowedFontTypes();
		if ( in_array( $extension, $allowed ) ) {
			return $errors;
		}

		return [ 'error' => _x( 'Invalid font file type.', 'error', 'wp-surf-theme' ) ];
	}

}
