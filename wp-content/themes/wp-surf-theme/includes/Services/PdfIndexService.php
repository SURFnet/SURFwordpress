<?php

namespace SURF\Services;

use Exception;
use Smalot\PdfParser\Parser;
use SURF\Core\Exceptions\MismatchingPostTypesException;
use SURF\Enums\PdfIndexStatus;
use SURF\Pdf\PdfParser;
use SURF\PostTypes\Asset;
use SURF\PostTypes\Attachment;
use SURF\PostTypes\Download;
use WP_Query;

/**
 * Class PdfIndexService
 * @package SURF\Services
 */
class PdfIndexService
{

	public const META_INDEX        = 'pdf_index';
	public const META_INDEX_STATUS = 'pdf_index_status';

	/**
	 * @param Parser $parser
	 * @param PdfParser $backupParser
	 */
	public function __construct(
		protected Parser    $parser,
		protected PdfParser $backupParser
	) {}

	/**
	 * @return void
	 */
	public function registerHooks(): void
	{
		add_action( 'posts_join', [ $this, 'postsJoin' ], 5, 2 );
		add_action( 'posts_search', [ $this, 'postsSearch' ], 5, 2 );

		add_filter( 'acf/update_value/name=field_asset_' . Asset::FIELD_FILE_ID, [ $this, 'updateValue' ], 10, 3 );
		add_filter( 'acf/update_value/name=field_download_' . Download::FIELD_FILE_ID, [ $this, 'updateValue' ], 10, 3 );
	}

	/**
	 * @param int $postId
	 * @param Attachment|null $attachment
	 * @return void
	 */
	public function index( int $postId, Attachment $attachment = null ): void
	{
		if ( !$attachment ) {
			update_post_meta( $postId, static::META_INDEX_STATUS, PdfIndexStatus::NO_FILE );
			update_post_meta( $postId, static::META_INDEX, '' );

			return;
		}

		if ( !$attachment->isPDF() ) {
			update_post_meta( $postId, static::META_INDEX_STATUS, PdfIndexStatus::TYPE_NOT_SUPPORTED );
			update_post_meta( $postId, static::META_INDEX, '' );

			return;
		}

		$output = '';

		try {
			$pdf    = $this->parser->parseFile( $attachment->path() );
			$output = utf8_encode( $pdf->getText() );
		} catch ( Exception $e ) {
			// Do nothing
		}

		if ( empty( $output ) ) {
			$this->backupParser->setFilename( $attachment->path() );
			$this->backupParser->decodePDF();
			$output = $this->backupParser->output();
		}

		if ( empty( $output ) ) {
			update_post_meta( $postId, static::META_INDEX_STATUS, PdfIndexStatus::PDF_NOT_SUPPORTED );
			update_post_meta( $postId, static::META_INDEX, '' );

			return;
		}

		update_post_meta( $postId, static::META_INDEX_STATUS, PdfIndexStatus::INDEXED );
		update_post_meta( $postId, static::META_INDEX, $output );
	}

	/**
	 * @param mixed $value
	 * @param int $postId
	 * @param array $field
	 * @return mixed
	 * @throws MismatchingPostTypesException
	 */
	public function updateValue( mixed $value, int $postId, array $field )
	{
		$this->index( $postId, Attachment::find( $value ) );
		$status = get_post_meta( $postId, static::META_INDEX_STATUS, true );

		setcookie(
			'pdf_index_notice',
			json_encode( [
				'message' => PdfIndexStatus::message( $status ),
				'status'  => $status,
			] ),
			[ 'secure' => true, 'SameSite' => 'Strict' ]
		);

		return $value;
	}

	/**
	 * @param string $join
	 * @param WP_Query $query
	 * @return string
	 */
	public function postsJoin( string $join, WP_Query $query ): string
	{
		if ( !$query->is_search() ) {
			return $join;
		}

		global $wpdb;
		$join .= " LEFT JOIN {$wpdb->postmeta} AS pim ON ({$wpdb->posts}.ID = pim.post_id AND pim.meta_key = '" . static::META_INDEX . "')";

		return $join;
	}

	/**
	 * @param string $search
	 * @param WP_Query $query
	 * @return string
	 */
	public function postsSearch( string $search, WP_Query $query ): string
	{
		if ( !$search || !$query->is_search() ) {
			return $search;
		}

		global $wpdb;

		$result = ' AND (';
		foreach ( explode( ' ', $query->get( 's' ) ) as $index => $word ) {
			if ( $index > 0 ) {
				$result .= ' AND ';
			}
			$result .= $wpdb->prepare(
				"(
                {$wpdb->posts}.post_title LIKE '%s' OR
                {$wpdb->posts}.post_excerpt LIKE '%s' OR
                {$wpdb->posts}.post_content LIKE '%s' OR
                pim.meta_value LIKE '%s'
            )",
				'%' . $word . '%',
				'%' . $word . '%',
				'%' . $word . '%',
				'%' . $word . '%'
			);
		}
		$result .= ')';

		return $result;
	}

}
