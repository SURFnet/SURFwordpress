<?php

namespace SURF;

use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use SURF\Core\Controllers\ArchiveController;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use SURF\PostTypes\Asset;
use SURF\Taxonomies\AssetCategory;
use WP_Query;
use WP_Term;

/**
 * Class ArchiveSURFAssetController
 * @package SURF
 */
class ArchiveSURFAssetController extends TemplateController
{

	use ArchiveController;

	/**
	 * @param PostCollection $assets
	 * @return View
	 */
	public function handle( Request $request, PostCollection $assets ): View
	{
		global $wp_query;

		$cat_slug = $request->get( 'category' );
		$category = $cat_slug ? AssetCategory::findBySlug( $cat_slug ) : null;

		$page = Asset::getArchivePage();
		$data = [
			'section_id' => sanitize_key( 'archive-' . Asset::getName() ),
			'title'      => $page ? $page->title() : get_the_archive_title(),
			'content'    => $page ? $page->content() : get_the_archive_description(),
			'top_menu'   => AssetCategory::getTopMenu( $category, 'asset-top-menu__item' ),
			'aside_menu' => AssetCategory::getAsideMenu( $category ),
			'total'      => (int) ( $wp_query->found_posts ),
			'assets'     => $assets,
		];

		return $this->view( 'asset.archive', $data );
	}

	/**
	 * @param PostCollection $assets
	 * @return string
	 */
	public static function renderPosts( PostCollection $assets ): string
	{
		$filterPosition = get_option( 'options_surf-assets-archive-filters-position' ) ?: 'top';

		$html = '';
		foreach ( $assets as $asset ) {
			if ( in_array( $filterPosition, [ 'top', 'none' ] ) ) {
				$html .= '<div class="column span-4-sm span-4-md span-4-lg">';
			} else {
				$html .= '<div class="column span-6-sm span-6-md span-6-lg">';
			}

			$html .= surfView( 'asset.item', [ 'asset' => $asset, 'hideExcerpt' => true ] );
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * @return string
	 */
	public static function renderNothingFound(): string
	{
		$html = '';
		$html .= '<div class="column span-4-sm span-8-md span-12-lg">';
		$html .= surfView( 'asset.not-found' );
		$html .= '</div>';

		return $html;
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
			$term = $request->get( 'category' )
				? get_term_by( 'slug', $request->get( 'category' ), TaxonomySURFAssetCategoryController::getTaxonomy() )
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
				$term = $request->get( 'term' )
					? get_term_by( 'slug', $request->get( 'term' ), TaxonomySURFAssetCategoryController::getTaxonomy() )
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
