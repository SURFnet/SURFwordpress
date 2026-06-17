<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Traits\HasFields;
use SURF\Traits\HasCategoryColor;

/**
 * Class Category
 * @package SURF\Taxonomies
 */
class Category extends Taxonomy
{

	use HasFields, HasCategoryColor;

	protected static string $taxonomy = 'category';

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
					static::getColorField( $identifier ),
				],
			],
		];
	}

}
