<?php

namespace SURF\Admin\MetaBoxes;

use SURF\Admin\MetaBoxes;

/**
 * Class UserSettings
 * @package SURF\Admin\MetaBoxes
 */
class UserSettings
{

	/**
	 * @return void
	 */
	public static function init(): void
	{
		MetaBoxes::register( [
			'title'    => _x( 'User settings', 'admin', 'wp-surf-theme' ),
			'key'      => 'group_user_settings',
			'location' => static::getLocation(),
			'fields'   => static::getFields(),
		] );
	}

	/**
	 * @return array[]
	 */
	public static function getLocation(): array
	{
		return [
			[
				[
					'param'    => 'user_form',
					'operator' => '==',
					'value'    => 'edit',
				],
			],
		];
	}

	/**
	 * @return array[]
	 */
	public static function getFields(): array
	{
		return [
			[
				'key'           => 'user_settings_disable_archive',
				'label'         => _x( 'Disable archive', 'admin', 'wp-surf-theme' ),
				'name'          => 'disable_archive',
				'type'          => 'true_false',
				'default_value' => 0,
			],
		];
	}

}
