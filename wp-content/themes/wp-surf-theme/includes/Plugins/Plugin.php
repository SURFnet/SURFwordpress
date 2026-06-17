<?php

namespace SURF\Plugins;

use Exception;
use Illuminate\Support\Str;
use SURF\Helpers\Helper;
use ZipArchive;

/**
 * Class Plugin
 * @package SURF\Plugins
 */
abstract class Plugin
{

	public string $licenseKey = '';

	/**
	 * @return string
	 */
	abstract public function getPluginFile(): string;

	/**
	 * @return string
	 */
	abstract public function getSlug(): string;

	/**
	 * @return string
	 */
	abstract public function getZipPath(): string;

	/**
	 * @return void
	 */
	public function register(): void
	{
		$file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->getPluginFilePath();
		register_activation_hook( $file, [ $this, 'afterActivation' ] );
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function install(): void
	{
		if ( !is_dir( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->getSlug() ) ) {
			$this->prepareInstallation();

			$zip    = new ZipArchive();
			$result = $zip->open( $this->getZipPath() );
			if ( $result !== true ) {
				throw new Exception( sprintf( _x( 'Could not open zip file %s.', 'error', 'wp-surf-theme' ), $this->getZipPath() ) );
			}

			if ( !$zip->extractTo( WP_PLUGIN_DIR ) ) {
				throw new Exception( _x( 'Could not extract plugin.', 'error', 'wp-surf-theme' ) );
			}

			$zip->close();
		}

		$this->afterInstallation();
	}

	/**
	 * @return void
	 * @throws Exception
	 */
	public function prepareInstallation(): void
	{
		$plugin_url = add_query_arg( [
			'action' => 'plugin_information',
			'slug'   => $this->getSlug(),
		], 'https://api.wordpress.org/plugins/info/1.2/' );
		$response   = wp_remote_get( $plugin_url );
		if ( is_wp_error( $response ) ) {
			throw new Exception( _x( 'Could not fetch plugin info.', 'error', 'wp-surf-theme' ) );
		}

		$body     = wp_remote_retrieve_body( $response );
		$content  = json_decode( $body );
		$download = $content?->download_link;
		if ( empty( $download ) ) {
			throw new Exception( _x( 'Could not find download link.', 'error', 'wp-surf-theme' ) );
		}

		$this->writeZip( $download );
	}

	/**
	 * @param string $url
	 * @return void
	 * @throws Exception
	 */
	public function writeZip( string $url ): void
	{
		$response = wp_safe_remote_get( $url, [
			'timeout' => 60,
		] );
		if ( is_wp_error( $response ) ) {
			throw new Exception( _x( 'Could not download plugin.', 'error', 'wp-surf-theme' ) );
		}

		$zip_content = wp_remote_retrieve_body( $response );
		if ( empty( $zip_content ) ) {
			throw new Exception( _x( 'Downloaded plugin zip is empty.', 'error', 'wp-surf-theme' ) );
		}

		try {
			Helper::putContents( $this->getZipPath(), $zip_content );
		} catch ( Exception $exception ) {
			throw new Exception( _x( 'Could not write plugin zip to disk', 'error', 'wp-surf-theme' ) );
		}
	}

	/**
	 * @return void
	 */
	public function afterInstallation(): void {}

	/**
	 * @return void
	 */
	public function afterActivation(): void {}

	/**
	 * @param string $licenseKey
	 * @return void
	 */
	public function setLicenseKey( string $licenseKey ): void
	{
		$this->licenseKey = $licenseKey;
	}

	/**
	 * @return string
	 */
	public function getLicenseKey(): string
	{
		return $this->licenseKey;
	}

	/**
	 * @return bool
	 */
	public function requiresLicense(): bool
	{
		return false;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return Str::title( Str::snake( class_basename( static::class ), ' ' ) );
	}

	/**
	 * @return string
	 */
	public function getOptionKey(): string
	{
		return 'surf_installed_plugins--' . $this->getSlug();
	}

	/**
	 * @return bool
	 */
	public function isInstalled(): bool
	{
		return file_exists( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->getPluginFilePath() );
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return in_array( $this->getPluginFilePath(), get_option( 'active_plugins', [] ) );
	}

	/**
	 * @param bool $value
	 * @return void
	 */
	public function setInstalled( bool $value = true ): void
	{
		update_option( $this->getOptionKey(), $value );
	}

	/**
	 * @return string
	 */
	public function getPluginFilePath(): string
	{
		return $this->getSlug() . DIRECTORY_SEPARATOR . $this->getPluginFile();
	}

	/**
	 * @return string
	 */
	public function getActivationUrl(): string
	{
		$path = $this->getPluginFilePath();

		return add_query_arg( [
			'action'   => 'activate',
			'plugin'   => $path,
			'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $path ),
		], self_admin_url( 'plugins.php' ) );
	}

	/**
	 * @return string
	 */
	public function getInstructions(): string
	{
		return '';
	}

	/**
	 * @return array
	 */
	public function toArray(): array
	{
		return [
			'name'             => $this->getName(),
			'slug'             => $this->getSlug(),
			'installed'        => $this->isInstalled(),
			'active'           => $this->isActive(),
			'activation_url'   => $this->getActivationUrl(),
			'instructions'     => $this->getInstructions(),
			'requires_license' => $this->requiresLicense(),
		];
	}

}
