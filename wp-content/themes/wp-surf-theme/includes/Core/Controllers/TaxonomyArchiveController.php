<?php

namespace SURF\Core\Controllers;

use SURF\Core\PostTypes\PostCollection;
use SURF\Core\View\Template;
use SURF\Helpers\Helper;
use SURF\Helpers\PolylangHelper;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use WP_Taxonomy;

use function Symfony\Component\String\s;

/**
 * Class TaxonomyArchiveController
 * @package SURF\Core\Controllers
 */
trait TaxonomyArchiveController
{

	/** Post Type: Set on init based on file. */
	protected static string $taxonomy;

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
	public static function getTaxonomy(): string
	{
		return static::$taxonomy;
	}

	/**
	 * Sets up archive page and Traits.
	 * @param string $taxonomy
	 * @return void
	 */
	public static function init( string $taxonomy ): void
	{
		static::$taxonomy = $taxonomy;

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
		if ( $output === 'rest_base' ) {
			$taxonomies = array_flip(
				array_filter(
					array_map(
						fn( WP_Taxonomy $taxonomy ) => $taxonomy->rest_base,
						get_object_taxonomies( static::getTaxonomy(), 'objects' )
					)
				)
			);
		} else {
			$taxonomies = get_object_taxonomies( static::getTaxonomy(), $output );
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
		if ( $query->get( 'api_index' ) === static::getTaxonomy() ) {
			return static::query( $query );
		}

		if ( is_admin() || !$query->is_main_query() || !is_tax( static::getTaxonomy() ) ) {
			return $query;
		}

		return static::query( $query );
	}

	/**
	 * Add everything in the $_REQUEST to the query.
	 * @param WP_Query $query
	 * @return WP_Query
	 */
	public static function query( WP_Query $query ): WP_Query
	{
		$query->set( 'posts_per_page', static::getPostsPerPage() );
		if ( isset( $_REQUEST['search'] ) ) {
			$query->set( 'is_filtering', 1 );
			$query->set( 's', Helper::getSanitizedRequest( 'search', '' ) );
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
	 * Creates the Taxonomy query based on the $_REQUEST.
	 * @return null|array
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
			if ( !empty( $value ) ) {
				$value_type = $value;
				if ( is_array( $value_type ) ) {
					$value_type = current( $value );
				}
				$tax_query[] = [
					'taxonomy' => $name,
					'terms'    => $value,
					'field'    => is_numeric( $value_type ) ? 'term_id' : 'slug',
				];
				unset( $value );
			}
		}

		return $tax_query ?: null;
	}

	/**
	 * Creates the Meta query based on the $_REQUEST.
	 * @return null|array
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
	 * @return string
	 */
	public static function renderIntro(): string
	{
		return '';
	}

	/**
	 * Initializes API endpoint.
	 * @return void
	 */
	public static function initRest(): void
	{
		add_action( 'rest_api_init', function ()
		{
			register_rest_route( 'surf/v1', '/' . static::getTaxonomy(), [
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
		return PolylangHelper::parseQueryArgs( $args, static::getTaxonomy() );
	}

	/**
	 * Handles API request.
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public static function index( WP_REST_Request $request ): WP_REST_Response
	{
		$paged       = $request['paged'] ?? 1;
		$taxonomy    = static::getTaxonomy();
		$term        = get_term_by( 'slug', $request->get_param( 'term' ), static::getTaxonomy() );
		$archive_url = get_term_link( $term->term_id );
		$api_url     = get_rest_url( get_current_blog_id(), 'surf/v1/' ) . $taxonomy . '/';

		$args = [
			'api_index' => $taxonomy,
			'taxonomy'  => $taxonomy,
			'tax_query' => [
				[
					'taxonomy' => $taxonomy,
					'terms'    => [ $term->term_id ],
				],
			],
			'paged'     => $paged,
		];

		$args = static::handlePolylangArgs( $args );

		$query               = new WP_Query( $args );
		$GLOBALS['wp_query'] = $query;
		if ( $query->have_posts() ) {
			$html       = (string) static::renderPosts( PostCollection::fromQuery( $query ) );
			$pagination = (string) static::getPaginationTemplate();

		} else {
			$html = (string) static::renderNothingFound();
		}

		return new WP_REST_Response(
			[
				'last_page'   => ( $paged * static::getPostsPerPage() ) >= $query->found_posts,
				'found_posts' => $query->found_posts,
				'intro'       => static::renderIntro(),
				'html'        => $html,
				'pagination'  => str_replace( $api_url, $archive_url, $pagination ?? '' ),
			]
		);
	}

}
