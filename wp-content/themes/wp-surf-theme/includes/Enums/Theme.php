<?php

namespace SURF\Enums;

use SURF\Helpers\ColorHelper;
use SURF\Helpers\PolylangHelper;
use SURF\Helpers\PostHelper;
use SURF\Hooks\HeadingHooks;

/**
 * Class Theme
 * @package SURF\Enums
 */
class Theme extends Enum
{

	public const SURF            = 'surf';
	public const POWERED_BY_SURF = 'powered';

	/**
	 * @return bool
	 */
	public static function isSetup(): bool
	{
		return (bool) static::getGlobalOption( 'surf_theme' );
	}

	/**
	 * @return string
	 */
	public static function current(): string
	{
		return static::getGlobalOption( 'surf_theme' ) ?: static::SURF;
	}

	/**
	 * @param string $theme
	 * @return bool
	 */
	public static function is( string $theme ): bool
	{
		return static::current() === $theme;
	}

	/**
	 * @return bool
	 */
	public static function isPoweredBy(): bool
	{
		return static::current() === static::POWERED_BY_SURF;
	}

	/**
	 * @return bool
	 */
	public static function isSURF(): bool
	{
		return static::current() === static::SURF;
	}

	/**
	 * @return string
	 */
	public static function bodyClass(): string
	{
		return implode( ' ', [
			'theme-' . static::current(),
			'image-type-' . static::getImageType(),
			'button-type-' . static::getButtonType(),
		] );
	}

	/**
	 * Gets a theme option based on the current language
	 * @param string $key
	 * @return mixed
	 */
	public static function getOption( string $key ): mixed
	{
		return PolylangHelper::getThemeOption( $key );
	}

	/**
	 * Gets a repeater theme option based on the current language
	 * @param string $key
	 * @param array $subFields
	 * @return array
	 */
	public static function getRepeaterOption( string $key, array $subFields ): array
	{
		return PolylangHelper::getRepeaterThemeOption( $key, $subFields );
	}

	/**
	 * Gets a global theme option
	 * @param string $key
	 * @return mixed
	 */
	public static function getGlobalOption( string $key ): mixed
	{
		return PolylangHelper::getGlobalThemeOption( $key );
	}

	/**
	 * Gets a global repeater theme option
	 * @param string $key
	 * @param array $subFields
	 * @return array
	 */
	public static function getGlobalRepeaterOption( string $key, array $subFields ): array
	{
		return PolylangHelper::getGlobalRepeaterThemeOption( $key, $subFields );
	}

	/**
	 * @return string
	 */
	public static function primaryColor(): string
	{
		$default = ColorHelper::getHexByName( ColorHelper::COLOR_PRIMARY );

		return static::getGlobalOption( 'surf_theme_primary_color' ) ?: $default;
	}

	/**
	 * @return string
	 */
	public static function secondaryColor(): string
	{
		$default = ColorHelper::getHexByName( ColorHelper::COLOR_SECONDARY );

		return static::getGlobalOption( 'surf_theme_secondary_color' ) ?: $default;
	}

	/**
	 * @return string
	 */
	public static function tertiaryColor(): string
	{
		$default = ColorHelper::getHexByName( ColorHelper::COLOR_TERTIARY );

		return static::getGlobalOption( 'surf_theme_tertiary_color' ) ?: $default;
	}

	/**
	 * @return string
	 */
	public static function quaternaryColor(): string
	{
		$default = ColorHelper::getHexByName( ColorHelper::COLOR_QUATERNARY );

		return static::getGlobalOption( 'surf_theme_quaternary_color' ) ?: $default;
	}

	/**
	 * @return string
	 */
	public static function headerColor(): string
	{
		return static::getGlobalOption( 'surf_theme_header_background_color' ) ?: 'var(--surf-color-white)';
	}

	/**
	 * @return string
	 */
	public static function footerColor(): string
	{
		$default = ColorHelper::getHexByName( ColorHelper::COLOR_TERTIARY );

		return static::getGlobalOption( 'surf_theme_footer_background_color' ) ?: $default;
	}

	/**
	 * @return string
	 */
	public static function formColor(): string
	{
		$default = ColorHelper::getHexByName( ColorHelper::COLOR_TERTIARY );

		return static::getGlobalOption( 'surf_theme_form_background_color' ) ?: $default;
	}

	/**
	 * @return string
	 */
	public static function backgroundColor(): string
	{
		$default = ColorHelper::getHexByName( ColorHelper::COLOR_TERTIARY );

		return static::getGlobalOption( 'surf_theme_background_color' ) ?: $default;
	}

	/**
	 * @return string
	 */
	public static function altColor(): string
	{
		return ColorHelper::colorBrightness( static::primaryColor(), -0.3 );
	}

	/**
	 * @return array
	 */
	public static function logos(): array
	{
		$key    = 'surf_logos';
		$fields = [ 'logo' => 0, 'logo_size' => '' ];
		$list   = static::getRepeaterOption( $key, $fields );
		if ( empty( $list ) ) {
			$list = static::getGlobalRepeaterOption( $key, $fields );
			if ( empty( $list ) ) {
				return [];
			}
		}

		foreach ( $list as $row => $values ) {
			$logoID = $values['logo'];
			if ( empty( $logoID ) ) {
				unset( $list[ $row ] );
			}

			$list[ $row ]['logo_id'] = $values['logo'];
		}

		return $list;
	}

	/**
	 * @return array[]
	 */
	public static function colorPalette(): array
	{
		$list = [
			[
				'name'   => __( 'SURF colors', 'wp-surf-theme' ),
				'slug'   => 'surf-colors',
				'colors' => [
					[
						'name'  => _x( 'Blue', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_BLUE,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_BLUE ),
					],
					[
						'name'  => _x( 'Red', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_RED,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_RED ),
					],
					[
						'name'  => _x( 'Green', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_GREEN,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_GREEN ),
					],
					[
						'name'  => _x( 'Orange', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_ORANGE,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_ORANGE ),
					],
					[
						'name'  => _x( 'Purple', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_PURPLE,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_PURPLE ),
					],
				],
			],
			[
				'name'   => _x( '5% colors', 'admin', 'wp-surf-theme' ),
				'slug'   => 'five-percent-colors',
				'colors' => [
					[
						'name'  => _x( 'Blue 5%', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_BLUE_5,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_BLUE_5 ),
					],
					[
						'name'  => _x( 'Red 5%', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_RED_5,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_RED_5 ),
					],
					[
						'name'  => _x( 'Green 5%', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_GREEN_5,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_GREEN_5 ),
					],
					[
						'name'  => _x( 'Orange 5%', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_ORANGE_5,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_ORANGE_5 ),
					],
					[
						'name'  => _x( 'Purple 5%', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_PURPLE_5,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_PURPLE_5 ),
					],
				],
			],
			[
				'name'   => _x( 'Neutral colors', 'admin', 'wp-surf-theme' ),
				'slug'   => 'neutral-colors',
				'colors' => [
					[
						'name'  => _x( 'Black', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_BLACK,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_BLACK ),
					],
					[
						'name'  => _x( 'Grey', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_GREY,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_GREY ),
					],
					[
						'name'  => _x( 'White', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_WHITE,
						'color' => ColorHelper::getHexByName( ColorHelper::COLOR_WHITE ),
					],
				],
			],
		];

		if ( static::isPoweredBy() ) {
			$list[] = [
				'name'   => _x( 'Powered by colors', 'admin', 'wp-surf-theme' ),
				'slug'   => 'powered-by-colors',
				'colors' => [
					[
						'name'  => _x( 'Primary', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_PRIMARY,
						'color' => static::primaryColor(),
					],
					[
						'name'  => _x( 'Secondary', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_SECONDARY,
						'color' => static::secondaryColor(),
					],
					[
						'name'  => _x( 'Tertiary', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_TERTIARY,
						'color' => static::tertiaryColor(),
					],
					[
						'name'  => _x( 'Quaternary', 'admin', 'wp-surf-theme' ),
						'slug'  => ColorHelper::COLOR_QUATERNARY,
						'color' => static::quaternaryColor(),
					],
				],
			];
		}

		return $list;
	}

	/**
	 * @return array
	 */
	public static function getCategoryColorPalette(): array
	{
		return array_filter( static::colorPalette(), function ( $color )
		{
			return $color['slug'] !== ColorHelper::COLOR_YELLOW;
		} );
	}

	/**
	 * @param string $slug
	 * @return string|null
	 */
	public static function getColorHexBySlug( string $slug ): ?string
	{
		if ( empty( $slug ) ) {
			return null;
		}

		foreach ( static::colorPalette() as $group ) {
			foreach ( $group['colors'] as $color ) {
				if ( $color['slug'] === $slug ) {
					return strtoupper( $color['color'] );
				}
			}
		}

		return null;
	}

	/**
	 * Converts the grouped color palette to a flat array (HEX code => color name)
	 * @param array|null $palette
	 * @param bool $asc
	 * @return array
	 */
	public static function colorPaletteFlat( ?array $palette = null, bool $asc = false ): array
	{
		$palette = $palette ?? static::colorPalette();
		$list    = [];

		foreach ( $palette as $group ) {
			foreach ( $group['colors'] as $color ) {
				$list[ $color['color'] ] = $color['name'];
			}
		}

		if ( $asc ) {
			// Sort array by values (color names), but keep keys (HEX values)
			asort( $list );
		}

		return $list;
	}

	/**
	 * Returns a flat array of colors for the category color palette
	 * @param bool $asc
	 * @return array
	 */
	public static function categoryColorPaletteFlat( bool $asc = false ): array
	{
		return static::colorPaletteFlat( static::getCategoryColorPalette(), $asc );
	}

	/**
	 * Returns a compact array of colors for WordPress editor-color-palette
	 * WordPress doesn't support grouped colors in the standard color palette
	 * @see https://github.com/WordPress/gutenberg/issues/47837
	 * @param array|null $palette
	 * @return array
	 */
	public static function colorPaletteCompact( ?array $palette = null ): array
	{
		$list = [];
		if ( empty( $palette ) ) {
			$palette = static::colorPalette();
		}

		foreach ( $palette as $group ) {
			foreach ( $group['colors'] as $color ) {
				$list[] = [
					'slug'  => $color['slug'],
					'color' => $color['color'],
					'name'  => $color['name'],
				];
			}
		}

		return $list;
	}

	// Custom images and buttons

	/**
	 * @return string
	 */
	public static function getImageType(): string
	{
		return (string) static::getGlobalOption( 'image_type' );
	}

	/**
	 * @return string
	 */
	public static function getButtonType(): string
	{
		return (string) static::getGlobalOption( 'button_type' );
	}

	/**
	 * @return string
	 */
	public static function getImageRadius(): string
	{
		return (string) static::getGlobalOption( 'image_rounded' );
	}

	/**
	 * @return string
	 */
	public static function getButtonRadius(): string
	{
		return (string) static::getGlobalOption( 'button_rounded' );
	}

	/**
	 * @return object
	 */
	public static function getCutoutImage(): object
	{
		return (object) [
			'topLeftX'     => (int) static::getGlobalOption( 'image_cutout_top_left_x' ),
			'topLeftY'     => (int) static::getGlobalOption( 'image_cutout_top_left_y' ),
			'topRightX'    => (int) static::getGlobalOption( 'image_cutout_top_right_x' ),
			'topRightY'    => (int) static::getGlobalOption( 'image_cutout_top_right_y' ),
			'bottomRightX' => (int) static::getGlobalOption( 'image_cutout_bottom_right_x' ),
			'bottomRightY' => (int) static::getGlobalOption( 'image_cutout_bottom_right_y' ),
			'bottomLeftX'  => (int) static::getGlobalOption( 'image_cutout_bottom_left_x' ),
			'bottomLeftY'  => (int) static::getGlobalOption( 'image_cutout_bottom_left_y' ),
		];
	}

	/**
	 * @return object
	 */
	public static function getCutoutButton(): object
	{
		return (object) [
			'topLeftX'     => (int) static::getGlobalOption( 'button_cutout_top_left_x' ),
			'topLeftY'     => (int) static::getGlobalOption( 'button_cutout_top_left_y' ),
			'topRightX'    => (int) static::getGlobalOption( 'button_cutout_top_right_x' ),
			'topRightY'    => (int) static::getGlobalOption( 'button_cutout_top_right_y' ),
			'bottomRightX' => (int) static::getGlobalOption( 'button_cutout_bottom_right_x' ),
			'bottomRightY' => (int) static::getGlobalOption( 'button_cutout_bottom_right_y' ),
			'bottomLeftX'  => (int) static::getGlobalOption( 'button_cutout_bottom_left_x' ),
			'bottomLeftY'  => (int) static::getGlobalOption( 'button_cutout_bottom_left_y' ),
		];
	}

	/**
	 * Set all the color variables,
	 * this is only used within the Powered by SURF theme
	 */
	public static function colorVariables(): string
	{
		$primaryVariations = [
			'primary',
			'link',

			// Header
			'top-bar-link-hover',
			'header-menu-link-hover',
			'header-menu-link-active-indicator',
			'header-search-button',
			'header-hamburger-button',

			// Footer
			'footer-link-hover',

			// Button group
			'button-group-link',

			// Search form
			'search-button',

			// Blockquote
			'blockquote',

			// Buttons
			'button',
		];

		$secondaryVariations = [
			'secondary',
		];

		$tertiaryVariations = [
			'tertiary',
		];

		$quaternaryVariations = [
			'quaternary',
		];

		$altVariations = [
			// Header
			'link-hover',
			'button-hover',

			// Button
			'header-search-button-hover',

			// Button group
			'button-group-link-hover',

			// Search button
			'search-button-hover',
		];

		$variables = '';

		foreach ( $primaryVariations as $var ) {
			$variables .= '--surf-color-' . $var . ': ' . static::primaryColor() . "; \n";
		}

		foreach ( $secondaryVariations as $var ) {
			$variables .= '--surf-color-' . $var . ': ' . static::secondaryColor() . "; \n";
		}

		foreach ( $tertiaryVariations as $var ) {
			$variables .= '--surf-color-' . $var . ': ' . static::tertiaryColor() . "; \n";
		}

		foreach ( $quaternaryVariations as $var ) {
			$variables .= '--surf-color-' . $var . ': ' . static::quaternaryColor() . "; \n";
		}

		foreach ( $altVariations as $var ) {
			$variables .= '--surf-color-' . $var . ': ' . static::altColor() . "; \n";
		}

		$variables .= '--surf-color-header-background:' . static::headerColor() . "; \n";
		$variables .= '--surf-color-footer-background:' . static::footerColor() . "; \n";
		$variables .= '--surf-color-comment-form-background:' . static::formColor() . "; \n";
		$variables .= '--surf-color-background:' . static::backgroundColor() . "; \n";
		$variables .= '--surf-color-articles-background:' . static::backgroundColor() . "; \n";

		if ( in_array( static::getImageType(), [ 'square', 'cutout' ] ) ) {
			$variables .= "--surf-border-radius: 0px; \n";
			$variables .= "--surf-image-border-radius: 0px; \n";
			$variables .= "--surf-post-item-figure-border-radius: 0px; \n";
			$variables .= "--surf-assets-header-border-radius: 0px; \n";
		}

		if ( in_array( static::getButtonType(), [ 'square', 'cutout' ] ) ) {
			$variables .= "--surf-button-border-radius: 0px; \n";
			$variables .= "--surf-category-tag-border-radius: 0px; \n";
			$variables .= "--surf-header-button-radius: 0px; \n";
			$variables .= "--surf-search-field-border-radius: 0px; \n";
			$variables .= "--surf-search-field-hero-border-radius: 0px; \n";
			$variables .= "--surf-assets-button-border-radius: 0px; \n";
		}

		if ( static::getImageType() === 'rounded' ) {
			$variables .= '--surf-border-radius:' . static::getImageRadius() . "px; \n";
			$variables .= '--surf-image-border-radius:' . static::getImageRadius() . "px; \n";
			$variables .= "--surf-assets-header-border-radius: 5px; \n";
		}

		if ( static::getButtonType() === 'rounded' ) {
			$variables .= '--surf-button-border-radius:' . static::getButtonRadius() . "px; \n";
			$variables .= "--surf-assets-button-border-radius: 3px; \n";
		}

		$variables .= '--surf-button-cutout: polygon(
                0% ' . static::getCutoutButton()->topLeftY . '%,
                ' . static::getCutoutButton()->topLeftX . '% ' . static::getCutoutButton()->topLeftY . '%,
                ' . static::getCutoutButton()->topLeftX . '% 0%,
                ' . ( 100 - static::getCutoutButton()->topRightX ) . '% 0%,
                ' . ( 100 - static::getCutoutButton()->topRightX ) . '% ' . static::getCutoutButton()->topRightY . '%,
                100% ' . static::getCutoutButton()->topRightY . '%,
                100% ' . ( 100 - static::getCutoutButton()->bottomRightY ) . '%,
                ' . ( 100 - static::getCutoutButton()->bottomRightX ) . '% ' . ( 100 - static::getCutoutButton()->bottomRightY ) . '%,
                ' . ( 100 - static::getCutoutButton()->bottomRightX ) . '% 100%,
                ' . static::getCutoutButton()->bottomLeftX . '% 100%,
                ' . static::getCutoutButton()->bottomLeftX . '% ' . ( 100 - static::getCutoutButton()->bottomLeftY ) . '%,
                0% ' . ( 100 - static::getCutoutButton()->bottomLeftY ) . "%
                ); \n";

		$variables .= '--surf-image-cutout: polygon(
            0% ' . static::getCutoutImage()->topLeftY . '%,
            ' . static::getCutoutImage()->topLeftX . '% ' . static::getCutoutImage()->topLeftY . '%,
            ' . static::getCutoutImage()->topLeftX . '% 0%,
            ' . ( 100 - static::getCutoutImage()->topRightX ) . '% 0%,
            ' . ( 100 - static::getCutoutImage()->topRightX ) . '% ' . static::getCutoutImage()->topRightY . '%,
            100% ' . static::getCutoutImage()->topRightY . '%,
            100% ' . ( 100 - static::getCutoutImage()->bottomRightY ) . '%,
            ' . ( 100 - static::getCutoutImage()->bottomRightX ) . '% ' . ( 100 - static::getCutoutImage()->bottomRightY ) . '%,
            ' . ( 100 - static::getCutoutImage()->bottomRightX ) . '% 100%,
            ' . static::getCutoutImage()->bottomLeftX . '% 100%,
            ' . static::getCutoutImage()->bottomLeftX . '% ' . ( 100 - static::getCutoutImage()->bottomLeftY ) . '%,
            0% ' . ( 100 - static::getCutoutImage()->bottomLeftY ) . "%
            ); \n";

		return $variables;
	}

	/**
	 * @return array
	 */
	public static function fonts(): array
	{
		$fonts = static::getGlobalRepeaterOption( 'surf_fonts', [ 'name', 'file' ] );
		if ( empty( $fonts ) ) {
			return [];
		}

		return array_map( function ( $font )
		{
			$id = $font['file'];

			return [
				'name' => surfSlugify( $font['name'] ),
				'url'  => $id ? wp_get_attachment_url( $id ) : null,
			];
		}, $fonts );
	}

	/**
	 * @return string|null
	 */
	public static function font(): ?string
	{
		$font = static::getGlobalOption( 'surf_theme_font' );

		return $font ?: null;
	}

	/**
	 * @param $heading
	 * @return string|null
	 */
	public static function headingFont( $heading = '' ): ?string
	{
		if ( in_array( $heading, HeadingHooks::$headings ) ) {
			$value = static::getGlobalOption( 'surf_theme_heading_font_' . $heading );

			return $value ?: null;
		}

		$value = static::getGlobalOption( 'surf_theme_heading_font' );

		return $value ?: null;
	}

	/**
	 * @return string
	 */
	public static function tagsLocation(): string
	{
		$value = static::getGlobalOption( 'surf_theme_position_tags' );

		return $value ?: 'bottom';
	}

	/**
	 * @return bool|string
	 */
	public static function showEventYear(): bool|string
	{
		$value = static::getGlobalOption( 'surf_theme_date_year' ) ?: false;

		return $value === '1' ? true : ( $value === '0' ? false : ( $value === 'current' ? $value : false ) );
	}

	/**
	 * @param int $postId
	 * @return string
	 */
	public static function postDateDisplay( int $postId = -1 ): string
	{
		if ( is_singular( [ 'post' ] ) || $postId !== -1 ) {
			$postId   = (int) ( $postId ?: get_queried_object_id() );
			$override = PostHelper::getMetaValue( 'publication_date_settings_override', $postId ) ?: 'default';
			if ( $override !== 'default' ) {
				return $override;
			}
		}

		return match ( static::getGlobalOption( 'surf_theme_post_date_display' ) ) {
			'hidden'   => 'hidden',
			'modified' => 'modified',
			'both'     => 'both',
			default    => 'published'
		};
	}

	/**
	 * @return string
	 */
	public static function postDateDisplayPublishedAtText(): string
	{
		return (string) PolylangHelper::getThemeOptionWithGlobalFallback( 'surf_theme_post_date_display_published_at_text' );
	}

	/**
	 * @return string
	 */
	public static function postDateDisplayModifiedAtText(): string
	{
		return (string) PolylangHelper::getThemeOptionWithGlobalFallback( 'surf_theme_post_date_display_modified_at_text' );
	}

}
