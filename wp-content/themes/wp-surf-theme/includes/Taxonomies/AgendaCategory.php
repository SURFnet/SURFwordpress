<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Traits\HasFields;
use SURF\PostTypes\Agenda;
use SURF\Traits\HasCategoryColor;
use SURF\Traits\HasPriority;

/**
 * Class AgendaCategory
 * @package SURF\Taxonomies
 */
class AgendaCategory extends Taxonomy
{

	use Registers, HasFields, HasPriority, HasCategoryColor;

	protected static string $taxonomy  = 'surf-agenda-category';
	protected static array  $postTypes = [ Agenda::class ];

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Category', 'label singular - agenda-category', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Categories', 'label plural - agenda-category', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'category', 'tax slug - agenda-category', 'wp-surf-theme' );
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
					static::getColorField( $identifier ),
				],
			],
		];
	}

}
