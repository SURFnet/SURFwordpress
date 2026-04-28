<?php

namespace SURF\Hooks;

use DOMDocument;
use SURF\Helpers\ACFHelper;
use SURF\Helpers\Helper;
use WP_Error;
use SURF\Application;
use SURF\Enums\Theme;

/**
 * Starter theme configuration hooks
 * Define custom hooks in a separate class.
 * Class ConfigHooks
 * @package SURF\Hooks
 */
class ConfigHooks
{

	/**
	 * Register configuration hooks.
	 */
	public static function register(): void
	{
		static::registerActions();
		static::registerFilters();
		static::registerRestHooks();
		static::defaultPostTypeConfiguration();
	}

	/**
	 * @return void
	 */
	public static function defaultPostTypeConfiguration(): void
	{
		add_post_type_support( 'page', 'excerpt' );
	}

	/**
	 * add_action() or remove_action() calls.
	 * Please read the documentation at <https://developer.wordpress.org/reference/functions/add_action/>.
	 * ----------------------------------------------------------------------------
	 */
	public static function registerActions(): void
	{
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
		remove_action( 'template_redirect', 'rest_output_link_header', 11 );
		remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );

		add_action( 'surf_deploy', 'flush_rewrite_rules' );
		//add_action('init', [static::class, 'acfRedirect']);
		add_action( 'init', [ static::class, 'disableWpEmojicons' ] );
		add_action( 'admin_menu', [ static::class, 'changeAdminMenuOrder' ] );
		add_action( 'wp', [ static::class, 'generate404Status' ] );

		if ( !empty( $GLOBALS['sitepress'] ) ) {
			add_action( 'wp_head', [ static::class, 'removeWpmlGeneratorTag' ], 0 );
		}

		add_action( 'shutdown', function ()
		{
			$app = Application::getInstance();
			if ( method_exists( $app, 'runTerminatingCallbacks' ) ) {
				$app->runTerminatingCallbacks();
			}
		} );
	}

	/**
	 * add_filter() calls.
	 * Please read the documentation at <https://developer.wordpress.org/reference/functions/add_filter/>.
	 * ----------------------------------------------------------------------------
	 */
	public static function registerFilters(): void
	{
		add_filter( 'style_loader_src', [ static::class, 'removeWordpressVersionFromEnqueues' ], 9999 );
		add_filter( 'script_loader_src', [ static::class, 'removeWordpressVersionFromEnqueues' ], 9999 );
		add_filter( 'wpseo_metabox_prio', [ static::class, 'setWpseoMetaboxPrio' ] );

		add_filter( 'the_generator', '__return_empty_string' );
		add_filter( 'xmlrpc_enabled', '__return_false' );
		add_filter( 'xmlrpc_methods', fn() => [] );

		/* Control automatic WP Core updates */
		add_filter( 'allow_dev_auto_core_updates', '__return_false' );          // Disable development updates
		add_filter( 'allow_minor_auto_core_updates', '__return_true' );         // Enable minor updates
		add_filter( 'allow_major_auto_core_updates', '__return_false' );        // Disable major updates

		/* Disable automatic updates for non-core components */
		add_filter( 'auto_update_theme', '__return_false' );
		add_filter( 'auto_update_plugin', '__return_false' );

		// disabled for searching with quotes
//        add_filter('run_wptexturize', '__return_false', PHP_INT_MAX);
		remove_filter( 'the_title', 'wptexturize' );
		remove_filter( 'the_content', 'wptexturize' );

		/* Add custom block classes */
		add_filter( 'render_block', [ static::class, 'addCustomBlockClasses' ], 10, 3 );

		/* Comment form adjustments */
		add_filter( 'comment_form_defaults', [ static::class, 'changeCommentFields' ], 10, 2 );

		/* Admin body class */
		add_filter( 'admin_body_class', [ static::class, 'addThemeBodyClass' ] );

		add_filter( 'upload_mimes', [ static::class, 'uploadMimes' ], 99, 1 );
		add_filter( 'wp_check_filetype_and_ext', [ static::class, 'secureFontMimeCheck' ], 10, 4 );

		// Yoast
		add_filter( 'wpseo_breadcrumb_separator', [ static::class, 'voSeoBreadcrumbSeparator' ], 10, 1 );

		// Disables Gutenberg widgets
		add_filter( 'use_widgets_block_editor', '__return_false' );
	}

	/**
	 * Disable unauthenticated REST API access if 'SURF_ENABLE_JSON_API' is set to false.
	 */
	public static function registerRestHooks(): void
	{
		$json_enabled = ( !defined( 'SURF_ENABLE_JSON_API' ) || SURF_ENABLE_JSON_API === true );
		if ( !$json_enabled ) {
			add_filter( 'rest_authentication_errors', [ static::class, 'onlyAllowLoggedInRestAccess' ] );
			if ( has_filter( 'json_enabled' ) ) {
				add_filter( 'json_enabled', '__return_false' );
			}
			if ( has_filter( 'json_jsonp_enabled' ) ) {
				add_filter( 'json_jsonp_enabled', '__return_false' );
			}
			if ( has_filter( 'rest_enabled' ) ) {
				add_filter( 'rest_enabled', '__return_false' );
			}
			if ( has_filter( 'rest_jsonp_enabled' ) ) {
				add_filter( 'rest_jsonp_enabled', '__return_false' );
			}
		}

		add_filter( 'rest_endpoints', [ static::class, 'disableRestEndpoints' ] );
	}

	/**
	 * Redirect ACF pages to Admin dashboard
	 * @return void
	 */
	public static function acfRedirect(): void
	{
		if ( !is_admin() ) {
			return;
		}

		$post_type = Helper::getSanitizedGet( 'post_type', '' );
		if ( $post_type === 'acf-field-group' ) {

			// Allow updates page
			$page = Helper::getSanitizedGet( 'page', '' );
			if ( $page === 'acf-settings-updates' ) {
				return;
			}

			wp_redirect( admin_url() );
			die();
		}
	}

	/**
	 * Disable WordPress Emojicons.
	 */
	public static function disableWpEmojicons(): void
	{
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

		add_filter( 'tiny_mce_plugins', [ static::class, 'disableEmojiconsTinymce' ] );

		/* Disable WP_Rocket caching */
		add_filter( 'do_rocket_generate_caching_files', '__return_false' );
	}

	/**
	 * Disable WordPress Emojicons from TinyMCE.
	 * @param $plugins
	 * @return array
	 */
	public static function disableEmojiconsTinymce( $plugins ): array
	{
		return is_array( $plugins ) ? array_diff( $plugins, [ 'wpemoji' ] ) : [];
	}

	/**
	 * Remove WPML generator tag.
	 */
	public static function removeWpmlGeneratorTag(): void
	{
		remove_action( current_filter(), [ $GLOBALS['sitepress'], 'meta_generator_tag' ] );
	}

	/**
	 * Change admin menu order.
	 */
	public static function changeAdminMenuOrder(): void
	{
		global $menu;

		$newMenu = [];

		foreach ( $menu as $position => $data ) {
			// Array key '2' is the URL of the menu item
			switch ( $data[2] ) {
				case 'admin.php?page=savvii_dashboard':
					$newMenu[999] = $data;
					unset( $menu[ $position ] );
					break;
			}
		}

		$menu = $menu + $newMenu;
	}

	/**
	 * @param string $src
	 * @return string
	 */
	public static function removeWordpressVersionFromEnqueues( $src = '' ): string
	{
		if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}

	/**
	 * @return string
	 */
	public static function setWpseoMetaboxPrio(): string
	{
		return 'low';
	}

	/**
	 * Disable JSON API calls when not authenticated.
	 * @param $access
	 * @return WP_Error
	 */
	public static function onlyAllowLoggedInRestAccess( $access )
	{
		if ( !is_user_logged_in() ) {
			return new WP_Error(
				'rest_cannot_access',
				_x( 'Only authenticated users can access the REST API.', 'rest', 'wp-surf-theme' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}

		return $access;
	}

	/**
	 * Searches for $elements in $blockContent and adds .surf-block-$classSuffix.
	 * @param string $blockContent
	 * @param array $elements
	 * @param string $classSuffix
	 * @return string|false
	 */
	public static function addBlockClass( $blockContent, $elements, $classSuffix )
	{
		if ( empty( $blockContent ) ) {
			return $blockContent;
		}

		$content  = '<?xml encoding="UTF-8">' . $blockContent;
		$document = new \DomDocument( '1.0', 'UTF-8' );
		libxml_use_internal_errors( true );

		$document->loadHTML( $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		foreach ( $elements as $element ) {
			foreach ( $document->getElementsByTagName( $element ) as $tag ) {
				$hasClass = $tag->hasAttribute( 'class' );
				$newClass = 'surf-block-' . $classSuffix;
				if ( $hasClass ) {
					$currentClass = $tag->getAttribute( 'class' );
					$newClass     = $newClass . ' ' . $currentClass;
				}
				$tag->setAttribute( 'class', $newClass );
			}
		}

		return $document->saveHTML();
	}

	/**
	 * Add classes to core Gutenberg blocks without classes.
	 * @param string $blockContent
	 * @param array $block
	 */
	public static function addCustomBlockClasses( $blockContent, $block )
	{
		switch ( $block['blockName'] ) {
			case 'core/heading':
				$lvl = $block['attrs']['level'] ?? 2; // 2 is default heading
				$lvl = 'h' . $lvl;

				return static::addBlockClass( $blockContent, [ $lvl ], 'heading' );

			case 'core/list':
				return static::addBlockClass( $blockContent, [ 'ol', 'ul' ], 'list' );

			case 'core/paragraph':
				return static::addBlockClass( $blockContent, [ 'p' ], 'paragraph' );
		}

		return $blockContent;
	}

	/**
	 * Change Comments Fields
	 * @param string $defaults
	 */
	public static function changeCommentFields( $defaults )
	{
		// Edit this to your needs:
		$btn_text = __( 'React on this article', 'wp-surf-theme' );
		$button   = '<button class="button %3$s" id="%2$s" type="submit">' . $btn_text . '</button>';

		// Override the default submit button:
		$defaults['submit_button'] = $button;
		$defaults['title_reply']   = __( 'Add your comment', 'wp-surf-theme' );
		$defaults['logged_in_as']  = '<p class="logged-in-as">' . __( 'You are currently logged in, therefor you don\'t see the fields for name, email etc.', 'wp-surf-theme' ) . '</p>';

		return $defaults;
	}

	/**
	 * @param $classes
	 * @return string
	 */
	public static function addThemeBodyClass( $classes )
	{
		$classes .= ' ' . Theme::bodyClass();

		return $classes;
	}

	/**
	 * @param $mimes
	 * @return mixed
	 */
	public static function uploadMimes( $mimes )
	{
		// Allow svg uploads
		$mimes['svg'] = 'image/svg+xml';

		// Allow font uploads
		foreach ( ACFHelper::listAllowedFontTypes() as $ext ) {
			$mimes[ $ext ] = 'font/' . $ext;
		}

		return $mimes;
	}

	/**
	 * @param $data
	 * @param $file
	 * @param $filename
	 * @param $mimes
	 * @return mixed
	 */
	public static function secureFontMimeCheck( $data, $file, $filename, $mimes )
	{
		if ( !empty( $data['ext'] ) && !empty( $data['type'] ) ) {
			return $data;
		}

		$current_ext     = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		$font_extensions = ACFHelper::listAllowedFontTypes();
		if ( !in_array( $current_ext, $font_extensions ) ) {
			return $data;
		}

		// Use finfo() to scan the real content
		$file_info = finfo_open( FILEINFO_MIME_TYPE );
		$real_mime = finfo_file( $file_info, $file );
		finfo_close( $file_info );

		// List the secure MIME-types that servers appoint to fonts
		$allowed_mimes = [
			'application/x-font-ttf',
			'application/x-font-otf',
			'application/font-woff',
			'application/font-woff2',
			'application/font-sfnt',
			'application/vnd.ms-opentype', // Often used for OTF/TTF
			'application/octet-stream',    // Many fonts don't have a specific binary header
			'font/ttf',
			'font/otf',
			'font/woff',
			'font/woff2',
			'font/sfnt',
		];
		if ( !in_array( $real_mime, $allowed_mimes ) ) {
			return $data;
		}

		// Only when the content matches the list, we allow this file
		$data['ext']             = $current_ext;
		$data['type']            = $real_mime;
		$data['proper_filename'] = $filename;

		return $data;
	}

	/**
	 * @return void
	 */
	public static function generate404Status()
	{
		$errorPage = get_option( 'options_error_page' );

		if ( $errorPage && is_page( $errorPage ) ) {
			global $wp_query;
			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
		}
	}

	/**
	 * @param $endpoints
	 * @return array
	 */
	public static function disableRestEndpoints( $endpoints ): array
	{
		foreach ( $endpoints as $key => &$endpoint ) {
			if ( !str_contains( $key, '/wp/v2/users' ) ) {
				continue;
			}

			foreach ( $endpoint as &$item ) {
				if ( !is_array( $item ) ) {
					continue;
				}

				$item['permission_callback'] = !is_callable( $item['permission_callback'] ?? null )
					? fn() => $item['permission_callback'] && is_user_logged_in()
					: fn() => is_user_logged_in();
			}
		}

		return $endpoints;
	}

	/**
	 * Add custom separator for Yoast Breadcrumbs
	 */
	public static function voSeoBreadcrumbSeparator( $this_options_breadcrumbs_sep )
	{
		return '<span class="breadcrumb__separator"></span>';
	}

}
