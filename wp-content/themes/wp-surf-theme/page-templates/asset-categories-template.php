<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use SURF\Core\Controllers\TemplateController;
use SURF\PostTypes\Asset;
use SURF\PostTypes\Page;
use SURF\Taxonomies\AssetCategory;

/**
 * Template Name: Asset Categories template
 */
_x( 'Asset Categories template', 'template', 'wp-surf-theme' );

/**
 * Class AssetCategoriesTemplateController
 * @package SURF
 */
class AssetCategoriesTemplateController extends TemplateController
{

	protected int $perPage = 9;

	/**
	 * @param Request $request
	 * @param Page $page
	 * @return View
	 */
	public function handle( Request $request, Page $page ): View
	{
		if ( $request->get( 'search' ) ) {
			return $this->handleSearch( $request, $page );
		}

		$currentPage = intval( $request->get( '_page', 1 ) );

		$category    = AssetCategory::findBySlug( $request->get( 'category', '' ) );
		$hasChildren = $category?->children()->count() > 0;
		$totalPages  = ( $category?->children()->count() / $this->perPage ) ?? 0;
		$subCategories = null;
		if ( $hasChildren ) {
			$subCategories = AssetCategory::querySortedByPriority( [
				'number'   => $this->perPage,
				'offset'   => ( $currentPage - 1 ) * $this->perPage,
				'parent'   => $category->term_id,
			] );
		}

		$mainCategories = AssetCategory::querySortedByPriority( [
			'hide_empty' => false,
			'parent'     => 0,
		] );

		return $this->view(
			'asset.template-categories',
			compact(
				'currentPage',
				'totalPages',
				'page',
				'category',
				'subCategories',
				'mainCategories'
			)
		);
	}

	/**
	 * @param Request $request
	 * @param Page $page
	 * @return View
	 */
	public function handleSearch( Request $request, Page $page ): View
	{
		$search         = $request->get( 'search' );
		$mainCategories = AssetCategory::querySortedByPriority( [
			'hide_empty' => false,
			'parent'     => 0,
		] );

		$queries = $mainCategories->map( function ( AssetCategory $cat ) use ( $search, $page )
		{
			return [
				'label'  => $cat->name,
				'link'   => add_query_arg( 'category', $cat->slug, $page->permalink() ),
				'assets' => Asset::query( [
					'posts_per_page' => 10,
					's'              => $search,
					'tax_query'      => [
						[
							'taxonomy' => AssetCategory::getName(),
							'terms'    => $cat->term_id,
						],
					],
				] ),
			];
		} )->toArray();

		return $this->view(
			'asset.search',
			compact(
				'page',
				'mainCategories',
				'queries'
			)
		);
	}

}
