<?php

namespace SURF\Plugins\CspManager;

use SURF\Plugins\Plugin;

/**
 * Class CspManager
 * @package SURF\Plugins\CspManager
 */
class CspManager extends Plugin
{

	public const OPTION_ADMIN     = 'csp_manager_admin';
	public const OPTION_LOGGED_IN = 'csp_manager_loggedin';
	public const OPTION_FRONT     = 'csp_manager_frontend';

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return _x( 'CSP Manager', 'plugin', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'csp-manager.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'csp-manager';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/CspManager/' . $this->getSlug() . '.zip' );
	}

	/**
	 * @return void
	 */
	public function afterInstallation(): void
	{
		if ( !get_option( static::OPTION_ADMIN ) ) {
			update_option( static::OPTION_ADMIN, [
				"mode"               => "enforce",
				"enable_connect-src" => "1",
				"connect-src"        => "'self' yoast.com",
				"enable_default-src" => "1",
				"default-src"        => "'self'",
				"enable_font-src"    => "1",
				"font-src"           => "'self' data:",
				"enable_img-src"     => "1",
				"img-src"            => "'self' data: *.gravatar.com ps.w.org",
				"enable_script-src"  => "1",
				"script-src"         => "'self' 'unsafe-inline' 'unsafe-eval' surfnl.containers.piwik.pro polyfill.io www.googletagmanager.com",
				"enable_style-src"   => "1",
				"style-src"          => "'self' 'unsafe-inline' yoast.com",
				"header_reportto"    => "",
				"enable_frame-src"   => "1",
				"frame-src"          => "'self' *.vimeo.com *.youtube.com www.google.com",
			] );
		}

		if ( !get_option( static::OPTION_LOGGED_IN ) ) {
			update_option( static::OPTION_LOGGED_IN, [
				"mode"               => "enforce",
				"enable_connect-src" => "1",
				"connect-src"        => "'self' surfnl.containers.piwik.pro surfnl.piwik.pro",
				"enable_default-src" => "1",
				"default-src"        => "'self'",
				"enable_font-src"    => "1",
				"font-src"           => "'self' data:",
				"enable_img-src"     => "1",
				"img-src"            => "'self' data: *.gravatar.com",
				"enable_script-src"  => "1",
				"script-src"         => "'self' 'unsafe-inline' 'unsafe-eval' surfnl.containers.piwik.pro polyfill.io www.googletagmanager.com",
				"enable_style-src"   => "1",
				"style-src"          => "'self' 'unsafe-inline'",
				"header_reportto"    => "",
				"enable_frame-src"   => "1",
				"frame-src"          => "'self' *.vimeo.com *.youtube.com www.google.com",
			] );
		}

		if ( !get_option( static::OPTION_FRONT ) ) {
			update_option( static::OPTION_FRONT, [
				"mode"               => "enforce",
				"enable_connect-src" => "1",
				"connect-src"        => "'self' surfnl.containers.piwik.pro surfnl.piwik.pro",
				"enable_default-src" => "1",
				"default-src"        => "'self'",
				"enable_font-src"    => "1",
				"font-src"           => "'self' data:",
				"enable_frame-src"   => "1",
				"frame-src"          => "'self' *.vimeo.com *.youtube.com www.google.com",
				"enable_img-src"     => "1",
				"img-src"            => "'self' data: *.gravatar.com",
				"enable_script-src"  => "1",
				"script-src"         => "'self' 'unsafe-inline' 'unsafe-eval' surfnl.containers.piwik.pro polyfill.io www.googletagmanager.com",
				"enable_style-src"   => "1",
				"style-src"          => "'self' 'unsafe-inline'",
				"header_reportto"    => "",
			] );
		}
	}

}
