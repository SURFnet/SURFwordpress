<?php

namespace SURF\Helpers;

use Exception;

/**
 * Class Helper
 * @package SURF\Helpers
 */
class Helper
{

	/**
	 * Safely gets a value by its key from the $_GET global
	 * @param string|int $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getGet( string|int $key, mixed $default = null ): mixed
	{
		return static::getItem( $_GET, $key, $default );
	}

	/**
	 * Safely gets a sanitized value by its key from the $_GET global
	 * @param string|int $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getSanitizedGet( string|int $key, mixed $default = null ): mixed
	{
		return static::getSanitizedItem( $_GET, $key, $default );
	}

	/**
	 * Safely gets a value by its key from the $_POST global
	 * @param string|int $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getPost( string|int $key, mixed $default = null ): mixed
	{
		return static::getItem( $_POST, $key, $default );
	}

	/**
	 * Safely gets a sanitized value by its key from the $_POST global
	 * @param string|int $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getSanitizedPost( string|int $key, mixed $default = null ): mixed
	{
		return static::getSanitizedItem( $_POST, $key, $default );
	}

	/**
	 * Safely gets a value by its key from the $_REQUEST global
	 * @param string|int $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getRequest( string|int $key, mixed $default = null ): mixed
	{
		return static::getItem( $_REQUEST, $key, $default );
	}

	/**
	 * Safely gets a sanitized value by its key from the $_REQUEST global
	 * @param string|int $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getSanitizedRequest( string|int $key, mixed $default = null ): mixed
	{
		return static::getSanitizedItem( $_REQUEST, $key, $default );
	}

	/**
	 * Safely gets a value by its key from an array
	 * @param array $array
	 * @param string|int $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getItem( array $array, string|int $key, mixed $default = null ): mixed
	{
		if ( empty( $array[ $key ] ) ) {
			return $default;
		}

		return $array[ $key ];
	}

	/**
	 * Safely gets a sanitized value by its key from an array
	 * @param array $array
	 * @param string|int $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function getSanitizedItem( array $array, string|int $key, mixed $default = null ): mixed
	{
		$value = static::getItem( $array, $key, $default );
		if ( is_string( $value ) ) {
			return sanitize_text_field( $value );
		}

		if ( is_numeric( $value ) ) {
			return (int) $value;
		}

		// For other types (arrays, objects, etc.), we return as is - for now...
		return $value;
	}

	/**
	 * Tries to get array values from either an actual array or a comma-separated string
	 * @param $item
	 * @return array
	 */
	public static function maybeGetArrayValues( $item )
	{
		if ( is_array( $item ) ) {
			return $item;
		}

		if ( is_string( $item ) ) {
			if ( str_contains( urldecode( $item ), ',' ) ) {
				return explode( ',', urldecode( $item ) );
			}

			return [ $item ];
		}

		return [];
	}

	/**
	 * @param string $data
	 * @return bool
	 */
	public static function isBase64( string $data ): bool
	{
		return (bool) preg_match( '/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $data );
	}

	/**
	 * @return string
	 */
	public static function getCurrentUrl(): string
	{
		$request_uri = $_SERVER['REQUEST_URI'] ?? '/';

		return home_url( $request_uri );
	}

	/**
	 * @param string $url
	 * @param string $text
	 * @param array|null $attr
	 * @return string
	 */
	public static function buildLink( string $url, string $text = '', ?array $attr = [] ): string
	{
		if ( empty( $text ) ) {
			return '';
		}

		$empty_url = empty( $url ) || $url === 'mailto:' || $url === 'tel:';
		if ( $empty_url ) {
			return $text;
		}

		// check if url is just a post id
		if ( is_numeric( $url ) ) {
			$url = get_the_permalink( $url );
			if ( empty( $url ) ) {
				return '';
			}
		}

		$url = str_replace( ' ', '', $url );
		if ( empty( $url ) ) {
			return '';
		}

		$html = '<a href="' . esc_url( (string) $url ) . '"';

		// add attributes
		if ( is_array( $attr ) ) {
			$html .= ' ' . static::buildAttributes( $attr );
		}

		return $html . '>' . $text . '</a>';
	}

	/**
	 * @param array $attr
	 * @return string
	 */
	public static function buildAttributes( array $attr ): string
	{
		$string = '';
		foreach ( $attr as $key => $value ) {
			// maybe object
			if ( is_object( $value ) ) {
				$value = json_encode( $value );
			}

			if ( $value === null || $value === false ) {
				continue;
			}

			// boolean attributes: required, disabled, readonly, etc.
			if ( $value === true ) {
				$string .= esc_attr( $key ) . ' ';
				continue;
			}

			// empty string? skip
			if ( $value === '' ) {
				continue;
			}

			// maybe array
			if ( is_array( $value ) ) {
				$value = implode( ' ', $value );
			}

			$string .= $key . '="' . esc_attr( $value ) . '" ';
		}

		return trim( $string );
	}

	/**
	 * Write data to a file using WP Filesystem API
	 * @param string $filename Full path to file
	 * @param string $data     Data to write
	 * @return void
	 * @throws Exception
	 */
	public static function putContents( string $filename, string $data ): void
	{
		if ( !function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();
		global $wp_filesystem;
		if ( !$wp_filesystem->put_contents( $filename, $data, FS_CHMOD_FILE ) ) {
			throw new Exception( sprintf( 'Could not write file: %s', $filename ) );
		}
	}

	/**
	 * Download a remote file and save it using WP Filesystem
	 * @param string $url      Remote file URL
	 * @param string $filename Local file path
	 * @param int $timeout     Timeout in seconds
	 * @return void
	 * @throws Exception
	 */
	public static function downloadFileToServer( string $url, string $filename, int $timeout = 60 ): void
	{
		$response = wp_safe_remote_get( $url, [ 'timeout' => $timeout ] );
		if ( is_wp_error( $response ) ) {
			throw new Exception( sprintf( 'Could not fetch URL: %s', $url ) );
		}

		$body = wp_remote_retrieve_body( $response );
		static::putContents( $filename, $body );
	}

	/**
	 * Convert basic GitHub-flavored Markdown to HTML
	 * Supports:
	 * - Headings (#, ##, ###)
	 * - Unordered lists (-, *)
	 * - Bold (**text**)
	 * - Inline code (`code`)
	 * - Markdown links [text](url)
	 * - Bare URLs
	 * @param string $markdown
	 * @return string
	 */
	public static function formatMarkDown( string $markdown ): string
	{
		$html = $markdown;

		// Normalize line endings
		$html = str_replace( [ "\r\n", "\r" ], "\n", $html );

		// Headings
		$html = preg_replace( '/^###\s+(.*)$/m', '<h4>$1</h4>', $html );
		$html = preg_replace( '/^##\s+(.*)$/m', '<h3>$1</h3>', $html );
		$html = preg_replace( '/^#\s+(.*)$/m', '<h2>$1</h2>', $html );

		// Unordered list items
		$html = preg_replace( '/^[-*]\s+(.*)$/m', '<li>$1</li>', $html );

		// Wrap consecutive <li> in <ul>
		$html = preg_replace( '/(<li>.*<\/li>)/s', '<ul>$1</ul>', $html );
		$html = preg_replace( '/<\/ul>\s*<ul>/', '', $html );

		// Bold
		$html = preg_replace( '/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html );

		// Inline code
		$html = preg_replace( '/`(.*?)`/', '<code>$1</code>', $html );

		// Markdown links [text](url)
		$html = preg_replace(
			'/\[([^\]]+)\]\((https?:\/\/[^\)]+)\)/',
			'<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>',
			$html
		);

		// Bare URLs (avoid double-wrapping existing links)
		$html = preg_replace(
			'/(?<!href=")(https?:\/\/[^\s<]+)/',
			'<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
			$html
		);

		// Paragraphs
		return wpautop( $html );
	}

}
