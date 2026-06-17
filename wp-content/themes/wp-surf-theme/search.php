<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\ArchiveController;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use SURF\Helpers\Helper;
use SURF\Imposters\Search;
use WP_Query;

/**
 * Class SearchController
 * @package SURF
 */
class SearchController extends TemplateController
{

	use ArchiveController;

	/**
	 * @param PostCollection $posts
	 * @param WP_Query $query
	 * @return View
	 */
	public function handle( PostCollection $posts, WP_Query $query ): View
	{
		$postTypeName = 'post_type';
		$postTypeList = [];
		foreach ( get_post_types( [ 'public' => true ], 'objects' ) as $postType ) {
			$postTypeSlug = $postType->name;
			if ( $postTypeSlug === 'attachment' ) {
				continue;
			}

			$postCount = wp_count_posts( $postTypeSlug );
			if ( $postCount->publish < 1 ) {
				continue;
			}

			$postTypeList[ $postTypeSlug ] = $postType->label;
		}

		$selectedPostType = Helper::getSanitizedRequest( $postTypeName, '' );
		$selectedPostType = !empty( $selectedPostType ) && array_key_exists( $selectedPostType, $postTypeList )
			? $selectedPostType
			: false;

		$taxonomies = [];
		if ( $selectedPostType ) {
			$taxonomies = get_object_taxonomies( $selectedPostType, 'objects' );
			$taxonomies = array_filter( $taxonomies, function ( $taxonomy )
			{
				return ( property_exists( $taxonomy, '_pll' ) && $taxonomy?->_pll === true ) ||
				       $taxonomy->name === 'post_format' ||
				       empty( $terms = get_terms( [
					       'taxonomy'   => $taxonomy->name,
					       'hide_empty' => false,
					       'parent'     => 0,
				       ] ) );
			} );
		}

		$widgetAreaPosition = Search::getArchiveWidgetAreaPosition();
		$widgetAreaId       = Search::getWidgetAreaId();

		$columnSpanClass = static::getColumnSpanClass();
		$postItemType    = static::getPostItemType();

		return $this->view(
			'search',
			compact(
				'query',
				'posts',
				'postTypeName',
				'postTypeList',
				'selectedPostType',
				'taxonomies',
				'widgetAreaPosition',
				'widgetAreaId',
				'columnSpanClass',
				'postItemType'
			)
		);
	}

	/**
	 * Renders Archive items
	 * @param PostCollection $posts
	 * @return string
	 */
	public static function renderPosts( PostCollection $posts )
	{
		$html = '';
		foreach ( $posts as $post ) {
			$html .= '<div class="' . static::getColumnSpanClass() . '">';
			$html .= surfView( 'search.item', [ 'post' => $post ] );
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Renders nothing found page
	 * @return string
	 */
	public static function renderNothingFound()
	{
		$html = '<div class="column span-4-sm span-8-md span-12-lg">';
		$html .= surfView( 'search.not-found' );
		$html .= '</div>';

		return $html;
	}

	/**
	 * @return string
	 */
	public static function getColumnSpanClass()
	{
		$isWidgetTop = Search::getArchiveWidgetAreaPosition() == 'top';
		$columnCount = $isWidgetTop ? Search::getColumnCount() : Search::getColumnCountWithWidgetArea();

		return match ( $columnCount ) {
			3       => 'column span-4-sm span-4-md span-4-lg',
			2       => 'column span-4-sm span-4-md span-6-lg',
			default => 'column span-4-sm span-8-md span-12-lg',
		};
	}

	/**
	 * @return string
	 */
	public static function getPostItemType()
	{
		$isHiddenWidget = Search::getArchiveWidgetAreaPosition() == 'hidden';
		$columnCount    = $isHiddenWidget ? Search::getColumnCount() : Search::getColumnCountWithWidgetArea();

		return match ( $columnCount ) {
			1       => 'row',
			2       => $isHiddenWidget ? 'row' : 'block',
			default => 'block',
		};
	}

}
