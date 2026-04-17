<?php

namespace SURF\PostTypes;

use SURF\Core\PostTypes\BasePost;

/**
 * Class Attachment
 * @package SURF\PostTypes
 */
class Attachment extends BasePost
{

	protected static string $postType = 'attachment';

	/**
	 * @return string
	 */
	public function path(): string
	{
		$relative = $this->getMeta( '_wp_attached_file' );
		$dir_list = wp_upload_dir();

		return $dir_list['basedir'] . DIRECTORY_SEPARATOR . $relative;
	}

	/**
	 * @return string
	 */
	public function url(): string
	{
		return wp_get_attachment_url( $this->ID() );
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return basename( $this->path() );
	}

	/**
	 * @return int
	 */
	public function size(): int
	{
		$path = $this->path();
		if ( !file_exists( $path ) ) {
			return 0;
		}

		$size = filesize( $path );

		return $size === false ? 0 : (int) $size;
	}

	/**
	 * @return string
	 */
	public function extension(): string
	{
		$path = $this->path();
		if ( !file_exists( $path ) ) {
			return '';
		}

		return pathinfo( $path )['extension'] ?? '';
	}

	/**
	 * @return bool
	 */
	public function isPDF(): bool
	{
		return strtolower( $this->extension() ) === 'pdf';
	}

}
