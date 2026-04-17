<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Traits\HasFields;
use SURF\PostTypes\Page;
use SURF\Traits\HasPriority;

/**
 * Class PageCategory
 * @package SURF\Taxonomies
 */
class PageCategory extends Taxonomy
{

	use Registers, HasFields, HasPriority;

	protected static string $taxonomy  = 'surf-page-category';
	protected static array  $postTypes = [ Page::class ];

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Category', 'label singular - page-category', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Categories', 'label plural - page-category', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'page-category', 'tax slug - page-category', 'wp-surf-theme' );
	}

	/**
	 * @return bool
	 */
	public static function useSlugInFilters(): bool
	{
		return true;
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
				'title'  => _x( 'Category settings', 'admin', 'wp-surf-theme' ),
				'fields' => [
					static::getPriorityField( $identifier ),
				],
			],
		];
	}

}
