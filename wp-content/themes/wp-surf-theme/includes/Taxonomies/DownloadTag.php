<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\PostTypes\Download;

/**
 * Class DownloadTag
 * @package SURF\Taxonomies
 */
class DownloadTag extends Taxonomy
{

	use Registers;

	protected static string $taxonomy  = 'surf-download-tag';
	protected static array  $postTypes = [ Download::class ];

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Tag', 'label singular - download-tag', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Tags', 'label plural - download-tag', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'download-tag', 'tax slug - download-tag', 'wp-surf-theme' );
	}

}
