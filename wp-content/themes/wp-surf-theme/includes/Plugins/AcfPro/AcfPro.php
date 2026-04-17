<?php

namespace SURF\Plugins\AcfPro;

use Exception;
use SURF\Helpers\Helper;
use SURF\Plugins\Plugin;

/**
 * Class AcfPro
 * @package SURF\Plugins\AcfPro
 */
class AcfPro extends Plugin
{

	public const OPTION_LICENSE = 'acf_pro_license';

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return _x( 'ACF Pro', 'plugin', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'acf.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'advanced-custom-fields-pro';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/AcfPro/' . $this->getSlug() . '.zip' );
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
	 * @throws Exception
	 */
	public function prepareInstallation(): void
	{
		$response = wp_remote_get( 'https://connect.advancedcustomfields.com/packages.json' );
		if ( is_wp_error( $response ) ) {
			throw new Exception( _x( 'Could not fetch plugin info.', 'error', 'wp-surf-theme' ) );
		}

		$body    = wp_remote_retrieve_body( $response );
		$content = json_decode( $body, true );
		$package = $content['packages']['wpengine/advanced-custom-fields-pro'] ?? null;
		if ( empty( $package ) ) {
			throw new Exception( _x( 'Could not find package info.', 'error', 'wp-surf-theme' ) );
		}

		$latest       = array_shift( $package );
		$download_url = $latest['dist']['url'] ?? null;
		if ( empty( $download_url ) ) {
			throw new Exception( _x( 'Could not find download link, is the license key correct?', 'error', 'wp-surf-theme' ) );
		}

		$license  = $this->getLicenseKey();
		$url      = get_site_url();
		$response = wp_safe_remote_get( $download_url, [
			'headers' => [
				'Authorization' => 'Basic ' . base64_encode( "$license:$url" ),
			],
		] );

		$error = _x( 'Could not download plugin, is the license key correct?', 'error', 'wp-surf-theme' );
		if ( is_wp_error( $response ) ) {
			throw new Exception( $error );
		}

		$content = wp_remote_retrieve_body( $response );
		try {
			Helper::putContents( $this->getZipPath(), $content );
		} catch ( Exception $exception ) {
			throw new Exception( _x( 'Could not write plugin zip to disk', 'error', 'wp-surf-theme' ) );
		}
	}

	/**
	 * @return void
	 */
	public function afterInstallation(): void
	{
		update_option( static::OPTION_LICENSE, $this->getLicenseKey() );
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function afterActivation(): void
	{
		if ( !function_exists( 'acf_pro_activate_license' ) ) {
			throw new Exception( _x( 'ACF Pro is not activated.', 'error', 'wp-surf-theme' ) );
		}

		acf_pro_activate_license( get_option( static::OPTION_LICENSE ), true );
	}

}
