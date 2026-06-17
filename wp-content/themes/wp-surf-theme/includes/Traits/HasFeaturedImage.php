<?php

namespace SURF\Traits;

/**
 * Trait HasFeaturedImage
 * @package SURF\Traits
 */
trait HasFeaturedImage
{

	public const FIELD_HIDE_FEATURED_IMAGE = 'hide_featured_image';

	/**
	 * @return bool
	 */
	public function shouldHideFeaturedImage(): bool
	{
		return (bool) $this->getMeta( static::FIELD_HIDE_FEATURED_IMAGE, false );
	}

	/**
	 * @return array[]
	 */
	public static function getHideImageField( string $identifier = 'surf' ): array
	{
		return [
			'key'          => 'field_' . $identifier . '_' . static::FIELD_HIDE_FEATURED_IMAGE,
			'label'        => _x( 'Hide featured image', 'admin', 'wp-surf-theme' ),
			'name'         => static::FIELD_HIDE_FEATURED_IMAGE,
			'instructions' => _x( 'Hide the featured image on the single page', 'admin', 'wp-surf-theme' ),
			'type'         => 'true_false',
			'ui'           => true,
		];
	}

	/**
	 * @return array[]
	 */
	public static function getImageGroup( string $identifier = 'surf' ): array
	{
		return [
			'key'      => 'group_' . $identifier . '_featured_image_settings',
			'title'    => _x( 'Featured Image Settings', 'admin', 'wp-surf-theme' ),
			'position' => 'side',
			'fields'   => [
				static::getHideImageField( $identifier ),
			],
		];
	}

}
