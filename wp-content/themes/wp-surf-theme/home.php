<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\ArchiveController;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use SURF\PostTypes\Page;
use SURF\PostTypes\Post;
use SURF\Taxonomies\Category;
use WP_Query;

/**
 * Class HomeController
 * @package SURF
 */
class HomeController extends TemplateController
{

	use ArchiveController;

	/**
	 * @param WP_Query $query
	 * @param PostCollection $posts
	 * @return View
	 * @throws Core\Exceptions\MismatchingPostTypesException
	 */
	public function handle( WP_Query $query, PostCollection $posts ): View
	{
		$page = Page::find( get_option( 'page_for_posts' ) );

		// Category
		$categoryName = Category::getName();
		$categoryList = [];
		$termList     = get_terms( [ 'taxonomy' => $categoryName, 'hide_empty' => false ] );
		if ( is_array( $termList ) ) {
			foreach ( $termList as $term ) {
				$categoryList[ $term->slug ] = $term->name;
			}
		}

		$widgetAreaPosition = Post::getArchiveWidgetAreaPosition();
		$widgetAreaId       = Post::getWidgetAreaId();
		$columnSpanClass    = static::getColumnSpanClass();
		$postItemType       = static::getPostItemType();

		return $this->view(
			'home',
			compact(
				'query',
				'posts',
				'page',
				'categoryName',
				'categoryList',

				'widgetAreaPosition',
				'widgetAreaId',

				'columnSpanClass',
				'postItemType'
			)
		);
	}

	/**
	 * @param PostCollection $posts
	 * @return string
	 */
	public static function renderPosts( PostCollection $posts )
	{
		$postItemType = static::getPostItemType();
		$html         = '';
		foreach ( $posts as $post ) {
			$html .= '<div class="' . static::getColumnSpanClass() . '">';
			$html .= surfView( 'post.item', [ 'post' => $post, 'type' => $postItemType, 'headingTag' => 'h2' ] );
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * @return string
	 */
	public static function renderNothingFound()
	{
		$html = '';
		$html .= '<div class="column span-4-sm span-8-md span-12-lg">';
		$html .= surfView( 'parts.content-none' );
		$html .= '</div>';

		return $html;
	}

	/**
	 * @return string
	 */
	public static function getPostType(): string
	{
		return 'post';
	}

	/**
	 * @return string
	 */
	public static function getColumnSpanClass()
	{
		$columnCount = static::hasSidebar() ? Post::getColumnCountWithWidgetArea() : Post::getColumnCount();

		return match ( $columnCount ) {
			1       => 'column span-4-sm span-8-md span-12-lg',
			2       => 'column span-4-sm span-4-md span-6-lg',
			3       => 'column span-4-sm span-4-md span-4-lg',
			default => 'column span-4-sm span-8-md span-12-lg',
		};
	}

	/**
	 * @return string
	 */
	public static function getPostItemType()
	{
		$hasSidebar  = static::hasSidebar();
		$columnCount = $hasSidebar ? Post::getColumnCountWithWidgetArea() : Post::getColumnCount();

		return match ( $columnCount ) {
			1       => 'row',
			2       => $hasSidebar ? 'block' : 'row',
			3       => 'block',
			default => 'block',
		};
	}

	/**
	 * @return bool
	 */
	public static function hasSidebar(): bool
	{
		return !in_array( Post::getArchiveWidgetAreaPosition(), [ 'hidden', 'top' ] );
	}

	/**
	 * @return array
	 */
	public static function getTaxonomyAliases(): array
	{
		return [
			Category::getName() => Category::getQueryKey(),
		];
	}

}
