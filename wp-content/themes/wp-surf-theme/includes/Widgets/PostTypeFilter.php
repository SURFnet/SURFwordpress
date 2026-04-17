<?php

namespace SURF\Widgets;

use SURF\Core\Widgets\Widget;

/**
 * Class PostTypeFilter
 * @package SURF\Widgets
 */
class PostTypeFilter extends Widget
{

	/**
	 * @return string
	 */
	protected function description(): string
	{
		return _x( 'Displays a list of post types to filter the current query', 'admin', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	protected function title(): string
	{
		return _x( 'Post type Filter', 'admin', 'wp-surf-theme' );
	}

	/**
	 * @return array[]
	 */
	protected function fields(): array
	{
		return [
			[
				'key'           => 'field_' . $this->getId() . '_title',
				'label'         => _x( 'Title', 'admin', 'wp-surf-theme' ),
				'name'          => 'title',
				'type'          => 'text',
				'default_value' => _x( 'Post type', 'admin', 'wp-surf-theme' ),
			],
		];
	}

}
