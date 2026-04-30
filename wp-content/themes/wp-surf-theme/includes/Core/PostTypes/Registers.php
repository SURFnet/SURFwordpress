<?php

namespace SURF\Core\PostTypes;

use Closure;
use Illuminate\Support\Str;
use ReflectionClass;
use SURF\Core\Exceptions\MismatchingPostTypesException;
use SURF\Helpers\PolylangHelper;
use SURF\PostTypes\Page;
use WP_Post;

/**
 * Trait Registers
 * Provides methods to register custom post types in WordPress
 * @package SURF\Core\PostTypes
 */
trait Registers
{

	/**
	 * @return string
	 */
	abstract public static function getName(): string;

	/**
	 * @return void
	 */
	public static function registered(): void {}

	/**
	 * @return bool
	 */
	public static function registersArchivePage(): bool
	{
		return true;
	}

	/**
	 * @return void
	 * @throws MismatchingPostTypesException
	 */
	public static function register(): void
	{
		$page = Page::find( get_option( static::getArchivePageOption() ) );

		$args = [
			'labels'            => static::getLabels(),
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_in_admin_bar' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
			'rewrite'           => [ 'slug' => $page ? $page->post_name : static::getSlug() ],
			'has_archive'       => $page ? $page->post_name : static::getSlug(),
			'hierarchical'      => false,
			'menu_icon'         => 'dashicons-admin-post',
			'taxonomies'        => [],
			'supports'          => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt' ],
		];

		register_post_type(
			static::getName(),
			array_merge( $args, static::getArgs() )
		);

		static::registerRewriteRules();
		static::registerHooks();
		static::registered();
	}

	/**
	 * @return void
	 * @throws MismatchingPostTypesException
	 */
	public static function registerRewriteRules(): void
	{
		foreach ( PolylangHelper::getLanguages() as $lang ) {
			$postType = static::getName();
			$page     = Page::findForLanguage( get_option( static::getArchivePageOption() ), $lang );

			if ( !$page ) {
				continue;
			}

			$url = ltrim( parse_url( $page->permalink(), PHP_URL_PATH ), '/' );
			add_rewrite_rule( "{$url}?$", "index.php?post_type={$postType}&lang={$lang}", 'top' );
			add_rewrite_rule(
				"{$url}page/([0-9]+)/?$",
				"index.php?post_type={$postType}&lang={$lang}&paged=\$matches[1]",
				'top'
			);
			add_rewrite_rule(
				"{$url}([a-z0-9-]+)/?$",
				"index.php?post_type={$postType}&lang={$lang}&name=\$matches[1]",
				'top'
			);
		}
	}

	/**
	 * @return void
	 * @throws MismatchingPostTypesException
	 */
	public static function registerHooks(): void
	{
		if ( !static::registersArchivePage() ) {
			return;
		}

		add_action( 'admin_init', function ()
		{
			register_setting( 'reading', static::getArchivePageOption() );
			add_settings_field(
				static::getArchivePageOption(),
				static::getPluralLabel() . ' ' . __( 'Page', 'wp-surf-theme' ),
				static::getPageSelectCallback( static::getArchivePageOption() ),
				'reading',
				'surf_custom_post_types'
			);
		} );

		add_filter( 'display_post_states', function ( array $states, WP_Post $post )
		{
			if ( (int) get_option( static::getArchivePageOption() ) === $post->ID ) {
				$states[ static::getArchivePageOption() ] = static::getPluralLabel() . ' ' . __( 'Page', 'wp-surf-theme' );
			}

			return $states;
		}, 10, 2 );

		add_filter( 'nav_menu_css_class', function ( array $classes, WP_Post $item )
		{
			$archive_id = intval( get_option( static::getArchivePageOption() ) );

			if (
				'post_type' === $item->type
				&& $archive_id === intval( $item->object_id )
				&& !in_array( 'current-menu-item', $classes )
				&& is_post_type_archive( static::getName() )
			) {
				$classes[] = 'current-menu-item';
			}

			return $classes;
		}, 10, 2 );

		add_filter( 'post_type_archive_link', function ( $link, string $postType )
		{
			if ( $postType !== static::getName() ) {
				return $link;
			}

			$page = static::getArchivePage();
			if ( !$page ) {
				return $link;
			}

			return $page->permalink();
		}, 10, 2 );

		add_filter( 'wpseo_title', function ( $title )
		{
			if ( !is_post_type_archive( static::getName() ) ) {
				return $title;
			}

			$page = static::getArchivePage();
			if ( !$page ) {
				return $title;
			}

			$sep      = YoastSEO()->helpers->options->get_title_separator();
			$parts    = explode( $sep, $title );
			$parts[0] = $page->title() . ' ';

			return implode( $sep, $parts );
		} );

		add_filter( 'document_title_parts', function ( $title )
		{
			if ( !is_post_type_archive( static::getName() ) ) {
				return $title;
			}

			$page = static::getArchivePage();
			if ( !$page ) {
				return $title;
			}

			$title['title'] = $page->title();

			return $title;
		} );

		add_filter( 'post_type_link', function ( $link, WP_Post $post )
		{
			if ( $post->post_type !== static::getName() ) {
				return $link;
			}
			if ( !function_exists( 'pll_get_post_language' ) ) {
				return $link;
			}

			$lang = pll_get_post_language( $post->ID );
			if ( $lang === pll_default_language() ) {
				return $link;
			}

			$page = static::getArchivePage( $lang );
			if ( !$page ) {
				return $link;
			}

			return $page->permalink() . $post->post_name;
		}, 10, 2 );

		add_filter( 'nav_menu_css_class', function ( array $classes, WP_Post $item )
		{
			if ( $item->object !== 'page' || get_post()?->post_type !== static::getName() ) {
				return $classes;
			}

			$page = static::getArchivePage();
			if ( !$page ) {
				return $classes;
			}

			if ( intval( $item->object_id ) !== $page->ID() ) {
				return $classes;
			}

			if ( in_array( 'current-menu-item', $classes ) ) {
				return $classes;
			}

			$classes[] = 'current-menu-item';

			return $classes;
		}, 10, 2 );

		add_filter( 'pll_translation_url', function ( $url, $lang )
		{
			if ( !is_post_type_archive( static::getName() ) ) {
				return $url;
			}

			return static::getArchivePage( $lang )?->permalink() ?? $url;
		}, 10, 2 );
	}

	/**
	 * @return string
	 */
	public static function getArchivePageOption(): string
	{
		return 'page_for_' . static::getName();
	}

	/**
	 * @return array
	 */
	public static function getArgs(): array
	{
		return [];
	}

	/**
	 * @return string
	 */
	public static function getSingularLabel(): string
	{
		return static::getClassName();
	}

	/**
	 * @return string
	 */
	public static function getPluralLabel(): string
	{
		return Str::plural( static::getClassName() );
	}

	/**
	 * @return string
	 */
	public static function getSlug(): string
	{
		return Str::slug( static::getClassName() );
	}

	/**
	 * @return string
	 */
	public static function getClassName(): string
	{
		return ( new ReflectionClass( static::class ) )->getShortName();
	}

	/**
	 * @return array
	 */
	public static function getLabels(): array
	{
		$singular = static::getSingularLabel();
		$plural   = static::getPluralLabel();

		return [
			'name'               => sprintf( _x( '%s', 'admin: post type general name', 'wp-surf-theme' ), $plural ),
			'singular_name'      => sprintf( _x( '%s', 'admin: post type singular name', 'wp-surf-theme' ), $singular ),
			'menu_name'          => sprintf( _x( '%s', 'admin: admin menu', 'wp-surf-theme' ), $plural ),
			'name_admin_bar'     => sprintf( _x( '%s', 'admin: add new on admin bar', 'wp-surf-theme' ), $singular ),
			'add_new'            => sprintf( _x( 'Add %s', 'admin', 'wp-surf-theme' ), $singular ),
			'add_new_item'       => sprintf( _x( 'Add New %s', 'admin', 'wp-surf-theme' ), $singular ),
			'new_item'           => sprintf( _x( 'New %s', 'admin', 'wp-surf-theme' ), $singular ),
			'edit_item'          => sprintf( _x( 'Edit %s', 'admin', 'wp-surf-theme' ), $singular ),
			'view_item'          => sprintf( _x( 'View %s', 'admin', 'wp-surf-theme' ), $singular ),
			'view_items'         => sprintf( _x( 'View %s', 'admin', 'wp-surf-theme' ), $plural ),
			'all_items'          => sprintf( _x( 'All %s', 'admin', 'wp-surf-theme' ), $plural ),
			'search_items'       => sprintf( _x( 'Search %s', 'admin', 'wp-surf-theme' ), $plural ),
			'parent_item_colon'  => sprintf( _x( 'Parent %s:', 'admin', 'wp-surf-theme' ), $plural ),
			'not_found'          => sprintf( _x( 'No %s found.', 'admin', 'wp-surf-theme' ), $plural ),
			'not_found_in_trash' => sprintf( _x( 'No %s found in Trash.', 'admin', 'wp-surf-theme' ), $plural ),
		];
	}

	/**
	 * @param string $option
	 * @return Closure
	 */
	public static function getPageSelectCallback( string $option ): Closure
	{
		return function () use ( $option )
		{
			echo wp_dropdown_pages(
				[
					'name'              => $option,
					'echo'              => 0,
					'show_option_none'  => _x( '&mdash; Select &mdash;', 'admin', 'wp-surf-theme' ),
					'option_none_value' => '0',
					'selected'          => get_option( $option ),
				]
			);
		};
	}

	/**
	 * @param string|null $lang
	 * @return Page|null
	 * @throws MismatchingPostTypesException
	 */
	public static function getArchivePage( string $lang = null ): ?Page
	{
		$id = get_option( static::getArchivePageOption() );
		if ( $id === '0' ) {
			return null;
		}

		return Page::findForLanguage( $id, $lang );
	}

	/**
	 * @return bool
	 */
	public static function isEnabled(): bool
	{
		return boolval( get_option( static::getName() . '_is_enabled', true ) );
	}

	/**
	 * @param bool $enable
	 * @return void
	 */
	public static function enable( bool $enable = true ): void
	{
		update_option( static::getName() . '_is_enabled', $enable ? 1 : 0 );
	}

	/**
	 * @return void
	 */
	public static function disable(): void
	{
		static::enable( false );
	}

}
