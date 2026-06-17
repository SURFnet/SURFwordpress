<?php

namespace SURF\Hooks;

use SURF\Helpers\Helper;
use SURF\PostTypes\Asset;
use WP_Query;

/**
 * Class SearchHooks
 * @package SURF\Hooks
 */
class SearchHooks
{

	/**
	 * @return void
	 */
	public static function register(): void
	{
		add_filter( 'posts_search', [ static::class, 'handleSearch' ], 15, 2 );
		add_filter( 'posts_join', [ static::class, 'handleSearchJoin' ], 15, 2 );
		add_filter( 'posts_groupby', [ static::class, 'handleSearchGroupBy' ], 15, 2 );

		add_filter( 'pre_get_posts', [ static::class, 'handleSearchPage' ], 10, 1 );
	}

	/**
	 * @param string $search
	 * @param WP_Query $query
	 * @return string
	 */
	public static function handleSearch( string $search, WP_Query $query ): string
	{
		if ( wp_is_json_request() || is_admin() || empty( $search ) || !$query->is_search() ) {
			return $search;
		}

		$optionEnabled = (bool) get_option( 'options_surf-assets-search-deep', false );

		if ( !$optionEnabled ) {
			return $search;
		}

		global $wpdb;

		$q = $query->query_vars;
		$t = (array) ( $q['search_terms'] ?? [] );
		$n = !empty( $q['exact'] ) ? '' : '%';

		$search = preg_replace( "/ AND /", " AND (", $search, 1 );

		if ( !empty( $t ) ) {
			$assetPostType = Asset::getName();
			$search        .= " OR ({$wpdb->posts}.post_type = '{$assetPostType}' AND (";
			$compare       = '';

			foreach ( $t as $term ) {
				$term = esc_sql( $wpdb->esc_like( $term ) );

				// Post
				$search .= "{$compare}({$wpdb->posts}.post_title LIKE '{$n}{$term}{$n}')";

				$compare = ' OR ';

				$search .= "{$compare}({$wpdb->posts}.post_content LIKE '{$n}{$term}{$n}')";
				$search .= "{$compare}({$wpdb->posts}.post_excerpt LIKE '{$n}{$term}{$n}')";

				// Author
				$search .= "{$compare}(vosu.display_name LIKE '{$n}{$term}{$n}')";

				// Meta author
				$search .= sprintf(
					"{$compare}{$wpdb->posts}.ID IN (%s)",
					"SELECT vosp.ID FROM {$wpdb->posts} vosp
                        WHERE (vospm.meta_key = 'author_text' AND vospm.meta_value LIKE '{$n}{$term}{$n}')
                        OR (vospm.meta_key = 'author_user' AND vospm.meta_value IN (
                            SELECT ID FROM {$wpdb->users} WHERE display_name LIKE '{$n}{$term}{$n}'
                        ))
                        GROUP BY vosp.id"
				);

				// Terms
				$search .= sprintf(
					"{$compare}{$wpdb->posts}.ID IN (%s)",
					"SELECT vosp.ID FROM {$wpdb->posts} vosp
                        WHERE vost.name LIKE '{$n}{$term}{$n}'
                        GROUP BY vosp.id"
				);

				// Files
				$search .= sprintf(
					"{$compare}{$wpdb->posts}.ID IN (%s)",
					"SELECT vosp.ID FROM {$wpdb->posts} vosp
                        WHERE vospp.post_title LIKE '{$n}{$term}{$n}'
                        GROUP BY vosp.id"
				);
			}

			$search .= ')))';
		}

		return $search;
	}

	/**
	 * @param string $join
	 * @param WP_Query $query
	 * @return string
	 */
	public static function handleSearchJoin( string $join, WP_Query $query ): string
	{
		if ( wp_is_json_request() || is_admin() || !$query->is_search() ) {
			return $join;
		}

		$optionEnabled = (bool) get_option( 'options_surf-assets-search-deep', false );

		if ( !$optionEnabled ) {
			return $join;
		}

		global $wpdb;

		// Author
		$join .= " LEFT JOIN {$wpdb->users} vosu ON ({$wpdb->posts}.post_author = vosu.ID) ";

		// Meta author
		$join .= " LEFT JOIN {$wpdb->postmeta} vospm ON ({$wpdb->posts}.ID = vospm.post_id) ";

		// Terms
		$join .= " LEFT JOIN {$wpdb->term_relationships} vostr ON ({$wpdb->posts}.ID = vostr.object_id) LEFT JOIN {$wpdb->term_taxonomy} vostt ON (vostt.term_taxonomy_id = vostr.term_taxonomy_id) LEFT JOIN {$wpdb->terms} vost ON (vost.term_id = vostt.term_id) ";

		// Files
		$join .= " LEFT JOIN {$wpdb->postmeta} vostm ON ({$wpdb->posts}.ID = vostm.post_id AND vostm.meta_key = 'file_id') LEFT JOIN {$wpdb->posts} vospp ON (vospp.ID = vostm.meta_value) ";

		return $join;
	}

	/**
	 * @param string $groupBy
	 * @param WP_Query $query
	 * @return string
	 */
	public static function handleSearchGroupBy( string $groupBy, WP_Query $query ): string
	{
		if ( wp_is_json_request() || is_admin() || !$query->is_search() ) {
			return $groupBy;
		}

		$optionEnabled = (bool) get_option( 'options_surf-assets-search-deep', false );

		if ( !$optionEnabled ) {
			return $groupBy;
		}

		global $wpdb;

		// Group by post ID to avoid duplicates
		return "{$wpdb->posts}.ID";
	}

	/**
	 * @param WP_Query $query
	 * @return WP_Query
	 */
	public static function handleSearchPage( WP_Query $query )
	{
		if ( !$query->is_search() || !$query->is_main_query() ) {
			return $query;
		}

		$term = Helper::getSanitizedRequest( 'term', [] );
		if ( !empty( $term ) && is_array( $term ) ) {
			foreach ( $term as $value ) {
				$term = get_term( $value );

				if ( $term ) {
					$query->set( 'tax_query', [
						[
							'taxonomy' => $term->taxonomy,
							'field'    => 'term_id',
							'terms'    => $term->term_id,
						],
					] );
				}
			}
		}

		$type = Helper::getSanitizedRequest( 'post_type', [] );
		if ( !empty( $type ) ) {
			if ( in_array( $type, get_post_types(), true ) ) {
				$query->set( 'post_type', $type );
			}
		}

		return $query;
	}

}
