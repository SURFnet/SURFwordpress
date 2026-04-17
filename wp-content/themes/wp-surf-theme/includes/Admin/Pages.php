<?php

namespace SURF\Admin;

use SURF\Admin\Pages\CspTool;
use SURF\Admin\Pages\SetupWizard;
use SURF\Helpers\PolylangHelper;

/**
 * Class Pages.
 */
class Pages
{

	public const SLUG_THEME_SETTINGS = 'surf-theme-settings';
	public const SLUG_EVENT_SETTINGS = 'surf-agenda-settings';

	/**
	 * Make sure pages are registered at the right time
	 * @return void
	 */
	public static function init(): void
	{
		add_action( 'acf/init', [ static::class, 'initPages' ] );
		add_action( 'admin_menu', [ static::class, 'adminInit' ] );
	}

	/**
	 * Register pages
	 * @return void
	 */
	public static function initPages(): void
	{
		if ( !function_exists( 'acf_add_options_page' ) || !function_exists( 'acf_add_options_sub_page' ) ) {
			return;
		}

		$languages = PolylangHelper::getLanguages();
		if ( $languages ) {
			// Theme Settings
			$parent = static::register(
				[
					'page_title' => _x( 'Theme Settings', 'admin', 'wp-surf-theme' ),
					'menu_title' => _x( 'Theme Settings', 'admin', 'wp-surf-theme' ),
					'capability' => 'edit_posts',
					'icon_url'   => 'dashicons-admin-generic',
					'redirect'   => _x( 'Theme Settings', 'admin', 'wp-surf-theme' ),
				]
			);

			// Global options
			static::registerSubPage(
				[
					'page_title'  => _x( 'Theme Settings', 'admin', 'wp-surf-theme' ),
					'menu_title'  => _x( 'Theme Settings', 'admin', 'wp-surf-theme' ),
					'menu_slug'   => static::SLUG_THEME_SETTINGS,
					'parent_slug' => $parent['menu_slug'],
				]
			);

			foreach ( $languages as $lang => $language ) {
				$lang_code = strtoupper( $language );
				// Add sub-page
				static::registerSubPage(
					[
						'page_title'  => sprintf( _x( 'Theme Settings (%s)', 'admin', 'wp-surf-theme' ), $lang_code ),
						'menu_title'  => sprintf( _x( 'Theme Settings (%s)', 'admin', 'wp-surf-theme' ), $lang_code ),
						'menu_slug'   => static::SLUG_THEME_SETTINGS . '-' . $language,
						'post_id'     => $language,
						'parent_slug' => $parent['menu_slug'],
					]
				);
			}
		} else {
			static::register(
				[
					'page_title' => _x( 'Theme Settings', 'admin', 'wp-surf-theme' ),
					'menu_title' => _x( 'Theme Settings', 'admin', 'wp-surf-theme' ),
					'menu_slug'  => static::SLUG_THEME_SETTINGS,
					'capability' => 'edit_posts',
					'icon_url'   => 'dashicons-admin-generic',
					'redirect'   => false,
				]
			);
		}

		static::registerSubPage(
			[
				'page_title'  => _x( 'Event settings', 'admin', 'wp-surf-theme' ),
				'menu_slug'   => static::SLUG_EVENT_SETTINGS,
				'capability'  => 'edit_theme_settings',
				'parent_slug' => 'edit.php?post_type=surf-agenda',
			]
		);
	}

	/**
	 * Register ACF options page.
	 * @param array $args
	 * @return array|false
	 */
	public static function register( array $args )
	{
		if ( !function_exists( 'acf_add_options_page' ) ) {
			return false;
		}

		return acf_add_options_page( $args );
	}

	/**
	 * Register ACF options sub page.
	 * @param array $args
	 * @return array|false
	 */
	public static function registerSubPage( array $args )
	{
		if ( !function_exists( 'acf_add_options_sub_page' ) ) {
			return false;
		}

		return acf_add_options_sub_page( $args );
	}

	/**
	 * Register admin pages
	 * @return void
	 */
	public static function adminInit(): void
	{
		$capability = 'edit_theme_settings';
		add_menu_page(
			_x( 'Setup Wizard', 'admin', 'wp-surf-theme' ),
			_x( 'Setup Wizard', 'admin', 'wp-surf-theme' ),
			$capability,
			'setup-wizard',
			[ new SetupWizard(), 'render' ],
			'dashicons-superhero-alt'
		);

		add_submenu_page(
			'tools.php',
			_x( 'CSP Tool', 'admin', 'wp-surf-theme' ),
			_x( 'CSP Tool', 'admin', 'wp-surf-theme' ),
			$capability,
			'csp-tool',
			[ new CspTool(), 'render' ],
		);
	}

}
