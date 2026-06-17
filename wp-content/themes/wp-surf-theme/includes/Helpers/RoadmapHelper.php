<?php

namespace SURF\Helpers;

/**
 * Class RoadmapHelper
 * @package SURF\Helpers
 */
class RoadmapHelper
{

	/**
	 * @return array
	 */
	public static function listIcons(): array
	{
		return [
			'file'          => _x( 'File', 'admin', 'wp-surf-theme' ),
			'file-pdf'      => _x( 'PDF', 'admin', 'wp-surf-theme' ),
			'message'       => _x( 'Chat bubble', 'admin', 'wp-surf-theme' ),
			'messages'      => _x( 'Chat bubbles', 'admin', 'wp-surf-theme' ),
			'comment'       => _x( 'Speech bubble', 'admin', 'wp-surf-theme' ),
			'comments'      => _x( 'Speech bubbles', 'admin', 'wp-surf-theme' ),
			'address-card'  => _x( 'Address Cards', 'admin', 'wp-surf-theme' ),
			'clock'         => _x( 'Clock', 'admin', 'wp-surf-theme' ),
			'bullseye'      => _x( 'Bullseye', 'admin', 'wp-surf-theme' ),
			'calendar-days' => _x( 'Calendar', 'admin', 'wp-surf-theme' ),
			'newspaper'     => _x( 'Newspaper', 'admin', 'wp-surf-theme' ),
			'clipboard 2'   => _x( 'Clipboard', 'admin', 'wp-surf-theme' ),
			'code'          => _x( 'Code', 'admin', 'wp-surf-theme' ),
			'envelope'      => _x( 'Envelope', 'admin', 'wp-surf-theme' ),
			'notebook'      => _x( 'Notebook', 'admin', 'wp-surf-theme' ),
			'heart'         => _x( 'Heart', 'admin', 'wp-surf-theme' ),
			'lightbulb'     => _x( 'Lightbulb', 'admin', 'wp-surf-theme' ),
			'list-ol'       => _x( 'List', 'admin', 'wp-surf-theme' ),
			'network-wired' => _x( 'Network', 'admin', 'wp-surf-theme' ),
			'wifi'          => _x( 'Wifi', 'admin', 'wp-surf-theme' ),
			'vials'         => _x( 'Experiment', 'admin', 'wp-surf-theme' ),
			'coins'         => _x( 'Databases', 'admin', 'wp-surf-theme' ),
			'cake-candles'  => _x( 'Cake', 'admin', 'wp-surf-theme' ),
			'star'          => _x( 'Star', 'admin', 'wp-surf-theme' ),
			'tree'          => _x( 'Tree', 'admin', 'wp-surf-theme' ),
			'turntable'     => _x( 'Turntable', 'admin', 'wp-surf-theme' ),
			'paper-plane'   => _x( 'Paper plane', 'admin', 'wp-surf-theme' ),
		];
	}

	/**
	 * @return array
	 */
	public static function getIconsForScript(): array
	{
		$list = [];
		foreach ( static::listIcons() as $slug => $label ) {
			$list[] = [
				'label' => $label,
				'slug'  => $slug,
			];
		}

		return $list;
	}

}
