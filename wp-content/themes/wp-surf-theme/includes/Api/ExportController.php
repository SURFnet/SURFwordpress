<?php

namespace SURF\Api;

use Illuminate\Support\Collection;
use Mpdf\MpdfException;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use SURF\Core\PostTypes\PostCollection;
use SURF\Helpers\Helper;
use SURF\Helpers\PolylangHelper;
use SURF\Helpers\TermHelper;
use SURF\PostTypes\Faq;
use SURF\Services\ExportService;
use WP_Query;
use WP_REST_Request;

/**
 * Class ExportController
 * @package SURF\Api
 */
class ExportController
{

	public const ORDER_DEFAULT   = 'asc';
	public const ORDERBY_DEFAULT = 'date';

	/**
	 * @return void
	 */
	public function register(): void
	{
		add_action( 'rest_api_init', [ $this, 'registerRoutes' ] );
	}

	/**
	 * @return void
	 */
	public function registerRoutes(): void
	{
		register_rest_route( 'surf/v1', 'export/(?P<post_type>\D+)', [
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => [ $this, 'exportPostType' ],
			'args'                => [
				'output'   => [
					'required'          => false,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => fn( $value ) => in_array( $value, [ 'xlsx', 'pdf' ] ),
				],
				'taxonomy' => [
					'required'          => false,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => fn( $value ) => is_string( $value ) && !empty( $value ),
				],
				'category' => [
					'required'          => false,
					'sanitize_callback' => 'rest_sanitize_array',
					'validate_callback' => fn( $value ) => is_array( $value ) && !empty( $value ),
				],
				'search'   => [
					'required'          => false,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => fn( $value ) => is_string( $value ) && !empty( $value ),
				],
			],
		] );
	}

	/**
	 * @param WP_REST_Request $request
	 * @return void
	 * @throws Exception
	 * @throws MpdfException
	 */
	public function exportPostType( WP_REST_Request $request ): void
	{
		$postType    = $request->get_param( 'post_type' );
		$filterTerms = [];

		$args = [
			'post_type'      => $postType,
			'posts_per_page' => -1,
			'no_found_rows'  => true,
		];

		if ( $request->has_param( 'taxonomy' ) ) {
			$taxonomy  = $request->get_param( 'taxonomy' );
			$tax_query = [ 'relation' => 'AND' ];

			if ( $request->has_param( 'category' ) ) {
				$value = Helper::maybeGetArrayValues( $request->get_param( 'category' ) );
				if ( !empty( $value ) ) {
					$groupedValue = TermHelper::groupByParent( $taxonomy, $value );

					$valueType = is_array( $value ) ? current( $value ) : $value;

					foreach ( $groupedValue as $group ) {
						$tax_query[] = [
							'taxonomy' => $taxonomy,
							'terms'    => $group,
							'field'    => is_numeric( $valueType ) ? 'term_id' : 'slug',
							'operator' => 'IN',
						];
					}
				}
			}

			$args['tax_query'] = $tax_query;
		}

		if ( $request->has_param( 'search' ) ) {
			$args['s'] = $request->get_param( 'search' );
		}

		// Parse the args to be used for the query.
		$args = $this->parseArgs( $request, $postType, $args );

		// Execute the query and set up a post collection.
		$query = new WP_Query( $args );
		$posts = PostCollection::fromQuery( $query );

		// Parse the filters used on the archive page.
		$filters = $this->parseFilters( $request, $postType, $args );

		$output = $request->get_param( 'output' );
		match ( $output ) {
			'pdf'   => $this->exportToPdf( $posts, $postType, $filters, $filterTerms ),
			default => $this->exportToCsv( $posts, $postType, $filters, $filterTerms )
		};
	}

	/**
	 * @param WP_REST_Request $request
	 * @param string $postType
	 * @param array $args
	 * @return array
	 */
	protected function parseArgs( WP_REST_Request $request, string $postType, array $args ): array
	{
		$args = PolylangHelper::parseQueryArgs( $args, $postType );

		$orderby = $request->get_param( 'orderby' ) ?? static::ORDERBY_DEFAULT;
		$order   = $request->get_param( 'order' ) ?? static::ORDER_DEFAULT;

		if ( $postType === Faq::getName() ) {
			$parts = explode( '--', $orderby );

			if ( isset( $parts[0] ) ) {
				$orderby = $parts[0];
			}

			if ( isset( $parts[1] ) ) {
				$order = $parts[1];
			}

			if ( $orderby === 'id' ) {
				$orderby          = 'meta_value_num';
				$args['meta_key'] = 'ID';
			}
		}

		$args['orderby'] = $orderby;
		$args['order']   = $order;

		return $args;
	}

	/**
	 * @param WP_REST_Request $request
	 * @param string $postType
	 * @param array $filters
	 * @return array
	 */
	protected function parseFilters( WP_REST_Request $request, string $postType, array $filters ): array
	{
		$orderby = $request->get_param( 'orderby' ) ?? static::ORDERBY_DEFAULT;
		$order   = $request->get_param( 'order' ) ?? static::ORDER_DEFAULT;

		if ( $postType === Faq::getName() ) {
			$parts = explode( '--', $orderby );

			if ( isset( $parts[0] ) ) {
				$orderby = $parts[0];
			}

			if ( isset( $parts[1] ) ) {
				$order = $parts[1];
			}
		}

		$filters['orderby'] = $orderby;
		$filters['order']   = $order;

		return $filters;
	}

	/**
	 * @param Collection $posts
	 * @param string $postType
	 * @param array $args
	 * @param array $filterTerms
	 * @return void
	 * @throws Exception
	 */
	protected function exportToCsv( Collection $posts, string $postType, array $args = [], array $filterTerms = [] ): void
	{
		ExportService::make( $posts, $postType, $args )
		             ->setFilterTerms( $filterTerms )
		             ->toXlsx()
		             ->toBrowser();
	}

	/**
	 * @param Collection $posts
	 * @param string $postType
	 * @param array $args
	 * @param array $filterTerms
	 * @return void
	 * @throws MpdfException
	 * @throws Exception
	 */
	protected function exportToPdf( Collection $posts, string $postType, array $args = [], array $filterTerms = [] ): void
	{
		ExportService::make( $posts, $postType, $args )
		             ->setFilterTerms( $filterTerms )
		             ->toPdf()
		             ->toBrowser();
	}

}
