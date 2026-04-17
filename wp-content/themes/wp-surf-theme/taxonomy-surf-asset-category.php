<?php

namespace SURF;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use SURF\Core\Controllers\TaxonomyArchiveController;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use SURF\PostTypes\Asset;
use SURF\Taxonomies\AssetCategory;
use WP_Query;
use WP_Term;

/**
 * Class TaxonomySURFAssetCategoryController
 * @package SURF
 */
class TaxonomySURFAssetCategoryController extends TemplateController
{

	use TaxonomyArchiveController;

	/**
	 * @param Request $request
	 * @param PostCollection $assets
	 * @param AssetCategory $category
	 * @return View
	 */
	public function handle( Request $request, PostCollection $assets, AssetCategory $category ): View
	{
		global $wp_query;

		$data = [
			'section_id' => sanitize_key( 'archive-' . AssetCategory::getName() ),
			'title'      => $category->name,
			'content'    => $category->description,
			'top_menu'   => AssetCategory::getTopMenu( $category, 'asset-top-menu__item' ),
			'aside_menu' => AssetCategory::getAsideMenu( $category ),
			'selected'   => $category->slug,
			'total'      => (int) ( $wp_query->found_posts ),
			'assets'     => $assets,
		];

		return $this->view( 'asset.archive', $data );
	}

	/**
	 * @return int
	 */
	public static function getPostsPerPage(): int
	{
		return 12;
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	public static function renderIntro(): string
	{
		$request  = surfApp( Request::class );
		$category = AssetCategory::findBySlug( $request->get( 'category' ) ?: $request->get( 'term' ) );

		return surfView( 'asset.parts.archive-header', [
			'title'   => $category->name,
			'content' => $category->description,
		] );
	}

	/**
	 * @param PostCollection $posts
	 * @return View
	 */
	public static function renderPosts( PostCollection $posts ): View
	{
		return surfView( 'asset.items', [ 'assets' => $posts, 'hideExcerpt' => true ] );
	}

	/**
	 * @return View
	 * @throws ContainerExceptionInterface
	 * @throws Exception
	 */
	public static function renderNothingFound(): View
	{
		$request  = surfApp( Request::class );
		$category = AssetCategory::findBySlug( $request->get( 'category' ) ?: $request->get( 'term' ) );

		return surfView( 'asset.items', [ 'assets' => collect(), 'hideExcerpt' => true ] );
	}

	/**
	 * @return array
	 */
	public static function getTaxonomyAliases(): array
	{
		return [
			AssetCategory::getName() => AssetCategory::getQueryKey(),
		];
	}

	/**
	 * @param WP_Query $query
	 * @return WP_Query
	 * @throws Exception
	 */
	public static function afterQuery( WP_Query $query ): WP_Query
	{
		$request = surfApp( Request::class );
		try {
			/** @var WP_Term $term */
			$term = $request->get( 'term' )
				? get_term_by( 'slug', $request->get( 'term' ), static::getTaxonomy() )
				: get_queried_object();
		} catch ( Exception $exception ) {
			throw new Exception( $exception->getMessage(), $exception->getCode() );
		}
		if ( !( $term instanceof WP_Term ) ) {
			return $query;
		}

		$children = AssetCategory::querySortedByPriority( [
			'parent' => $term->term_id,
		] );
		if ( $children->isNotEmpty() ) {
			try {
				$term = $request->get( 'category' )
					? get_term_by( 'slug', $request->get( 'category' ), static::getTaxonomy() )
					: $term;

				$query->set( 'tax_query', [
					[
						'taxonomy' => $term->taxonomy,
						'field'    => 'term_id',
						'terms'    => [ $term->term_id ],
					],
				] );
			} catch ( Exception $exception ) {
				throw new Exception( $exception->getMessage(), $exception->getCode() );
			}
		}

		return Asset::setOrderBy( $query, $request->get( 'sort-by' ) );
	}

}
