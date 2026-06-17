<?php

namespace SURF\PostTypes;

use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\HasFactory;
use SURF\Core\Taxonomies\TermCollection;
use SURF\Core\Traits\HasFields;
use SURF\Taxonomies\Category;
use SURF\Traits\HasArchiveWidgetAreaFilters;
use SURF\Traits\HasPublicationDate;

/**
 * Class Post
 * @package SURF\PostTypes
 */
class Post extends BasePost
{

	use HasFactory, HasArchiveWidgetAreaFilters, HasFields, HasPublicationDate;

	protected static string $postType = 'post';

	public const FIELD_PUB_DATE_OVERRIDE = 'publication_date_settings_override';

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Post', 'admin', 'wp-surf-theme' );
	}

	/**
	 * @param array $args
	 * @return TermCollection
	 */
	public function categories( array $args = [] ): TermCollection
	{
		return $this->getTerms( Category::getName(), $args );
	}

	/**
	 * @return array
	 */
	public static function getFields(): array
	{
		return [
			static::getImageGroup( static::getName() ),
			[
				'key'      => 'group_post_layout_settings',
				'title'    => _x( 'Layout Settings', 'admin', 'wp-surf-theme' ),
				'position' => 'side',
				'fields'   => [
					[
						'key'           => 'field_content_over_image',
						'label'         => _x( 'Content Over the Image', 'admin', 'wp-surf-theme' ),
						'name'          => static::FIELD_CONTENT_OVER_IMAGE,
						'type'          => 'true_false',
						'ui'            => true,
						'default_value' => true,
					],
				],
			],
			static::getPublicationGroup( static::getName() ),
			[
				'key'      => 'group_publication_date_settings_override',
				'title'    => _x( 'Publication date settings override', 'admin', 'wp-surf-theme' ),
				'position' => 'side',
				'fields'   => [
					[
						'key'           => 'field_publication_date_settings_override',
						'label'         => _x( 'Post date display', 'admin', 'wp-surf-theme' ),
						'name'          => static::FIELD_PUB_DATE_OVERRIDE,
						'instructions'  => _x( 'Select which date(s) you want to display. This only applies to posts and pages.', 'admin', 'wp-surf-theme' ),
						'type'          => 'select',
						'default_value' => 'default',
						'choices'       => [
							'default'   => _x( 'Site default', 'admin', 'wp-surf-theme' ),
							'hidden'    => _x( 'Hidden', 'admin', 'wp-surf-theme' ),
							'published' => _x( 'Published', 'admin', 'wp-surf-theme' ),
							'modified'  => _x( 'Modified', 'admin', 'wp-surf-theme' ),
							'both'      => _x( 'Published and modified', 'admin', 'wp-surf-theme' ),
						],
					],
				],
			],
		];
	}

}
