<?php

namespace SURF\Admin\MetaBoxes;

use SURF\Admin\MetaBoxes;
use SURF\PostTypes\Agenda;
use SURF\PostTypes\Asset;
use SURF\PostTypes\ContactPerson;
use SURF\PostTypes\Download;
use SURF\PostTypes\Faq;
use SURF\PostTypes\Page;
use SURF\PostTypes\Post;

/**
 * Class ContactPersonsSettings
 * @package SURF\Admin\MetaBoxes
 */
class ContactPersonsSettings
{

	/**
	 * @return void
	 */
	public static function init(): void
	{
		MetaBoxes::register( [
			'title'    => _x( 'Contact persons', 'admin', 'wp-surf-theme' ),
			'key'      => 'group_contact_persons_settings',
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
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => Agenda::getName(),
				],
			],
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => Asset::getName(),
				],
			],
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => Faq::getName(),
				],
			],
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => Download::getName(),
				],
			],
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => Page::getName(),
				],
				[
					'param'    => 'page_type',
					'operator' => '!=',
					'value'    => 'front_page',
				],
			],
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => Post::getName(),
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
				'key'           => 'field_contact_persons',
				'label'         => _x( 'Contact persons', 'admin', 'wp-surf-theme' ),
				'name'          => 'contact_persons',
				'type'          => 'relationship',
				'post_type'     => [ ContactPerson::getName() ],
				'multiple'      => 1,
				'max'           => 2,
				'filters'       => [ 'search' ],
				'return_format' => 'object',
			],
		];
	}

}
