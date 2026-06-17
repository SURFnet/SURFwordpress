<?php

namespace SURF\Providers;

use Illuminate\Support\Facades\Cache;
use JetBrains\PhpStorm\NoReturn;
use SURF\Application;
use SURF\Helpers\Helper;
use WP_Admin_Bar;

/**
 * Class CacheServiceProvider
 * @package SURF\Providers
 */
class CacheServiceProvider extends \Illuminate\Cache\CacheServiceProvider
{

	public const ACTION = 'surf_clear_cache';

	/**
	 * @var Application
	 */
	protected $app;

	/**
	 * @return void
	 */
	public function boot(): void
	{
		add_action( 'admin_bar_menu', [ static::class, 'addClearCacheButton' ], 100 );
		add_action( 'admin_post_surf_clear_cache', [ static::class, 'handleClearCache' ] );
	}

	/**
	 * Adds clear cache button to WP Admin bar
	 * @param WP_Admin_Bar $admin_bar
	 */
	public static function addClearCacheButton( WP_Admin_Bar $admin_bar ): void
	{
		if ( !is_admin() ) {
			return;
		}

		$button_text = _x( 'Clear SURF Cache', 'admin', 'wp-surf-theme' );
		$proceed     = Helper::getSanitizedRequest( 'result', '' );
		if ( $proceed === static::ACTION ) {
			$message     = _x( 'SURF Cache cleared', 'cache clear', 'wp-surf-theme' );
			$button_text = '<span style="color:lawngreen;">' . $message . '</span>';
		}

		$admin_bar->add_menu( [
			'id'    => 'clear-surf-cache',
			'title' => $button_text,
			'href'  => wp_nonce_url(
				add_query_arg( 'action', static::ACTION, admin_url( 'admin-post.php' ) ),
				static::ACTION,
			),
		] );
	}

	/**
	 * @return void
	 */
	#[NoReturn]
	public static function handleClearCache(): void
	{
		$referer = wp_get_referer();
		if ( check_admin_referer( static::ACTION ) ) {
			Cache::flush();
			$referer = add_query_arg( 'result', static::ACTION, $referer );
		}

		wp_safe_redirect( $referer );
		exit;
	}

}
