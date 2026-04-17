<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Traits\HasFields;
use SURF\PostTypes\Page;
use SURF\Traits\HasPriority;

/**
 * Class PageTag
 * @package SURF\Taxonomies
 */
class PageTag extends Taxonomy
{

	use Registers, HasFields, HasPriority;

	protected static string $taxonomy  = 'surf-page-tag';
	protected static array  $postTypes = [ Page::class ];

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Tag', 'label singular - page-tag', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Tags', 'label plural - page-tag', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'page-tag', 'tax slug - page-tag', 'wp-surf-theme' );
	}

	/**
	 * @return array[]
	 */
	public static function getFields(): array
	{
		$identifier = str_replace( '-', '_', static::getName() );

		return [
			[
				'key'    => 'group_' . $identifier . '_settings',
				'title'  => _x( 'Tag settings', 'admin', 'wp-surf-theme' ),
				'fields' => [
					static::getPriorityField( $identifier ),
				],
			],
		];
	}

}
