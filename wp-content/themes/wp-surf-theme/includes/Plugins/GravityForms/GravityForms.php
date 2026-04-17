<?php

namespace SURF\Plugins\GravityForms;

use Exception;
use SURF\Plugins\Plugin;

/**
 * Class GravityForms
 * @package SURF\Plugins\GravityForms
 */
class GravityForms extends Plugin
{

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'gravityforms.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'gravityforms';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/GravityForms/' . $this->getSlug() . '.zip' );
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
		$plugin_url = add_query_arg( [
			'op'   => 'get_plugin',
			'slug' => 'gravityforms',
			'key'  => $this->getLicenseKey(),
		], 'https://www.gravityhelp.com/wp-content/plugins/gravitymanager/api.php' );
		$response   = wp_remote_get( $plugin_url );
		if ( is_wp_error( $response ) ) {
			throw new Exception( _x( 'Could not fetch plugin info.', 'error', 'wp-surf-theme' ) );
		}

		$body    = wp_remote_retrieve_body( $response );
		$content = unserialize( $body, [ 'allowed_classes' => false ] );
		if ( false === $content && 'b:0;' !== $body ) {
			throw new Exception( _x( 'Invalid response from license server.', 'error', 'wp-surf-theme' ) );
		}
		if ( !is_array( $content ) ) {
			throw new Exception( _x( 'Unexpected response format from license server.', 'error', 'wp-surf-theme' ) );
		}

		$download = $content['download_url'] ?? null;
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
		update_option( 'rg_gforms_key', $this->getLicenseKey() );
	}

}
