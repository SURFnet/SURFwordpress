<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\ArchiveController;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use SURF\Helpers\Helper;
use SURF\Helpers\TermHelper;
use SURF\PostTypes\Faq;
use SURF\Taxonomies\FaqCategory;
use WP_Query;

/**
 * Class ArchiveSURFFaqController
 * @package SURF
 */
class ArchiveSURFFaqController extends TemplateController
{

	use ArchiveController;

	/**
	 * @param PostCollection $faqs
	 * @return View
	 */
	public function handle( PostCollection $faqs ): View
	{
		$page = Faq::getArchivePage();

		$categoryName   = FaqCategory::getName();
		$categoryList   = FaqCategory::querySortedByPriority( [
			'hide_empty' => false,
		] )->reduce( function ( array $carry, FaqCategory $cat )
		{
			$carry[ $cat->slug ] = $cat->name;

			return $carry;
		}, [] );
		$categoryCounts = static::getFilterCounts( $faqs );

		$sortList = [
			'title--asc'  => __( 'Title - Ascending', 'wp-surf-theme' ),
			'title--desc' => __( 'Title - Descending', 'wp-surf-theme' ),
		];

		if ( get_option( 'options_faq_single_disable_id' ) !== '1' ) {
			$sortList['id--asc']  = __( 'ID - Ascending', 'wp-surf-theme' );
			$sortList['id--desc'] = __( 'ID - Descending', 'wp-surf-theme' );
		}

		return $this->view(
			'faq.archive',
			compact(
				'faqs',
				'page',
				'categoryName',
				'categoryList',
				'categoryCounts',
				'sortList'
			)
		);
	}

	/**
	 * @param PostCollection $faqs
	 * @return string
	 */
	public static function renderPosts( PostCollection $faqs ): string
	{
		return (string) surfView( 'faq.list-items', [ 'faqs' => $faqs, 'isSearching' => static::isSearching() ] );
	}

	/**
	 * @return string
	 */
	public static function renderNothingFound(): string
	{
		return (string) surfView( 'faq.not-found' );
	}

	/**
	 * @return int
	 */
	public static function getPostsPerPage(): int
	{
		return get_option( 'options_faq_archive_posts_per_page', 8 );
	}

	/**
	 * @param WP_Query $query
	 * @return WP_Query
	 */
	public static function afterQuery( WP_Query $query ): WP_Query
	{
		$parts   = explode( '--', Helper::getSanitizedRequest( 'orderby', '' ) );
		$orderby = $parts[0] ?? 'title';
		$order   = $parts[1] ?? 'asc';

		$query->set( 'order', $order );
		if ( $orderby === 'id' ) {
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'meta_key', 'ID' );

			return $query;
		}

		$query->set( 'orderby', $orderby );

		return $query;
	}

	/**
	 * C
	 * @return string[]|null
	 */
	protected static function getTaxQuery(): ?array
	{
		$tax_query  = [ 'relation' => 'AND' ];
		$taxonomies = static::getTaxonomies( 'rest_base' );

		foreach ( $taxonomies as $rest_base => $name ) {
			$value        = [];
			$request_name = Helper::getSanitizedRequest( $name, '' );
			if ( !empty( $request_name ) ) {
				$value = Helper::maybeGetArrayValues( $request_name );
			} else {
				$request_base = Helper::getSanitizedRequest( $rest_base, '' );
				if ( !empty( $request_base ) ) {
					$value = Helper::maybeGetArrayValues( $request_base );
				}
			}

			if ( !empty( $value ) ) {
				$groupedValue = TermHelper::groupByParent( $name, $value );

				$valueType = is_array( $value ) ? current( $value ) : $value;
				foreach ( $groupedValue as $group ) {
					$tax_query[] = [
						'taxonomy' => $name,
						'terms'    => $group,
						'field'    => is_numeric( $valueType ) ? 'term_id' : 'slug',
						'operator' => 'IN',
					];
				}

				unset( $value );
			}
		}

		return $tax_query ?: null;
	}

	/**
	 * @return array
	 */
	public static function getTaxonomyAliases(): array
	{
		return [
			FaqCategory::getName() => FaqCategory::getQueryKey(),
		];
	}

	/**
	 * @return bool
	 */
	public static function isSearching(): bool
	{
		$search   = Helper::getSanitizedGet( 'search', '' );
		$category = Helper::getSanitizedGet( 'category', '' );

		return !empty( $search ) || !empty( $category );
	}

}
