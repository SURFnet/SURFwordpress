<?php

namespace SURF\Plugins\PolylangPro;

use Exception;
use SURF\Plugins\Plugin;

/**
 * Class PolylangPro
 * @package SURF\Plugins\PolylangPro
 */
class PolylangPro extends Plugin
{

	public const OPTION_LICENSE = 'pll_license_key';

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'polylang.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'polylang-pro';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/PolylangPro/' . $this->getSlug() . '.zip' );
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
		$license = $this->getLicenseKey();
		$args    = [
			'timeout'   => 15,
			'sslverify' => true,
			'body'      => [
				'edd_action'  => 'get_version',
				'license'     => $license,
				'item_name'   => 'Polylang Pro',
				'version'     => '0.0.0',
				'slug'        => $this->getSlug(),
				'author'      => 'WP SYNTEX',
				'url'         => home_url(),
				'beta'        => false,
				'php_version' => phpversion(),
				'wp_version'  => get_bloginfo( 'version' ),
			],
		];
		$request = wp_remote_post( 'https://www.polylang.pro', $args );
		if ( is_wp_error( $request ) || ( 200 !== wp_remote_retrieve_response_code( $request ) ) ) {
			throw new Exception( _x( 'Could not fetch plugin info.', 'error', 'wp-surf-theme' ) );
		}

		$content = json_decode( wp_remote_retrieve_body( $request ) );
		if ( isset( $content->msg ) ) {
			throw new Exception( $content->msg );
		}

		$download = $content->download_link ?? null;
		if ( empty( $download ) ) {
			throw new Exception( _x( 'Could not find download link, is the license key correct?', 'error', 'wp-surf-theme' ) );
		}

		$this->writeZip( $download );
	}

	/**
	 * @return void
	 */
	public function afterInstallation(): void
	{
		update_option( static::OPTION_LICENSE, $this->getLicenseKey() );
	}

	/**
	 * @noinspection PhpFullyQualifiedNameUsageInspection
	 * @return void
	 * @throws Exception
	 */
	public function afterActivation(): void
	{
		if ( !class_exists( '\PLL_License' ) ) {
			throw new Exception( _x( 'Polylang Pro is not installed.', 'error', 'wp-surf-theme' ) );
		}

		if ( !defined( 'POLYLANG_PRO_FILE' ) || !defined( 'POLYLANG_VERSION' ) ) {
			throw new Exception( _x( 'Polylang Pro is not installed: constants are not available.', 'error', 'wp-surf-theme' ) );
		}

		$license = get_option( static::OPTION_LICENSE );
		$pll     = new \PLL_License( POLYLANG_PRO_FILE, 'Polylang Pro', POLYLANG_VERSION, 'WP SYNTEX' );
		$pll->activate_license( $license );
	}

}
