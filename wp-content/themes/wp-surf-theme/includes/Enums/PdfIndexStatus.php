<?php

namespace SURF\Enums;

/**
 * Class PdfIndexStatus
 * @package SURF\Enums
 */
class PdfIndexStatus extends Enum
{

	public const INDEXED            = 'indexed';
	public const NO_FILE            = 'no-file';
	public const PDF_NOT_SUPPORTED  = 'pdf-not-supported';
	public const TYPE_NOT_SUPPORTED = 'type-not-supported';

	/**
	 * @param string $status
	 * @return string
	 */
	public static function message( string $status ): string
	{
		return match ( $status ) {
			PdfIndexStatus::INDEXED            => __( 'PDF has been indexed', 'wp-surf-theme' ),
			PdfIndexStatus::PDF_NOT_SUPPORTED  => __( 'PDF could not be indexed (secured PDFs are not supported)', 'wp-surf-theme' ),
			PdfIndexStatus::TYPE_NOT_SUPPORTED => __( 'Only PDFs can be indexed', 'wp-surf-theme' ),
			default                            => __( 'No file to index', 'wp-surf-theme' )
		};
	}

}
