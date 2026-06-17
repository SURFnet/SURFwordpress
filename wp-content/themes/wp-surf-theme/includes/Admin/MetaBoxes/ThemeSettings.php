<?php

namespace SURF\Admin\MetaBoxes;

use SURF\Admin\MetaBoxes;
use SURF\Admin\Pages;
use SURF\Enums\Theme;
use SURF\Helpers\ACFHelper;
use SURF\Helpers\CutoutHelper;
use SURF\Helpers\PolylangHelper;
use SURF\Helpers\SocialHelper;
use SURF\Hooks\HeadingHooks;
use SURF\PostTypes\Agenda;
use SURF\PostTypes\Asset;
use SURF\PostTypes\Download;
use SURF\PostTypes\Faq;
use SURF\PostTypes\Page;
use SURF\PostTypes\Post;
use SURF\PostTypes\Vacancy;

/**
 * Class ThemeSettings
 * @package SURF\Admin\MetaBoxes
 */
class ThemeSettings
{

	/**
	 * @return void
	 */
	public static function init(): void
	{
		// Register Polylang ThemeSettings Fields
		$languages = PolylangHelper::getLanguages();
		if ( $languages ) {
			// Theme MetaBoxes (Primary)
			MetaBoxes::register( [
				'title'    => _x( 'Settings', 'admin', 'wp-surf-theme' ),
				'key'      => 'group_surf_theme_settings_page',
				'location' => static::getDefaultLocation(),
				'fields'   => static::getPolylangThemeFields(),
			] );

			// Language MetaBoxes (Secondary)
			MetaBoxes::register( [
				'title'    => _x( 'Settings', 'admin', 'wp-surf-theme' ),
				'key'      => 'group_surf_theme_settings_pll_page',
				'location' => static::getPolylangLocation(),
				'fields'   => static::getPolylangLanguageFields(),
			] );
		}

		// Register Default ThemeSettings Fields
		MetaBoxes::register( [
			'title'    => _x( 'Settings', 'admin', 'wp-surf-theme' ),
			'key'      => 'group_surf_theme_settings_page',
			'location' => static::getDefaultLocation(),
			'fields'   => static::getDefaultFields(),
		] );
	}

	/**
	 * Get Locations to register Language Options
	 * @return array
	 */
	public static function getPolylangLocation(): array
	{
		$list      = [];
		$languages = PolylangHelper::getLanguages();
		if ( empty( $languages ) ) {
			return $list;
		}

		foreach ( $languages as $key => $language ) {
			$list[] = [
				[
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => Pages::SLUG_THEME_SETTINGS . '-' . $language,
				],
			];
		}

		return $list;
	}

	/**
	 * @return array[]
	 */
	public static function getDefaultLocation(): array
	{
		return [
			[
				[
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => Pages::SLUG_THEME_SETTINGS,
				],
			],
		];
	}

	/**
	 * Theme Settings fields used when polylang isn't installed
	 * @return array[]
	 */
	public static function getDefaultFields(): array
	{
		return static::setupMetaBoxes( static::getDefaultMetaBoxes() );
	}

	/**
	 * Polylang (Primary) fields used when polylang is installed
	 * @return array[]
	 */
	public static function getPolylangThemeFields(): array
	{
		return static::setupMetaBoxes( static::getPolylangThemeMetaBoxes() );
	}

	/**
	 * Polylang (Secondary) fields used when polylang is installed
	 * @return array[]
	 */
	public static function getPolylangLanguageFields(): array
	{
		return static::setupMetaBoxes( static::getPolylangLanguageMetaBoxes() );
	}

	/**
	 * @param array $metaBoxes
	 * @return array
	 */
	public static function setupMetaBoxes( $metaBoxes = [] ): array
	{
		$fields = [];
		if ( !$metaBoxes && !is_array( $metaBoxes ) ) {
			return $fields;
		}

		foreach ( $metaBoxes as $metaBox ) {
			foreach ( $metaBox as $field ) {
				$fields[] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Theme Settings MetaBoxes - Used when Polylang isn't installed.
	 * All meta boxes should be added here.
	 * @return \array[][]
	 */
	public static function getDefaultMetaBoxes(): array
	{
		return [
			static::getSite(),
			static::getSiteHeadings(),
			static::getSiteFonts(),
			static::getSiteColors(),
			static::getImagesButtons(),
			static::getSocialMedia(),
			static::getSocialSharing(),
			static::getErrorPage(),
			static::getFooter(),
			static::getBreadcrumbs(),
			static::getSearch(),
		];
	}

	/**
	 * Polylang Theme MetaBoxes - "Global" used meta boxes.
	 * @return \array[][]
	 */
	public static function getPolylangThemeMetaBoxes(): array
	{
		return [
			static::getSite(),
			static::getSiteHeadings(),
			static::getSiteFonts(),
			static::getSiteColors(),
			static::getImagesButtons(),
			static::getSocialMedia(),
			static::getSocialSharing(),
			static::getErrorPage(),
			static::getFooter(),
			static::getBreadcrumbs(),
		];
	}

	/**
	 * Polylang Language MetaBoxes - Secondary or lower meta boxes on language level.
	 * @return \array[][]
	 */
	public static function getPolylangLanguageMetaBoxes(): array
	{
		return [
			static::getSitePolyPerLanguage(),
			static::getSearch(),
		];
	}

	/**
	 * Site meta
	 * @return array[]
	 */
	public static function getSite(): array
	{
		return [
			[
				'key'       => 'field_theme_settings_site_tab',
				'label'     => _x( 'Site', 'admin', 'wp-surf-theme' ),
				'name'      => '',
				'placement' => 'left',
				'type'      => 'tab',
			],
			[
				'key'     => 'field_theme_settings_site_theme',
				'label'   => _x( 'Theme', 'admin', 'wp-surf-theme' ),
				'name'    => 'surf_theme',
				'type'    => 'select',
				'choices' => Theme::options(),
			],
			[
				'key'          => 'field_theme_settings_site_logos',
				'label'        => _x( "Logo's", 'admin', 'wp-surf-theme' ),
				'name'         => 'surf_logos',
				'type'         => 'repeater',
				'max'          => 3,
				'button_label' => _x( 'Add logo', 'admin', 'wp-surf-theme' ),
				'sub_fields'   => [
					[
						'key'           => 'field_theme_settings_site_logos_logo',
						'label'         => _x( 'Logo', 'admin', 'wp-surf-theme' ),
						'name'          => 'logo',
						'type'          => 'image',
						'return_format' => 'id',
						'wrapper'       => [ 'width' => 50 ],
					],
					[
						'key'     => 'field_theme_settings_site_logos_logo_size',
						'label'   => _x( 'Logo size', 'admin', 'wp-surf-theme' ),
						'name'    => 'logo_size',
						'type'    => 'select',
						'choices' => [
							'100' => '100%',
							'150' => '150%',
							'200' => '200%',
						],
						'wrapper' => [ 'width' => 50 ],
					],
				],
			],
			[
				'key'               => 'field_theme_settings_site_theme_font',
				'label'             => _x( 'Font', 'admin', 'wp-surf-theme' ),
				'name'              => 'surf_theme_font',
				'type'              => 'select',
				'choices'           => [],
				'conditional_logic' => static::getPoweredByCondition(),
			],
			[
				'key'               => 'field_theme_settings_site_theme_heading_font',
				'label'             => _x( 'Heading font', 'admin', 'wp-surf-theme' ),
				'name'              => 'surf_theme_heading_font',
				'type'              => 'select',
				'choices'           => [],
				'conditional_logic' => static::getPoweredByCondition(),
			],
			[
				'key'               => 'field_theme_settings_site_theme_menu_separator',
				'label'             => _x( 'Menu separator', 'admin', 'wp-surf-theme' ),
				'name'              => 'surf_theme_menu_separator',
				'type'              => 'group',
				'conditional_logic' => static::getPoweredByCondition(),
				'wrapper'           => [ 'width' => 50 ],
				'sub_fields'        => [
					[
						'key'           => 'field_theme_settings_site_theme_menu_separator_image',
						'label'         => _x( 'Separator image', 'admin', 'wp-surf-theme' ),
						'name'          => 'image',
						'type'          => 'image',
						'return_format' => 'id',
					],
					[
						'key'               => 'field_theme_settings_site_theme_menu_separator_margins',
						'label'             => _x( 'Negative margins', 'admin', 'wp-surf-theme' ),
						'instructions'      => _x( 'Sometimes an image has some whitespace, to control this you can add negative margins to the top and bottom, to align the image better with the content.', 'admin', 'wp-surf-theme' ),
						'name'              => 'margins',
						'type'              => 'group',
						'conditional_logic' => [
							'field'    => 'field_theme_settings_site_theme_menu_separator_image',
							'operator' => '!=empty',
						],
						'sub_fields'        => [
							[
								'key'           => 'field_theme_settings_site_theme_menu_separator_margin_mobile_top',
								'label'         => _x( 'Mobile - Top', 'admin', 'wp-surf-theme' ),
								'name'          => 'mobile_top',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
							[
								'key'           => 'field_theme_settings_site_theme_menu_separator_margin_mobile_bottom',
								'label'         => _x( 'Mobile - Bottom', 'admin', 'wp-surf-theme' ),
								'name'          => 'mobile_bottom',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
							[
								'key'           => 'field_theme_settings_site_theme_menu_separator_margin_tablet_top',
								'label'         => _x( 'Tablet - Top', 'admin', 'wp-surf-theme' ),
								'name'          => 'tablet_top',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
							[
								'key'           => 'field_theme_settings_site_theme_menu_separator_margin_tablet_bottom',
								'label'         => _x( 'Tablet - Bottom', 'admin', 'wp-surf-theme' ),
								'name'          => 'tablet_bottom',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
							[
								'key'           => 'field_theme_settings_site_theme_menu_separator_margin_desktop_top',
								'label'         => _x( 'Desktop - Top', 'admin', 'wp-surf-theme' ),
								'name'          => 'desktop_top',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
							[
								'key'           => 'field_theme_settings_site_theme_menu_separator_margin_desktop_bottom',
								'label'         => _x( 'Desktop - Bottom', 'admin', 'wp-surf-theme' ),
								'name'          => 'desktop_bottom',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
						],
					],
				],
			],
			[
				'key'               => 'field_theme_settings_site_theme_global_separator',
				'label'             => _x( 'Global separator', 'admin', 'wp-surf-theme' ),
				'name'              => 'surf_theme_global_separator',
				'type'              => 'group',
				'conditional_logic' => static::getPoweredByCondition(),
				'wrapper'           => [ 'width' => 50 ],
				'sub_fields'        => [
					[
						'key'           => 'field_theme_settings_site_theme_global_separator_image',
						'label'         => _x( 'Separator image', 'admin', 'wp-surf-theme' ),
						'name'          => 'image',
						'type'          => 'image',
						'return_format' => 'id',
					],
					[
						'key'               => 'field_theme_settings_site_theme_global_separator_margins',
						'label'             => _x( 'Negative margins', 'admin', 'wp-surf-theme' ),
						'instructions'      => _x( 'Sometimes an image has some whitespace, to control this you can add negative margins to the top and bottom, to align the image better with the content.', 'admin', 'wp-surf-theme' ),
						'name'              => 'margins',
						'type'              => 'group',
						'conditional_logic' => [
							'field'    => 'field_theme_settings_site_theme_global_separator_image',
							'operator' => '!=empty',
						],
						'sub_fields'        => [
							[
								'key'           => 'field_theme_settings_site_theme_global_separator_margin_mobile_top',
								'label'         => _x( 'Mobile - Top', 'admin', 'wp-surf-theme' ),
								'name'          => 'mobile_top',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
							[
								'key'           => 'field_theme_settings_site_theme_global_separator_margin_mobile_bottom',
								'label'         => _x( 'Mobile - Bottom', 'admin', 'wp-surf-theme' ),
								'name'          => 'mobile_bottom',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
							[
								'key'           => 'field_theme_settings_site_theme_global_separator_margin_tablet_top',
								'label'         => _x( 'Tablet - Top', 'admin', 'wp-surf-theme' ),
								'name'          => 'tablet_top',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
							[
								'key'           => 'field_theme_settings_site_theme_global_separator_margin_tablet_bottom',
								'label'         => _x( 'Tablet - Bottom', 'admin', 'wp-surf-theme' ),
								'name'          => 'tablet_bottom',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
							[
								'key'           => 'field_theme_settings_site_theme_global_separator_margin_desktop_top',
								'label'         => _x( 'Desktop - Top', 'admin', 'wp-surf-theme' ),
								'name'          => 'desktop_top',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
							[
								'key'           => 'field_theme_settings_site_theme_global_separator_margin_desktop_bottom',
								'label'         => _x( 'Desktop - Bottom', 'admin', 'wp-surf-theme' ),
								'name'          => 'desktop_bottom',
								'type'          => 'number',
								'append'        => 'px',
								'prepend'       => '-',
								'default_value' => 0,
								'wrapper'       => [ 'width' => 50 ],
							],
						],
					],
					[
						'key'   => 'field_theme_settings_site_theme_global_separator_no_margin',
						'label' => _x( 'No margin', 'admin', 'wp-surf-theme' ),
						'name'  => 'no_margin',
						'type'  => 'true_false',
						'ui'    => 1,
					],
				],
			],
			[
				'key'          => 'field_theme_settings_site_theme_tags_position',
				'label'        => _x( 'Tags position', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'Where do you want to show the tags, at the top of bottom of the post. Bottom is standard.', 'admin', 'wp-surf-theme' ),
				'name'         => 'surf_theme_position_tags',
				'type'         => 'button_group',
				'choices'      => [
					'bottom' => _x( 'Bottom', 'admin', 'wp-surf-theme' ),
					'top'    => _x( 'Top', 'admin', 'wp-surf-theme' ),
				],
			],
			[
				'key'          => 'field_theme_settings_site_theme_date_year',
				'label'        => _x( 'Show year in Event dates', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'Select when to display the year in the Event dates of this site.', 'admin', 'wp-surf-theme' ),
				'name'         => 'surf_theme_date_year',
				'type'         => 'button_group',
				'choices'      => [
					'0'       => _x( 'Never', 'admin', 'wp-surf-theme' ),
					'1'       => _x( 'Always', 'admin', 'wp-surf-theme' ),
					'current' => sprintf( _x( 'Only if not current (%s)', 'admin', 'wp-surf-theme' ), date( 'Y' ) ),
				],
				'wrapper'      => [ 'width' => 50 ],
			],
			[
				'key'          => 'field_theme_settings_site_theme_post_date_display',
				'label'        => _x( 'Post date display', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'Select which date(s) you want to display. This only applies to posts and pages.', 'admin', 'wp-surf-theme' ),
				'name'         => 'surf_theme_post_date_display',
				'type'         => 'select',
				'choices'      => [
					'hidden'    => _x( 'Hidden', 'admin', 'wp-surf-theme' ),
					'published' => _x( 'Published', 'admin', 'wp-surf-theme' ),
					'modified'  => _x( 'Modified', 'admin', 'wp-surf-theme' ),
					'both'      => _x( 'Published and modified', 'admin', 'wp-surf-theme' ),
				],
				'wrapper'      => [ 'width' => 50 ],
			],
			[
				'key'          => 'field_theme_settings_site_theme_post_date_display_published_at_text',
				'label'        => _x( 'Post date display - Published at text', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'This will be displayed before the post\'s published date.', 'admin', 'wp-surf-theme' ),
				'name'         => 'surf_theme_post_date_display_published_at_text',
				'type'         => 'text',
				'wrapper'      => [ 'width' => 50 ],
			],
			[
				'key'          => 'field_theme_settings_site_theme_post_date_display_modified_at_text',
				'label'        => _x( 'Post date display - Modified at text', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'This will be displayed before the post\'s modified date.', 'admin', 'wp-surf-theme' ),
				'name'         => 'surf_theme_post_date_display_modified_at_text',
				'type'         => 'text',
				'wrapper'      => [ 'width' => 50 ],
			],
		];
	}

	/**
	 * @return array
	 */
	public static function getSiteHeadings(): array
	{
		$headings = HeadingHooks::$headings;

		return [
			[
				'key'       => 'field_theme_settings_site_headings_tab',
				'label'     => _x( 'Headings', 'admin', 'wp-surf-theme' ),
				'type'      => 'tab',
				'name'      => '',
				'placement' => 'left',
			],
			...array_merge(
				...array_map(
					function ( $heading )
					{
						$whenPoweredBy = [
							[
								'field'    => 'field_theme_settings_site_theme',
								'operator' => '==',
								'value'    => Theme::POWERED_BY_SURF,
							],
						];

						return [
							[
								'key'        => 'field_theme_settings_site_headings_' . $heading,
								'label'      => strtoupper( $heading ),
								'name'       => 'surf_theme_' . $heading,
								'type'       => 'image',
								'mime_types' => 'png',
								'wrapper'    => [ 'width' => 50 ],
							],
							[
								'key'               => 'field_theme_settings_site_heading_font_' . $heading,
								'label'             => sprintf( _x( 'Font %s', 'admin', 'wp-surf-theme' ), strtoupper( $heading ) ),
								'name'              => 'surf_theme_heading_font_' . $heading,
								'type'              => 'select',
								'choices'           => [],
								'conditional_logic' => $whenPoweredBy,
								'wrapper'           => [ 'width' => 50 ],
							],
						];
					},
					$headings
				)
			),
		];
	}

	/**
	 * @return array[]
	 */
	public static function getSiteFonts(): array
	{
		return [
			[
				'key'               => 'field_theme_settings_fonts_tab',
				'label'             => _x( 'Fonts', 'admin', 'wp-surf-theme' ),
				'name'              => '',
				'placement'         => 'left',
				'type'              => 'tab',
				'conditional_logic' => static::getPoweredByCondition(),
			],
			[
				'key'          => 'field_theme_settings_fonts',
				'label'        => _x( 'Fonts', 'admin', 'wp-surf-theme' ),
				'name'         => 'surf_fonts',
				'type'         => 'repeater',
				'max'          => 8,
				'button_label' => _x( 'Add font', 'admin', 'wp-surf-theme' ),
				'sub_fields'   => [
					[
						'key'     => 'field_theme_settings_fonts_name',
						'label'   => _x( 'Name', 'admin', 'wp-surf-theme' ),
						'name'    => 'name',
						'type'    => 'text',
						'wrapper' => [ 'width' => 50 ],
					],
					[
						'key'               => 'field_theme_settings_fonts_file',
						'label'             => _x( 'File', 'admin', 'wp-surf-theme' ),
						'name'              => 'file',
						'type'              => 'file',
						'return_format'     => 'id',
						'mime_types'        => implode( ',', ACFHelper::listAllowedFontTypes() ),
						'conditional_logic' => static::getPoweredByCondition(),
						'wrapper'           => [ 'width' => 50 ],
					],
				],
			],
		];
	}

	/**
	 * Site Colors
	 * @return array[]
	 */
	public static function getSiteColors(): array
	{
		return [
			[
				'key'               => 'field_theme_settings_colors_tab',
				'label'             => _x( 'Colors', 'admin', 'wp-surf-theme' ),
				'name'              => '',
				'placement'         => 'left',
				'type'              => 'tab',
				'conditional_logic' => static::getPoweredByCondition(),
			],
			[
				'key'           => 'field_theme_settings_site_theme_primary_color',
				'label'         => _x( 'Primary color', 'admin', 'wp-surf-theme' ),
				'name'          => 'surf_theme_primary_color',
				'type'          => 'color_picker',
				'default_value' => '#0037dd',
				'wrapper'       => [ 'width' => 25 ],
			],
			[
				'key'           => 'field_theme_settings_site_theme_secondary_color',
				'label'         => _x( 'Secondary color', 'admin', 'wp-surf-theme' ),
				'name'          => 'surf_theme_secondary_color',
				'type'          => 'color_picker',
				'default_value' => '#fedb00',
				'wrapper'       => [ 'width' => 25 ],
			],
			[
				'key'           => 'field_theme_settings_site_theme_tertiary_color',
				'label'         => _x( 'Tertiary color', 'admin', 'wp-surf-theme' ),
				'name'          => 'surf_theme_tertiary_color',
				'type'          => 'color_picker',
				'default_value' => '#f9f5f2',
				'wrapper'       => [ 'width' => 25 ],
			],
			[
				'key'           => 'field_theme_settings_site_theme_quaternary_color',
				'label'         => _x( 'Quaternary color', 'admin', 'wp-surf-theme' ),
				'name'          => 'surf_theme_quaternary_color',
				'type'          => 'color_picker',
				'default_value' => '#D3D3D3',
				'wrapper'       => [ 'width' => 25 ],
			],
			[
				'key'          => 'field_theme_settings_site_theme_header_background_color',
				'label'        => _x( 'Header background color', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'This color will be used on items like: header', 'admin', 'wp-surf-theme' ),
				'name'         => 'surf_theme_header_background_color',
				'type'         => 'color_picker',
				'wrapper'      => [ 'width' => 50 ],
			],
			[
				'key'           => 'field_theme_settings_site_theme_footer_background_color',
				'label'         => _x( 'Footer background color', 'admin', 'wp-surf-theme' ),
				'instructions'  => _x( 'This color will be used on items like: Footer', 'admin', 'wp-surf-theme' ),
				'name'          => 'surf_theme_footer_background_color',
				'type'          => 'color_picker',
				'default_value' => '#f9f5f2',
				'wrapper'       => [ 'width' => 50 ],
			],
			[
				'key'           => 'field_theme_settings_site_theme_form_background_color',
				'instructions'  => _x( 'This color will be used on items like: Gravity Forms, Comment forms', 'admin', 'wp-surf-theme' ),
				'label'         => _x( 'Form background color', 'admin', 'wp-surf-theme' ),
				'name'          => 'surf_theme_form_background_color',
				'type'          => 'color_picker',
				'default_value' => '#f9f5f2',
				'wrapper'       => [ 'width' => 50 ],
			],
			[
				'key'           => 'field_theme_settings_site_theme_background_color',
				'label'         => _x( 'Global background color', 'admin', 'wp-surf-theme' ),
				'instructions'  => _x( 'This color will be used on items like: Comments, Archive pages, FAQ items, Articles block, Downloads block, Featured Items block', 'admin', 'wp-surf-theme' ),
				'name'          => 'surf_theme_background_color',
				'type'          => 'color_picker',
				'default_value' => '#f9f5f2',
				'wrapper'       => [ 'width' => 50 ],
			],
		];
	}

	/**
	 * Images and Buttons
	 * @return array[]
	 */
	public static function getImagesButtons(): array
	{
		return [
			[
				'key'               => 'field_theme_settings_ib_tab',
				'label'             => _x( 'Images and buttons', 'admin', 'wp-surf-theme' ),
				'name'              => '',
				'placement'         => 'left',
				'type'              => 'tab',
				'conditional_logic' => static::getPoweredByCondition(),
			],
			[
				'key'        => 'field_theme_settings_ib_image',
				'label'      => _x( 'Image', 'admin', 'wp-surf-theme' ),
				'name'       => 'image',
				'type'       => 'group',
				'wrapper'    => [ 'width' => 50 ],
				'sub_fields' => [
					[
						'key'          => 'field_theme_settings_ib_type_image',
						'label'        => _x( 'Type', 'admin', 'wp-surf-theme' ),
						'name'         => 'type',
						'instructions' => _x( 'Select how you would like to show the images', 'admin', 'wp-surf-theme' ),
						'type'         => 'radio',
						'choices'      => [
							''        => _x( 'Default', 'admin', 'wp-surf-theme' ),
							'square'  => _x( 'Square', 'admin', 'wp-surf-theme' ),
							'rounded' => _x( 'Rounded', 'admin', 'wp-surf-theme' ),
							'cutout'  => _x( 'Cutout', 'admin', 'wp-surf-theme' ),
						],
					],
					[
						'key'               => 'field_theme_settings_ib_image_rounded',
						'label'             => _x( 'Rounded', 'admin', 'wp-surf-theme' ),
						'name'              => 'rounded',
						'instructions'      => _x( 'Select how round you want the image to be', 'admin', 'wp-surf-theme' ),
						'type'              => 'range',
						'min'               => 0,
						'max'               => 200,
						'step'              => 1,
						'conditional_logic' => [
							'field'    => 'field_theme_settings_ib_type_image',
							'operator' => '==',
							'value'    => 'rounded',
						],
					],
					[
						'key'               => 'field_theme_settings_ib_image_cutout',
						'label'             => _x( 'Cutout', 'admin', 'wp-surf-theme' ),
						'name'              => 'cutout',
						'instructions'      => _x( 'This will be used to decide the cutout for each corner of an image.', 'admin', 'wp-surf-theme' ),
						'type'              => 'group',
						'sub_fields'        => [
							CutoutHelper::getCornerSettings(
								'image',
								'top_left',
								_x( 'Top left corner', 'admin', 'wp-surf-theme' )
							),
							CutoutHelper::getCornerSettings(
								'image',
								'top_right',
								_x( 'Top right corner', 'admin', 'wp-surf-theme' )
							),
							CutoutHelper::getCornerSettings(
								'image',
								'bottom_left',
								_x( 'Bottom left corner', 'admin', 'wp-surf-theme' )
							),
							CutoutHelper::getCornerSettings(
								'image',
								'bottom_right',
								_x( 'Bottom right corner', 'admin', 'wp-surf-theme' )
							),
						],
						'conditional_logic' => [
							'field'    => 'field_theme_settings_ib_type_image',
							'operator' => '==',
							'value'    => 'cutout',
						],
					],
				],
			],
			[
				'key'        => 'field_theme_settings_ib_button',
				'label'      => _x( 'Button', 'admin', 'wp-surf-theme' ),
				'name'       => 'button',
				'type'       => 'group',
				'wrapper'    => [ 'width' => 50 ],
				'sub_fields' => [
					[
						'key'          => 'field_theme_settings_ib_type_button',
						'label'        => _x( 'Type', 'admin', 'wp-surf-theme' ),
						'name'         => 'type',
						'instructions' => _x( 'Select how you would like to show the buttons', 'admin', 'wp-surf-theme' ),
						'type'         => 'radio',
						'choices'      => [
							''        => _x( 'Default', 'admin', 'wp-surf-theme' ),
							'square'  => _x( 'Square', 'admin', 'wp-surf-theme' ),
							'rounded' => _x( 'Rounded', 'admin', 'wp-surf-theme' ),
							'cutout'  => _x( 'Cutout', 'admin', 'wp-surf-theme' ),
						],
					],
					[
						'key'               => 'field_theme_settings_ib_button_rounded',
						'label'             => _x( 'Rounded', 'admin', 'wp-surf-theme' ),
						'name'              => 'rounded',
						'instructions'      => _x( 'Select how round you want the button to be', 'admin', 'wp-surf-theme' ),
						'type'              => 'range',
						'min'               => 0,
						'max'               => 200,
						'step'              => 1,
						'conditional_logic' => [
							'field'    => 'field_theme_settings_ib_type_button',
							'operator' => '==',
							'value'    => 'rounded',
						],
					],
					[
						'key'               => 'field_theme_settings_ib_button_cutout',
						'label'             => _x( 'Cutout', 'admin', 'wp-surf-theme' ),
						'name'              => 'cutout',
						'instructions'      => _x(
							'This will be used to decide the cutout for each corner of an button.',
							'admin',
							'wp-surf-theme'
						),
						'type'              => 'group',
						'sub_fields'        => [
							CutoutHelper::getCornerSettings(
								'button',
								'top_left',
								_x( 'Top left corner', 'admin', 'wp-surf-theme' )
							),
							CutoutHelper::getCornerSettings(
								'button',
								'top_right',
								_x( 'Top right corner', 'admin', 'wp-surf-theme' )
							),
							CutoutHelper::getCornerSettings(
								'button',
								'bottom_left',
								_x( 'Bottom left corner', 'admin', 'wp-surf-theme' )
							),
							CutoutHelper::getCornerSettings(
								'button',
								'bottom_right',
								_x( 'Bottom right corner', 'admin', 'wp-surf-theme' )
							),
						],
						'conditional_logic' => [
							'field'    => 'field_theme_settings_ib_type_button',
							'operator' => '==',
							'value'    => 'cutout',
						],
					],
				],
			],
		];
	}

	/**
	 * Site meta
	 * @return array[]
	 */
	public static function getSitePolyPerLanguage(): array
	{
		return [
			[
				'key'               => 'field_theme_settings_site_tab',
				'label'             => _x( 'Site', 'admin', 'wp-surf-theme' ),
				'name'              => '',
				'placement'         => 'left',
				'type'              => 'tab',
				'conditional_logic' => static::getPoweredByCondition(),
			],
			[
				'key'               => 'field_theme_settings_site_logos',
				'label'             => _x( "Logo's", 'admin', 'wp-surf-theme' ),
				'name'              => 'surf_logos',
				'type'              => 'repeater',
				'min'               => 1,
				'max'               => 3,
				'button_label'      => _x( 'Add logo', 'admin', 'wp-surf-theme' ),
				'conditional_logic' => static::getPoweredByCondition(),
				'sub_fields'        => [
					[
						'key'           => 'field_theme_settings_site_logos_logo',
						'label'         => _x( 'Logo', 'admin', 'wp-surf-theme' ),
						'name'          => 'logo',
						'type'          => 'image',
						'return_format' => 'id',
					],
					[
						'key'     => 'field_theme_settings_site_logos_logo_size',
						'label'   => _x( 'Logo size', 'admin', 'wp-surf-theme' ),
						'name'    => 'logo_size',
						'type'    => 'select',
						'choices' => [
							'100' => '100%',
							'150' => '150%',
							'200' => '200%',
						],
						'wrapper' => [ 'width' => 50 ],
					],
				],
			],
			[
				'key'          => 'field_theme_settings_site_theme_post_date_display_published_at_text',
				'label'        => _x( 'Post date display - Published at text', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'This will be displayed before the post\'s published date.', 'admin', 'wp-surf-theme' ),
				'name'         => 'surf_theme_post_date_display_published_at_text',
				'type'         => 'text',
				'wrapper'      => [ 'width' => 50 ],
			],
			[
				'key'          => 'field_theme_settings_site_theme_post_date_display_modified_at_text',
				'label'        => _x( 'Post date display - Modified at text', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'This will be displayed before the post\'s modified date.', 'admin', 'wp-surf-theme' ),
				'name'         => 'surf_theme_post_date_display_modified_at_text',
				'type'         => 'text',
				'wrapper'      => [ 'width' => 50 ],
			],
		];
	}

	/**
	 * Social Media
	 * @return array[]
	 */
	public static function getSocialMedia(): array
	{
		$fields = [
			[
				'key'       => 'field_theme_settings_socials_tab',
				'name'      => '',
				'label'     => _x( 'Social media', 'admin', 'wp-surf-theme' ),
				'placement' => 'left',
				'type'      => 'tab',
			],
		];

		foreach ( SocialHelper::allFollowOptions() as $slug => $name ) {
			$fields[] = [
				'key'   => 'field_theme_settings_social_' . $slug . '_url',
				'label' => $name,
				'name'  => $slug . '_url',
				'type'  => 'url',
			];
		}

		return $fields;
	}

	/**
	 * @return array[]
	 */
	public static function getErrorPage(): array
	{
		return [
			[
				'key'       => 'field_theme_settings_error_page_tab',
				'name'      => '',
				'label'     => _x( 'Error page', 'admin', 'wp-surf-theme' ),
				'placement' => 'left',
				'type'      => 'tab',
			],
			[
				'key'          => 'field_theme_settings_error_page',
				'label'        => _x( 'Error page', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'Select an error page, when there is not one selected, a standard page will be shown.', 'admin', 'wp-surf-theme' ),
				'name'         => 'error_page',
				'type'         => 'post_object',
				'post_type'    => [ 'page' ],
			],
		];
	}

	/**
	 * Footer
	 * @return array[]
	 */
	public static function getFooter(): array
	{
		return [
			[
				'key'       => 'field_theme_settings_footer_tab',
				'label'     => _x( 'Footer', 'admin', 'wp-surf-theme' ),
				'name'      => '',
				'placement' => 'left',
				'type'      => 'tab',
			],
			[
				'key'   => 'field_theme_settings_footer_simple',
				'label' => _x( 'Simple footer', 'admin', 'wp-surf-theme' ),
				'name'  => 'surf_theme_footer_simple',
				'type'  => 'true_false',
				'ui'    => '1',
			],
			[
				'key'   => 'field_theme_settings_footer_copyright',
				'label' => _x( 'Copyright', 'admin', 'wp-surf-theme' ),
				'name'  => 'surf_theme_footer_copyright',
				'type'  => 'text',
			],
		];
	}

	/**
	 * @return array[]
	 */
	public static function getBreadcrumbs(): array
	{
		return [
			[
				'key'       => 'field_theme_settings_breadcrumbs_tab',
				'label'     => _x( 'Breadcrumbs', 'admin', 'wp-surf-theme' ),
				'name'      => '',
				'placement' => 'left',
				'type'      => 'tab',
			],
			[
				'key'          => 'field_theme_settings_breadcrumbs_deactivate',
				'label'        => _x( 'Deactivate breadcrumbs', 'admin', 'wp-surf-theme' ),
				'instructions' => _x( 'Select post types where you would like to deactivate the breadcrumbs', 'admin', 'wp-surf-theme' ),
				'name'         => 'deactivate_breadcrumbs',
				'type'         => 'checkbox',
				'choices'      => [
					Post::getName()     => _x( 'Posts', 'admin', 'wp-surf-theme' ),
					Page::getName()     => _x( 'Pages', 'admin', 'wp-surf-theme' ),
					Agenda::getName()   => _x( 'Agenda', 'admin', 'wp-surf-theme' ),
					Asset::getName()    => _x( 'Assets', 'admin', 'wp-surf-theme' ),
					Download::getName() => _x( 'Downloads', 'admin', 'wp-surf-theme' ),
					Faq::getName()      => _x( 'FAQs', 'admin', 'wp-surf-theme' ),
					Vacancy::getName()  => _x( 'Vacancies', 'admin', 'wp-surf-theme' ),
				],
			],
		];
	}

	/**
	 * @return array[]
	 */
	public static function getSocialSharing(): array
	{
		$fields = [
			[
				'key'       => 'field_theme_settings_social_share_tab',
				'label'     => _x( 'Content Sharing', 'admin', 'wp-surf-theme' ),
				'name'      => '',
				'placement' => 'left',
				'type'      => 'tab',
			],
		];

		foreach ( SocialHelper::allShareOptions() as $slug => $name ) {
			$label    = sprintf( _x( 'Allow for sharing items via %1$s', 'admin', 'wp-surf-theme' ), $name );
			$fields[] = [
				'key'           => 'theme_settings_social_share_' . $slug,
				'label'         => $label,
				'name'          => 'social_share_' . $slug,
				'type'          => 'true_false',
				'ui'            => '1',
				'default_value' => 1,
			];
		}

		return $fields;
	}

	/**
	 * @return array
	 */
	public static function getSearch(): array
	{
		return [
			[
				'key'       => 'field_theme_settings_label_search_tab',
				'label'     => _x( 'Search labels', 'admin', 'wp-surf-theme' ),
				'name'      => '',
				'placement' => 'left',
				'type'      => 'tab',
			],
			[
				'key'           => 'field_theme_settings_labels_search_page_title',
				'label'         => _x( 'Search page title', 'admin', 'wp-surf-theme' ),
				'name'          => 'search_page_title',
				'type'          => 'text',
				'default_value' => _x( 'Search results', 'admin', 'wp-surf-theme' ),
			],
			...static::getPostTypeSearchFields(),
			...static::getTaxonomySearchFields(),
		];
	}

	/**
	 * @return array
	 */
	private static function getPoweredByCondition(): array
	{
		return [
			[
				'field'    => 'field_theme_settings_site_theme',
				'operator' => '==',
				'value'    => Theme::POWERED_BY_SURF,
			],
		];
	}

	/**
	 * @return array
	 */
	protected static function getPostTypeSearchFields(): array
	{
		$postTypes = get_post_types( [ 'exclude_from_search' => false ], 'objects' );

		if ( empty( $postTypes ) || !is_array( $postTypes ) ) {
			return [];
		}

		return array_values( array_map( function ( $type )
		{
			return [
				'key'           => "field_theme_settings_labels_search_name_$type->name",
				'name'          => "search_name_$type->name",
				'type'          => 'text',
				'label'         => sprintf( _x( 'Alternative search name: %s', 'admin', 'wp-surf-theme' ), $type->label ),
				'default_value' => $type->label,
			];
		}, $postTypes ) );
	}

	/**
	 * @return array
	 */
	protected static function getTaxonomySearchFields(): array
	{
		$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );

		if ( empty( $taxonomies ) || !is_array( $taxonomies ) ) {
			return [];
		}

		return array_values( array_map( function ( $taxonomy )
		{
			$postTypes = array_map( function ( $type )
			{
				return get_post_type_object( $type )?->label;
			}, $taxonomy->object_type );

			return [
				'key'           => "field_theme_settings_labels_search_taxonomy_$taxonomy->name",
				'name'          => "search_taxonomy_$taxonomy->name",
				'type'          => 'text',
				'label'         => sprintf(
					_x( 'Alternative search name: %s (%s)', 'admin', 'wp-surf-theme' ),
					$taxonomy->label,
					implode( ', ', $postTypes )
				),
				'default_value' => $taxonomy->label,
			];
		}, $taxonomies ) );
	}

}
