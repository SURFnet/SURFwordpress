<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\PostTypes\Vacancy;
use SURF\Traits\HasCategoryColor;

/**
 * Class VacancyCategory
 * @package SURF\Taxonomies
 */
class VacancyCategory extends Taxonomy
{

	use Registers, HasCategoryColor;

	protected static string $taxonomy  = 'surf-vacancy-category';
	protected static array  $postTypes = [ Vacancy::class ];

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Category', 'label singular - vacancy-category', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Categories', 'label plural - vacancy-category', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'category', 'tax slug - vacancy-category', 'wp-surf-theme' );
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
					static::getColorField( $identifier ),
				],
			],
		];
	}

}
