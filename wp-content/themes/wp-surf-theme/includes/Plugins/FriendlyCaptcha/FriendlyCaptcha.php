<?php

namespace SURF\Plugins\FriendlyCaptcha;

use SURF\Plugins\Plugin;

/**
 * Class FriendlyCaptcha
 * @package SURF\Plugins\FriendlyCaptcha
 */
class FriendlyCaptcha extends Plugin
{

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'friendly-captcha.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'friendly-captcha';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/FriendlyCaptcha/' . $this->getSlug() . '.zip' );
	}

	/**
	 * @return bool
	 */
	public function requiresLicense(): bool
	{
		return true;
	}

	/**
	 * @return void
	 */
	public function afterInstallation(): void
	{
		update_option( 'frcaptcha_api_key', $this->getLicenseKey() );

		// Default settings.
		update_option( 'frcaptcha_gravity_forms_integration_active', 1 );
		update_option( 'frcaptcha_wp_register_integration_active', 1 );
		update_option( 'frcaptcha_wp_login_integration_active', 1 );
		update_option( 'frcaptcha_wp_reset_password_integration_active', 1 );
		update_option( 'frcaptcha_wp_comments_integration_active', 1 );
		update_option( 'frcaptcha_wp_comments_logged_in_integration_active', 1 );
		update_option( 'frcaptcha_widget_language', 'automatic' );
	}

}
