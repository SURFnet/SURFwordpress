<?php
/**
 * Template helper functions
 * Only put aliases to Helper class methods here
 * ----------------------------------------------------------------------------.
 */

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use JetBrains\PhpStorm\NoReturn;
use Psr\Container\ContainerInterface;
use SURF\Application;
use SURF\Core\Taxonomies\TermCollection;
use SURF\Core\Vite;
use SURF\Enums\Theme;
use SURF\Helpers\AttachmentHelper;
use SURF\Helpers\DebugHelper;
use SURF\Helpers\Helper;
use SURF\Helpers\PolylangHelper;
use SURF\Helpers\PostHelper;
use SURF\Hooks\HeadingHooks;

/**
 * @param int $excerpt_length
 * @param int $id
 * @param string $excerpt_more
 * @return string
 */
function surfGetMyExcerpt( int $excerpt_length = 55, int $id = 0, string $excerpt_more = ' [...]' ): string
{
	return PostHelper::getMyExcerpt( $excerpt_length, $id, $excerpt_more );
}

if ( !function_exists( 'dump' ) ) {
	/**
	 * Dump debug data
	 * @param $values
	 */
	function dump( ...$values )
	{
		DebugHelper::dump( ...$values );
	}

}

if ( !function_exists( 'dd' ) ) {
	/**
	 * Dump debug data and die
	 * @param $values
	 */
	#[NoReturn]
	function dd( ...$values )
	{
		DebugHelper::dump( ...$values );
		exit( 1 );
	}
}

/**
 * @param int $image_id
 * @param string $image_size
 * @return string
 */
function surfGetImageUrl( int $image_id = 0, string $image_size = 'large' ): string
{
	return AttachmentHelper::getImageUrl( $image_id, $image_size );
}

/**
 * Safely get a key from the $_GET global.
 * @param string|int $key
 * @param mixed $default_value
 * @return mixed
 */
function surfGetGet( $key, $default_value = null )
{
	return Helper::getSanitizedGet( $key, $default_value );
}

/**
 * Safely get a key from the $_POST global.
 * @param string|int $key
 * @param mixed $default_value
 * @return mixed
 */
function surfGetPost( $key, $default_value = null )
{
	return Helper::getSanitizedPost( $key, $default_value );
}

/**
 * Safely get a key from the $_REQUEST global.
 * @param string|int $key
 * @param mixed $default_value
 * @return mixed
 */
function surfGetRequest( $key, $default_value = null )
{
	return Helper::getSanitizedRequest( $key, $default_value );
}

/**
 * Safely get a key from an array.
 * @param array $array
 * @param string|int $key
 * @param mixed $default_value
 * @return mixed
 */
function surfGetItem( $array, $key, $default_value = null )
{
	return Helper::getSanitizedItem( $array, $key, $default_value );
}

/**
 * Render a view
 * @param       $view
 * @param array $args
 * @return View
 */
function surfView( $view, $args = [] ): View
{
	return surfApp( ViewFactory::class )->make( $view, $args );
}

/**
 * Check if a view exists
 * @param $view
 * @return bool
 * @throws BindingResolutionException
 */
function surfViewExists( $view ): bool
{
	return surfApp( ViewFactory::class )->exists( $view );
}

/**
 * Polylang helper to get languages
 * @return array
 */
function surfGetLanguages(): array
{
	return PolylangHelper::getLanguages();
}

/**
 * Gets a theme option based on the current language
 * @param string $optionKey
 * @return mixed
 */
function surfGetThemeOption( string $optionKey = '' ): mixed
{
	return Theme::getOption( $optionKey );
}

/**
 * Gets a global theme option
 * @param string $optionKey
 * @return mixed
 */
function surfGetGlobalThemeOption( string $optionKey = '' ): mixed
{
	return Theme::getGlobalOption( $optionKey );
}

/**
 * Gets a widget option based on the widget ID
 * @param string $optionKey
 * @param string $widgetID
 * @return mixed
 */
function surfGetWidgetOption( string $optionKey = '', string $widgetID = 'widget' ): mixed
{
	return get_option( 'widget_' . $widgetID . '_' . $optionKey );
}

/**
 * @param string|null $abstract
 * @param array $parameters
 * @return Closure|mixed|object|Application|null
 */
function surfApp( string $abstract = null, array $parameters = [] )
{
	if ( $abstract === null ) {
		return Application::getInstance();
	}

	return Application::getInstance()->make( $abstract, $parameters );
}

/**
 * @param string $key
 * @param null $default
 * @return mixed
 */
function surfConfig( string $key, $default = null ): mixed
{
	return surfApp( 'config' )->get( $key, $default );
}

/**
 * @param string $path
 * @return string
 */
function surfPath( string $path = '' ): string
{
	return surfApp()->path( $path );
}

/**
 * @param string $param
 * @return array
 */
function surfGetSelectedCheckboxValues( string $param ): array
{
	$selected = esc_attr( Helper::getGet( $param ) );

	return $selected ? explode( ',', $selected ) : [];
}

/**
 * @param int|null $postId
 * @param string|null $taxonomy
 * @return WP_Term|WP_Error|bool|array|null
 */
function surfGetPrimaryTerm( ?int $postId = 0, ?string $taxonomy = '' ): WP_Term|WP_Error|bool|array|null
{
	return PostHelper::getPrimaryTerm( $postId, $taxonomy );
}

/**
 * @param string $license
 * @return string
 */
function surfLicense( string $license ): string
{
	return base64_decode( $license );
}

/**
 * @return Vite
 */
function vite(): Vite
{
	return surfApp( Vite::class );
}

/**
 * @param array $array
 * @param int $a
 * @param int $b
 * @return array
 */
function surfMoveElement( array $array, int $a, int $b ): array
{
	$p1 = array_splice( $array, $a, 1 );
	$p2 = array_splice( $array, 0, $b );

	return array_merge( $p2, $p1, $array );
}

/**
 * @param int $bytes
 * @param int $dec
 * @return string
 */
function surfFileSize( int $bytes, int $dec = 2 ): string
{
	$size   = [ 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' ];
	$factor = floor( ( strlen( $bytes ) - 1 ) / 3 );

	return sprintf( "%.{$dec}f", $bytes / pow( 1024, $factor ) ) . $size[ $factor ];
}

/**
 * @param string $key
 * @param mixed $postId
 * @return mixed
 */
function surfGetMetaValue( string $key, mixed $postId = 0 ): mixed
{
	return PostHelper::getMetaValue( $key, $postId );
}

/**
 * @param TermCollection $terms
 * @param bool $as_links
 * @return array
 */
function surfFormatTermsWithParents( TermCollection $terms, bool $as_links = false ): array
{
	return PostHelper::formatTermsWithParents( $terms, $as_links );
}

/**
 * @param TermCollection $terms
 * @param bool $as_links
 * @return array
 */
function surfFormatTerms( TermCollection $terms, bool $as_links = false ): array
{
	return PostHelper::formatTerms( $terms, $as_links );
}

/**
 * @return bool
 */
function surfShowNewFooter(): bool
{
	return !empty( wp_get_sidebars_widgets()['footer'] ?? [] );
}

/**
 * @param $needle
 * @param $haystack
 * @return false|int|string
 */
function surfSearchArrayAndGetKey( $needle, $haystack )
{
	foreach ( $haystack as $key => $value ) {
		if ( is_array( $value ) && in_array( $needle, $value ) ) {
			return $key;
		}
	}

	return false;
}

/**
 * @param string $heading
 * @return string
 */
function surfGetHeadingIcon( string $heading ): string
{
	if ( !in_array( $heading, HeadingHooks::$headings ) ) {
		return '';
	}

	$icon = Theme::getGlobalOption( 'surf_theme_' . $heading );
	if ( empty( $icon ) ) {
		return '';
	}

	if ( is_numeric( $icon ) ) {
		$icon = [
			'id' => (int) $icon,
		];

		$url = surfGetImageUrl( $icon['id'] );
		if ( empty( $url ) ) {
			return '';
		}

		$meta = wp_get_attachment_metadata( $icon['id'] );
		if ( empty( $icon ) ) {
			return '';
		}

		$icon['url']    = $url;
		$icon['alt']    = (string) get_post_meta( $icon['id'], '_wp_attachment_image_alt', true );
		$icon['height'] = $meta['height'];
		$icon['width']  = $meta['width'];
	}

	// generate image tag
	return sprintf(
		'<img src="%s" alt="%s" height="%s" width="%s" loading="eager" class="heading-icon">',
		esc_url( $icon['url'] ?? '' ),
		esc_attr( $icon['alt'] ?? '' ),
		esc_attr( $icon['height'] ?? '' ),
		esc_attr( $icon['width'] ?? '' )
	);
}

/**
 * Returns a slug friendly string
 * @param string $string
 * @param string $glue
 * @return  string
 */
function surfSlugify( $string = '', $glue = '-' )
{
	$raw  = $string;
	$slug = strtolower( remove_accents( $raw ) );
	$slug = str_replace( [ '_', '-', '/', ' ' ], $glue, $slug );
	$slug = preg_replace( '/[^A-Za-z0-9' . preg_quote( $glue ) . ']/', '', $slug );

	return apply_filters( 'surf_slugify', $slug, $raw, $glue );
}
