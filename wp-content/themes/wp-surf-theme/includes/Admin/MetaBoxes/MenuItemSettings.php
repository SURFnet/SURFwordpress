<?php

namespace SURF\Admin\MetaBoxes;

use SURF\Admin\MetaBoxes;

/**
 * Class MenuItemSettings
 * @package SURF\Admin\MetaBoxes
 */
class MenuItemSettings
{

	/**
	 * @return void
	 */
	public static function init(): void
	{
		MetaBoxes::register( [
			'title'    => _x( 'Menu item settings', 'admin', 'wp-surf-theme' ),
			'key'      => 'group_menu_item_settings',
			'location' => static::getLocation(),
			'fields'   => static::getFields(),
		] );
	}

	/**
	 * Gets the default location + all other locations for polylang
	 * @return array[]
	 */
	public static function getLocation(): array
	{
		$list = [
			[
				[
					'param'    => 'nav_menu_item',
					'operator' => '==',
					'value'    => 'location/primary-menu',
				],
			],
		];

		if ( function_exists( 'pll_languages_list' ) ) {
			$languages = pll_languages_list();
			foreach ( $languages as $language ) {
				if ( $language === pll_default_language() ) {
					continue;
				}

				$list[] = [
					[
						'param'    => 'nav_menu_item',
						'operator' => '==',
						'value'    => 'location/primary-menu___' . $language,
					],
				];
			}
		}

		return $list;
	}

	public static function getFields(): array
	{
		return [
			[
				'key'     => 'menu_item_setting_button_type',
				'label'   => _x( 'Button type', 'admin', 'wp-surf-theme' ),
				'name'    => 'menu_item_setting_button_type',
				'type'    => 'select',
				'choices' => [
					'none'      => _x( 'None', 'admin', 'wp-surf-theme' ),
					'primary'   => _x( 'Filled', 'admin', 'wp-surf-theme' ),
					'secondary' => _x( 'Transparent', 'admin', 'wp-surf-theme' ),
				],
			],
			[
				'key'   => 'menu_item_setting_button_color',
				'label' => _x( 'Button color', 'admin', 'wp-surf-theme' ),
				'name'  => 'menu_item_setting_button_color',
				'type'  => 'color_picker',
			],
			[
				'key'   => 'menu_item_setting_button_color_hover',
				'label' => _x( 'Button hover color', 'admin', 'wp-surf-theme' ),
				'name'  => 'menu_item_setting_button_color_hover',
				'type'  => 'color_picker',
			],
		];
	}

}
