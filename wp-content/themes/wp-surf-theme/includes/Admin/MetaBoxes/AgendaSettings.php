<?php

namespace SURF\Admin\MetaBoxes;

use SURF\Admin\MetaBoxes;
use SURF\Admin\Pages;
use SURF\PostTypes\Agenda;

/**
 * Class AgendaSettings
 * @package SURF\Admin\MetaBoxes
 */
class AgendaSettings
{

	/**
	 * @return void
	 */
	public static function init(): void
	{
		MetaBoxes::register( [
			'title'    => _x( 'Event settings', 'admin', 'wp-surf-theme' ),
			'key'      => 'group_agenda_global_settings',
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
					'param'    => 'options_page',
					'operator' => '==',
					'value'    => Pages::SLUG_EVENT_SETTINGS,
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
				'key'       => 'field_agenda_global_settings_archive',
				'label'     => _x( 'Archive page', 'admin', 'wp-surf-theme' ),
				'name'      => '',
				'placement' => 'left',
				'type'      => 'tab',
			],
			[
				'key'           => 'field_agenda_global_settings_archive_hide_expired',
				'label'         => _x( 'Hide expired items', 'admin', 'wp-surf-theme' ),
				'name'          => Agenda::SETTING_HIDE_EXPIRED,
				'type'          => 'true_false',
				'ui'            => true,
				'default_value' => true,
				'instructions'  => _x( 'Hide expired items and add a filter that allows the user to see them.', 'admin', 'wp-surf-theme' ),
			],
			[
				'key'       => 'field_agenda_global_settings_single',
				'label'     => _x( 'Detail page', 'admin', 'wp-surf-theme' ),
				'name'      => '',
				'placement' => 'left',
				'type'      => 'tab',
			],
			[
				'key'           => 'field_agenda_global_settings_single_show_related_items',
				'label'         => _x( 'Show related items', 'admin', 'wp-surf-theme' ),
				'name'          => Agenda::SETTING_SHOW_RELATED,
				'type'          => 'true_false',
				'ui'            => true,
				'default_value' => true,
				'instructions'  => _x( 'Show the related items block at the bottom of the single page.', 'admin', 'wp-surf-theme' ),
			],
		];
	}

}
