<?php

namespace SURF\Hooks;

use DOMDocument;
use DOMElement;

/**
 * Class HeadingHooks
 * @package SURF\Hooks
 */
class HeadingHooks
{

	public static array $headings = [
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
	];

	/**
	 * @return void
	 */
	public static function register(): void
	{
		if ( isset( $_GET['surf-no-head'] ) ) {
			return;
		}

		add_filter( 'the_content', [ static::class, 'addHeadingIcons' ] );
	}

	/**
	 * @param string $content
	 * @return string
	 */
	public static function addHeadingIcons( string $content ): string
	{
		if ( empty( $content ) ) {
			return $content;
		}

		// Load the content in an xhmtl parser
		// get all the different headings.
		// Plop the image in the heading tag before the text
		// profit.

		$content  = '<?xml encoding="UTF-8">' . $content;
		$document = new DOMDocument( '', 'UTF-8' );
		libxml_use_internal_errors( true );

		$document->loadHTML( $content, LIBXML_HTML_NODEFDTD );
		foreach ( static::$headings as $heading ) {
			$headings = $document->getElementsByTagName( $heading );

			foreach ( $headings as $h ) {
				$icon = static::getHeadingIconNode( $heading );

				if ( is_null( $icon ) ) {
					continue;
				}

				$h->insertBefore( $document->importNode( $icon, true ), $h->firstChild );
			}
		}

		$trim_off_front = strpos( $document->saveHTML(), '<body>' ) + 6;
		$trim_off_end   = ( strrpos( $document->saveHTML(), '</body>' ) ) - strlen( $document->saveHTML() );

		return substr( $document->saveHTML(), $trim_off_front, $trim_off_end );
	}

	/**
	 * @param string $heading
	 * @return DOMElement|null
	 */
	protected static function getHeadingIconNode( string $heading ): ?DOMElement
	{
		$image = surfGetHeadingIcon( $heading );
		if ( empty( $image ) ) {
			return null;
		}

		$document = new DOMDocument();
		$document->loadHTML( $image, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );

		return $document->getElementsByTagName( '*' )->item( 0 );
	}

}
