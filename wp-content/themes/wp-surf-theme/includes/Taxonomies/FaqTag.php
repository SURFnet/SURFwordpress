<?php

namespace SURF\Taxonomies;

use SURF\Core\Taxonomies\Registers;
use SURF\Core\Taxonomies\Taxonomy;
use SURF\PostTypes\Faq;

/**
 * Class FaqTag
 * @package SURF\Taxonomies
 */
class FaqTag extends Taxonomy
{

	use Registers;

	protected static string $taxonomy  = 'surf-faq-tag';
	protected static array  $postTypes = [ Faq::class ];

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return _x( 'Tag', 'label singular - faq-tag', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return _x( 'Tags', 'label plural - faq-tag', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return _x( 'faq-tag', 'tax slug - faq-tag', 'wp-surf-theme' );
	}

}
