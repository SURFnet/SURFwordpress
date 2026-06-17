<?php

namespace SURF\Traits;

use SURF\Core\Exceptions\MismatchingPostTypesException;
use SURF\Enums\PdfIndexStatus;
use SURF\PostTypes\Attachment;
use SURF\Services\PdfIndexService;

/**
 * Trait HasIndexedPdf
 * @package SURF\Traits
 */
trait HasIndexedPdf
{

	public const FIELD_FILE_ID      = 'file_id';
	public const FIELD_INDEX_STATUS = 'index_status';

	/**
	 * @param string $identifier
	 * @return void
	 */
	public static function registerPdfIndexHooks( string $identifier = 'surf' ): void
	{
		add_filter( 'acf/load_field/key=field_' . $identifier . '_' . static::FIELD_INDEX_STATUS,
			[ static::class, 'loadPdfIndexMessage' ] );
	}

	/**
	 * @param array $field
	 * @return array
	 */
	public static function loadPdfIndexMessage( array $field ): array
	{
		$post_id          = (int) get_the_ID();
		$field['message'] = static::getPdfIndexStatusMessageByID( $post_id );

		return $field;
	}

	/**
	 * @param int $post_id
	 * @return string
	 */
	public static function getPdfIndexStatusByID( int $post_id )
	{
		if ( empty( $post_id ) ) {
			return '';
		}

		return (string) ( get_post_meta( $post_id, PdfIndexService::META_INDEX_STATUS, true ) );
	}

	/**
	 * @param int $post_id
	 * @return string
	 */
	public static function getPdfIndexStatusMessageByID( int $post_id ): string
	{
		if ( empty( $post_id ) ) {
			return '';
		}

		$status = static::getPdfIndexStatusByID( $post_id );

		return !empty( $status ) ? PdfIndexStatus::message( $status ) : '';
	}

	/**
	 * @return null|int
	 */
	public function getFileID(): ?int
	{
		$file_id = (int) $this->getMeta( static::FIELD_FILE_ID );

		return !empty( $file_id ) ? $file_id : null;
	}

	/**
	 * @return Attachment|null
	 * @throws MismatchingPostTypesException
	 */
	public function getFile(): ?Attachment
	{
		$file_id = $this->getFileID();

		return $file_id ? Attachment::find( $file_id ) : null;
	}

	/**
	 * @param string $identifier
	 * @return array
	 */
	public static function getFileIDField( string $identifier = 'surf' ): array
	{
		return [
			'key'     => 'field_' . $identifier . '_' . static::FIELD_FILE_ID,
			'label'   => _x( 'File', 'admin', 'wp-surf-theme' ),
			'name'    => static::FIELD_FILE_ID,
			'type'    => 'file',
			'wrapper' => [ 'width' => 50 ],
		];
	}

	/**
	 * @param string $identifier
	 * @return array
	 */
	public static function getFileIndexStatusField( string $identifier = 'surf' ): array
	{
		return [
			'key'     => 'field_' . $identifier . '_' . static::FIELD_INDEX_STATUS,
			'label'   => _x( 'Index status', 'admin', 'wp-surf-theme' ),
			'name'    => static::FIELD_INDEX_STATUS,
			'type'    => 'message',
			'message' => '',
			'wrapper' => [ 'width' => 50 ],
		];
	}

}
