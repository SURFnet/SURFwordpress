<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\Traits\HasFields;
use SURF\PostTypes\Faq;
use SURF\Traits\HasPriority;

/**
 * Class FaqCategory
 * @package SURF\Taxonomies
 */
class FaqCategory extends Taxonomy
{

	use Registers, HasFields, HasPriority;

	protected static string $taxonomy  = 'surf-faq-category';
	protected static array  $postTypes = [ Faq::class ];

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Category', 'label singular - faq-category', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Categories', 'label plural - faq-category', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'category', 'tax slug - faq-category', 'wp-surf-theme' );
	}

	/**
	 * @return bool
	 */
	public static function useSlugInFilters(): bool
	{
		return true;
	}

	/**
	 * @return bool
	 */
	public static function shouldShowParents(): bool
	{
		return (bool) get_option('options_faq_show_parent_categories', false);
	}

	/**
	 * @return bool
	 */
	public static function hasArchive(): bool
	{
		return Faq::hasTaxArchive(static::getName());
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
					[
						'key'   => 'field_' . $identifier . '_activate_toggle',
						'label' => _x( 'Activate Toggle', 'admin', 'wp-surf-theme' ),
						'name'  => 'activate_toggle',
						'type'  => 'true_false',
						'ui'    => 1,
					],
				],
			],
		];
	}

}
