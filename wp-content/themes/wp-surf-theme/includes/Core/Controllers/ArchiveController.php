<?php

namespace SURF\Core\Controllers;

use Exception;
use SURF\Core\PostTypes\BasePost;
use SURF\Core\PostTypes\PostCollection;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\Core\View\Template;
use SURF\Helpers\Helper;
use SURF\Helpers\TaxonomyHelper;
use SURF\Helpers\PolylangHelper;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use WP_Taxonomy;

/**
 * Class ArchiveController
 * @package SURF\Core\Controllers
 */
trait ArchiveController
{

	/** Post Type: Set on init based on file. */
	protected static string $postType;

	/**
	 * @Overwrite to change posts per page on the archive page.
	 * @return int
	 */
	public static function getPostsPerPage(): int
	{
		return 9;
	}

	/**
	 * @Overwrite
	 * Array of meta keys that can be used in filtering.
	 * Usage :
	 *  [
	 *      'meta_key',                 | Use this if you want to use the meta key as the url parameter
	 *      'alias' => 'meta_key',      | Use this if you want to use an alias for the url parameter
	 *  ].
	 */
	public static function getMetaKeys(): array
	{
		return [];
	}

	/**
	 * @Overwrite
	 * Return aliases for taxonomies (to be overwritten in child class)
	 * usage: [ 'alias' => 'tax_name' ].
	 */
	public static function getTaxonomyAliases(): array
	{
		return [];
	}

	/**
	 * @Overwrite
	 * Array of Operators that are used in filtering.
	 * Usage :
	 *  [
	 *      'tax_name|alias' => '<OPERATOR>',| Use this if you want to use an alternate Operator for searching
	 *                                       | Possible values: ‘IN’, ‘NOT IN’, ‘AND’, ‘EXISTS’ and ‘NOT EXISTS’.
	 *  ].
	 */
	public static function getTaxonomyOperator(): array
	{
		return [];
	}

	/**
	 * @Overwrite this function to extend the custom query for this post type.
	 * @param WP_Query $query
	 * @return WP_Query
	 */
	public static function afterQuery( WP_Query $query ): WP_Query
	{
		return $query;
	}

	/**
	 * Post Type the controller is using.
	 * @return string
	 */
	public static function getPostType(): string
	{
		return static::$postType;
	}

	/**
	 * Sets up archive page and Traits.
	 * @param string $postType
	 * @return void
	 */
	public static function init( string $postType ): void
	{
		static::$postType = $postType;

		add_action( 'pre_get_posts', [ static::class, 'preGetPosts' ] );

		static::initRest();
	}

	/**
	 * Return an array of taxonomies of the Post type.
	 * @param string $output
	 * @return array
	 */
	public static function getTaxonomies( string $output = 'names' ): array
	{
		$taxonomies = get_object_taxonomies( static::getPostType(), $output );

		if ( is_search() || static::getPostType() === 'search' ) {
			$taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
		}

		if ( $output === 'rest_base' ) {
			$taxonomies = array_flip(
				array_filter(
					array_map(
						fn( WP_Taxonomy $taxonomy ) => $taxonomy->rest_base,
						$taxonomies
					)
				)
			);
		}

		return array_merge( $taxonomies, array_flip( static::getTaxonomyAliases() ) );
	}

	/**
	 * Sets up pre_get_pots for the archive page.
	 * @param WP_Query $query
	 * @return WP_Query
	 */
	public static function preGetPosts( WP_Query $query ): WP_Query
	{
		if ( $query->get( 'api_index' ) === static::getPostType() ) {
			return static::query( $query );
		}

		if ( static::getPostType() === 'search' ) {
			if ( is_admin() || !$query->is_main_query() || !$query->is_search ) {
				return $query;
			}
		} else {
			if ( static::getPostType() === 'post' ) {
				if ( is_admin() || !$query->is_main_query() || !$query->is_posts_page ) {
					return $query;
				}
			} else {
				if ( is_admin() || !$query->is_main_query() || !$query->is_post_type_archive( static::getPostType() ) ) {
					return $query;
				}
			}
		}

		return static::query( $query );
	}

	/**
	 * Add everything in the $_REQUEST to the query
	 * @param WP_Query $query
	 * @return WP_Query
	 */
	public static function query( WP_Query $query ): WP_Query
	{
		$query->set( 'posts_per_page', static::getPostsPerPage() );
		if ( isset( $_REQUEST['search'] ) || isset( $_REQUEST['s'] ) ) {
			$query->set( 'is_filtering', 1 );
			$query->set( 's', Helper::getSanitizedRequest( 'search', Helper::getSanitizedRequest( 's', '' ) ) );
		}

		if ( isset( $_REQUEST['post_type'] ) ) {
			$query->set( 'is_filtering', 1 );
			$query->set( 'post_type', Helper::getSanitizedRequest( 'post_type' ) );
		}

		$add_tax_query = static::getTaxQuery();
		if ( $add_tax_query ) {
			$query->set( 'is_filtering', 1 );
			$tax_query = $query->get( 'tax_query' );
			if ( empty( $tax_query ) ) {
				$tax_query = [];
			}
			$tax_query[] = $add_tax_query;
			$query->set( 'tax_query', array_merge( $tax_query ) );
		}

		$add_meta_query = static::getMetaQuery();
		if ( $add_meta_query ) {
			$query->set( 'is_filtering', 1 );
			$meta_query = $query->get( 'meta_query' );
			if ( empty( $meta_query ) ) {
				$meta_query = [];
			}
			$meta_query[] = $add_meta_query;
			$query->set( 'meta_query', array_merge( $meta_query ) );
		}

		return static::afterQuery( $query );
	}

	/**
	 * Creates the Taxonomy query based on the $_REQUEST
	 * @return array|null
	 */
	protected static function getTaxQuery(): ?array
	{
		$tax_query  = [];
		$taxonomies = static::getTaxonomies( 'rest_base' );
		foreach ( $taxonomies as $rest_base => $name ) {
			$request_name = Helper::getSanitizedRequest( $name, '' );
			if ( !empty( $request_name ) ) {
				$value = Helper::maybeGetArrayValues( $request_name );
			} else {
				$request_base = Helper::getSanitizedRequest( $rest_base, '' );
				if ( !empty( $request_base ) ) {
					$value = Helper::maybeGetArrayValues( $request_base );
				}
			}
			if ( empty( $value ) ) {
				continue;
			}

			$value_type = $value;
			if ( is_array( $value_type ) ) {
				$value_type = current( $value );
			}

			$taxonomy = TaxonomyHelper::getTaxonomyFromName( $name );
			if ( $taxonomy && method_exists( $taxonomy, 'useSlugInFilters' ) ) {
				$field = $taxonomy::useSlugInFilters() ? 'slug' : 'term_id';
			} else {
				$field = is_numeric( $value_type ) ? 'term_id' : 'slug';
			}

			$query = [
				'taxonomy' => $name,
				'terms'    => $value,
				'field'    => $field,
			];

			if ( !empty( static::getTaxonomyOperator()[ $name ] ) ) {
				$query['operator'] = static::getTaxonomyOperator()[ $name ];
			}

			$tax_query[] = $query;

			unset( $value );
		}

		return $tax_query ?: null;
	}

	/**
	 * Creates the Meta query based on the $_REQUEST
	 * @return array|null
	 */
	protected static function getMetaQuery(): ?array
	{
		$meta_query = [];
		$meta_keys  = static::getMetaKeys();
		foreach ( $meta_keys as $mapped_key => $meta_key ) {
			if ( is_string( $mapped_key ) && !empty( $_REQUEST[ $mapped_key ] ) ) {
				$value = Helper::getSanitizedRequest( $mapped_key );
			} elseif ( !empty( $_REQUEST[ $meta_key ] ) ) {
				$value = Helper::getSanitizedRequest( $meta_key );
			}
			if ( isset( $value ) ) {
				$meta_query[] = [
					'key'   => $meta_key,
					'value' => $value,
				];
				unset( $value );
			}
		}

		return $meta_query ?: null;
	}

	// =========================================================================== \\
	// ============================== API Functions ============================== \\
	// =========================================================================== \\

	/**
	 * Renders Archive items.
	 * @param PostCollection $posts
	 * @return mixed
	 */
	abstract public static function renderPosts( PostCollection $posts );

	/**
	 * Renders nothing found page.
	 * @return mixed
	 */
	abstract public static function renderNothingFound();

	/**
	 * Initializes API endpoint.
	 * @return void
	 */
	public static function initRest(): void
	{
		add_action( 'rest_api_init', function ()
		{
			register_rest_route( 'surf/v1', '/' . static::getPostType(), [
				'methods'             => [ 'GET', 'POST' ],
				'callback'            => [ static::class, 'index' ],
				'permission_callback' => '__return_true',
			] );
		} );
	}

	/**
	 * Get default pagination template.
	 * @return null|string
	 */
	public static function getPaginationTemplate(): ?string
	{
		global $wp_query;

		return Template::render( 'parts.pagination', [ 'query' => $wp_query ], true );
	}

	/**
	 * @param array $args
	 * @return array
	 */
	protected static function handlePolylangArgs( array $args ): array
	{
		return PolylangHelper::parseQueryArgs( $args, static::getPostType() );
	}

	/**
	 * @param PostCollection $posts
	 * @return array
	 * @throws Exception
	 */
	protected static function getFilterCounts( PostCollection $posts ): array
	{
		$filter_counts = [];

		/** @var BasePost $post */
		foreach ( $posts->all() as $post ) {
			$added_terms = [];

			foreach ( get_object_taxonomies( $post->getPost() ) as $taxonomy ) {
				/** @var Taxonomy $term */
				foreach ( $post->getTerms( $taxonomy ) as $term ) {
					if ( in_array( $term->slug, $added_terms ) ) {
						continue;
					}

					$filter_counts[ $term->slug ] = ( $filter_counts[ $term->slug ] ?? 0 ) + 1;
					$added_terms[]                = $term->slug;

					// Also add counts for parents.
					$parent = $term->parent();
					while ( $parent ) {
						if ( in_array( $parent->slug, $added_terms ) ) {
							break;
						}

						$filter_counts[ $parent->slug ] = ( $filter_counts[ $parent->slug ] ?? 0 ) + 1;
						$parent                         = $parent->parent();
					}
				}
			}
		}

		return $filter_counts;
	}

	/**
	 * Handles API request.
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 * @throws Exception
	 */
	public static function index( WP_REST_Request $request ): WP_REST_Response
	{
		$paged       = $request['paged'] ?? 1;
		$post_type   = static::getPostType();
		$archive_url = get_post_type_archive_link( $post_type );
		$api_url     = get_rest_url( get_current_blog_id(), 'surf/v1/' ) . $post_type . '/';

		$args = [
			'api_index' => $post_type,
			'post_type' => $post_type === 'search' ? 'any' : $post_type,
			'paged'     => $paged,
		];

		$args = static::handlePolylangArgs( $args );

		$query               = new WP_Query( $args );
		$GLOBALS['wp_query'] = $query;
		if ( $query->have_posts() ) {
			$collection = PostCollection::fromQuery( $query );

			$html          = (string) static::renderPosts( $collection );
			$pagination    = (string) static::getPaginationTemplate();
			$filter_counts = static::getFilterCounts( $collection );
		} else {
			$html          = (string) static::renderNothingFound();
			$filter_counts = [];
		}

		return new WP_REST_Response(
			[
				'last_page'     => ( $paged * static::getPostsPerPage() ) >= $query->found_posts,
				'found_posts'   => $query->found_posts,
				'html'          => $html,
				'pagination'    => str_replace( $api_url, $archive_url, $pagination ?? '' ),
				'filter_counts' => $filter_counts,
			]
		);
	}

}
