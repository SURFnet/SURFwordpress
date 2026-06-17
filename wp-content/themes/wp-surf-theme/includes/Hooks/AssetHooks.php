<?php

namespace SURF\Hooks;

use SURF\PostTypes\Asset;
use SURF\View\ViewModels\AssetsViewModel;
use WP_Post;
use WP_Post_Type;

/**
 * Class AssetHooks
 * @package SURF\Hooks
 */
class AssetHooks
{

	/**
	 * Register Asset hooks
	 */
	public static function register(): void
	{
		add_filter( 'get_the_archive_title', [ static::class, 'filterArchiveTitle' ], 10, 3 );
		add_filter( 'get_the_post_type_description', [ static::class, 'filterArchiveDescription' ], 10, 2 );
	}

	/**
	 * Updates the default title from Asset Category archive
	 * @param string $title
	 * @param string $original_title
	 * @param string $prefix
	 * @return string
	 */
	public static function filterArchiveTitle( string $title, string $original_title, string $prefix ): string
	{
		if ( !is_post_type_archive( Asset::getName() ) ) {
			return $title;
		}

		$setting = AssetsViewModel::getHeaderTitle();
		if ( !empty( $setting ) ) {
			return $setting;
		}

		return trim( str_replace( $prefix, '', $title ) );
	}

	/**
	 * Updates the default description from the Asset Category archive
	 * @param string $description
	 * @param WP_Post_Type $post_type_obj
	 * @return string
	 */
	public static function filterArchiveDescription( string $description, WP_Post_Type $post_type_obj ): string
	{
		if ( $post_type_obj->name !== Asset::getName() ) {
			return $description;
		}

		if ( !AssetsViewModel::hasFancyHeader() ) {
			return $description;
		}

		return apply_filters( 'the_content', AssetsViewModel::getHeaderDescription() );
	}

}
