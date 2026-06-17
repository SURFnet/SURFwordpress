<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;
use SURF\Enums\Theme;
use SURF\Helpers\RoadmapHelper;
use SURF\Hooks\HeadingHooks;

/**
 * Class ThemeServiceProvider
 * @package SURF\Providers
 */
class ThemeServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function register(): void
	{
		add_action( 'init', function ()
		{
			// Load translations
			load_theme_textdomain( 'wp-surf-theme', get_stylesheet_directory() . '/languages' );
		}, 10 );
	}

	/**
	 * @return void
	 */
	public function boot(): void
	{
		require_once $this->app->path( 'includes/bootstrap.php' );

		/*
		 * Register hooks
		 */
		add_action( 'after_setup_theme', [ $this, 'setupTheme' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueueScripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminScripts' ] );
		add_action( 'login_enqueue_scripts', [ $this, 'enqueueLoginScripts' ] );
		add_action( 'admin_head', [ $this, 'headCss' ] );
		add_action( 'wp_head', [ $this, 'headCss' ] );
	}

	/**
	 * Sets up theme features
	 * @return void
	 */
	public function setupTheme(): void
	{
		// Register the color palette for the editor
		// WordPress doesn't support grouped colors, so we use the flat palette
		add_theme_support( 'editor-color-palette', Theme::colorPaletteCompact() );

		// Make the color palette available to JavaScript
		// For our custom implementation, we can use the grouped palette
		wp_localize_script( 'wp-blocks', 'surfThemeColors', [
			'colorPalette'    => Theme::colorPalette(),
			'primaryColor'    => Theme::primaryColor(),
			'secondaryColor'  => Theme::secondaryColor(),
			'quaternaryColor' => Theme::quaternaryColor(),
		] );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ] );

		// Register menu's
		$this->registerMenus();
	}

	/**
	 * @return void
	 */
	public function enqueueScripts(): void
	{
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		vite()->enqueue( 'surf.theme', 'src/js/theme.js', [ 'wp-i18n' ] );
		vite()->enqueue( 'surf.sub-theme', 'src/js/theme.' . Theme::current() . '.js' );
		vite()->enqueue( 'surf.exports', 'src/scss/exports.scss' );

		global $pageNr, $wp_query;
		wp_localize_script( 'surf.theme', 'customData', [
			'ajaxURL'        => admin_url( 'admin-ajax.php' ),
			'ajaxSecurity'   => wp_create_nonce( 'surf-ajax' ),
			'apiBaseURL'     => get_rest_url( get_current_blog_id(), 'surf/v1/' ),
			'iconsPath'      => SURF_THEME_URI_ICONS,
			'lang'           => function_exists( 'pll_current_language' ) ? pll_current_language() : null,
			'pageNr'         => ( $pageNr ) ? $pageNr : 1,
			'paramsToRemove' => apply_filters( 'params_to_remove', [] ),
			'isArchive'      => $wp_query->is_posts_page || $wp_query->is_archive() || is_search(),
			'archiveType'    => $wp_query->is_posts_page ? 'post' : ( is_search() ? 'search' : $wp_query->get(
				'post_type'
			) ),
			'themes'         => Theme::options(),
			'currentTheme'   => Theme::current(),
		] );
		wp_set_script_translations( 'surf.theme', 'wp-surf-theme', vite()->assetPath( 'i18n' ) );
	}

	/**
	 * @return void
	 */
	public function enqueueAdminScripts(): void
	{
		$handle_admin = 'surf.admin';
		vite()->enqueue( $handle_admin, 'src/js/admin.js' );
		wp_localize_script( $handle_admin, 'customData', [
			'date'         => [
				'format' => get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
			],
			'iconsPath'    => SURF_THEME_URI_ICONS,
			'themes'       => Theme::options(),
			'currentTheme' => Theme::current(),
			'restNonce'    => wp_create_nonce( 'wp_rest' ),
			'roadmapIcons' => RoadmapHelper::getIconsForScript(),
		] );

		$editor_deps = [
			'wp-components',
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			'wp-editor',
			'wp-block-editor',
			'wp-data',
			'wp-date',
		];

		$handle_editor = 'surf.editor';
		vite()->enqueue( 'surf.editor.theme', 'src/js/editor.' . Theme::current() . '.js' );
		vite()->enqueue( $handle_editor, 'src/js/editor.js', $editor_deps );

		wp_localize_script( $handle_editor, 'surf', [
			'date'         => [
				'format' => get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
			],
			'iconsPath'    => SURF_THEME_URI_ICONS,
			'themes'       => Theme::options(),
			'currentTheme' => Theme::current(),
			'blocks'       => apply_filters( 'surf_editor_blocks_data', [] ),
		] );

		wp_set_script_translations( $handle_editor, 'wp-surf-theme', vite()->assetPath( 'i18n' ) );
	}

	/**
	 * @return void
	 */
	public function enqueueLoginScripts(): void
	{
		vite()->enqueue( 'surf.login', 'src/js/login.js' );
	}

	/**
	 * @return void
	 */
	public function registerMenus(): void
	{
		$menus = [
			'primary-menu'              => _x( 'Primary Menu', 'admin', 'wp-surf-theme' ),
			'top-menu'                  => _x( 'Top menu', 'admin', 'wp-surf-theme' ),
			'footer-menu'               => _x( 'Footer menu', 'admin', 'wp-surf-theme' ),
			'footer-first-column-menu'  => _x( 'First column menu', 'admin', 'wp-surf-theme' ),
			'footer-second-column-menu' => _x( 'Second column menu', 'admin', 'wp-surf-theme' ),
			'footer-third-column-menu'  => _x( 'Third column menu (Powered by Surf only)', 'admin', 'wp-surf-theme' ),
		];
		foreach ( $menus as $name => $label ) {
			register_nav_menu( $name, $label );
		}
	}

	/**
	 * Return a URL-encoded list of all polyfills to be loaded from cdnjs.cloudflare.com/polyfill.
	 * For a full list of available polyfills, see: https://cdnjs.cloudflare.com/polyfill/v3/url-builder/
	 * @return string
	 */
	public function getPolyfills(): string
	{
		$polyfills = [
			'default',
			'intersectionObserver',
			'intersectionObserverEntry',
			'Promise',
			'Promise.prototype.finally',
			'fetch',
			'Array.prototype.forEach',
			'NodeList.prototype.forEach',
		];

		return implode( '%2C', $polyfills );
	}

	/**
	 * @return void
	 */
	public function headCss(): void
	{
		if ( !Theme::isPoweredBy() ) {
			return;
		}

		$output    = '';
		$all_fonts = Theme::fonts();
		foreach ( $all_fonts as $body_font ) {
			$output .= "
                    @font-face {
                        font-family: '{$body_font['name']}';
                        src: url('{$body_font['url']}');
                    }
                ";
		}

		$body_font = Theme::font();
		if ( $body_font ) {
			$output .= "
                :root {
                    --surf-font-body: '$body_font', 'Open Sans', Helvetica, sans-serif;
                }
            ";
		}

		$heading_font = Theme::headingFont();
		if ( $heading_font ) {
			$output .= "
                :root {
                    --surf-font-heading: '$heading_font', 'Open Sans', Helvetica, sans-serif;
                }
            ";
		}

		$headings = HeadingHooks::$headings;
		foreach ( $headings as $heading ) {
			$heading_font = Theme::headingFont( $heading );
			if ( empty( $heading_font ) ) {
				continue;
			}

			$output .= "
                 :root {
                    --surf-font-heading-$heading: '{$heading_font}', var(--surf-font-heading), 'Open Sans', Helvetica, sans-serif;
                }
            ";
		}

		$output .= "
            :root {
                " . Theme::colorVariables() . "
            }
        ";
		echo '<style id="theme-service-style" type="text/css">' . $output . '</style>';
	}

}
