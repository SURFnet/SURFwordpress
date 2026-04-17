<?php

namespace SURF\Services;

use WP_Theme;
use WP_Upgrader;

/**
 * Class UpdateService
 * @package SURF\Services
 */
class UpdateService
{

	public const ACTION_THEME_DETAILS = 'surf_theme_details';

	/**
	 * @param AbstractReleaseService $releaseService
	 * @param string $zip_name
	 * @param string $theme_slug
	 */
	public function __construct(
		public AbstractReleaseService $releaseService,
		protected string              $zip_name,
		protected string              $theme_slug
	) {}

	/**
	 * @return void
	 */
	public function init(): void
	{
		add_filter( 'pre_set_site_transient_update_themes', [ $this, 'setSiteTransientUpdateThemes' ] );
		add_filter( 'upgrader_pre_download', [ $this, 'upgraderPreDownload' ], 10, 4 );

		add_action( 'admin_post_' . static::ACTION_THEME_DETAILS, [ $this, 'renderThemeDetails' ] );
	}

	/**
	 * @param $transient
	 * @return mixed
	 */
	public function setSiteTransientUpdateThemes( $transient ): object
	{
		$update = $this->getUpdateObject();
		if ( !empty( $update ) ) {
			$transient->response[ $this->getThemeSlug() ] = $update;
		}

		return $transient;
	}

	/**
	 * @param mixed $reply
	 * @param string $package
	 * @param WP_Upgrader $upgrader
	 * @param array $data
	 * @return false|mixed|string
	 */
	public function upgraderPreDownload( $reply, string $package, WP_Upgrader $upgrader, array $data )
	{
		if ( ( $data['theme'] ?? '' ) !== $this->getThemeSlug() ) {
			return $reply;
		}

		return $this->releaseService->preDownloadRelease( $this->getZipPath(), $package );
	}

	/**
	 * @return void
	 */
	public function renderThemeDetails(): void
	{
		if ( !current_user_can( 'update_themes' ) ) {
			wp_die( 'Unauthorized' );
		}

		$release = $this->getRelease();
		if ( !$release ) {
			return;
		}

		$theme   = $this->getTheme();
		$details = $release['changelog'] ?? _x( 'No changelog available.', 'admin', 'wp-surf-theme' );

		echo surfView( 'admin.theme-details', [
			'theme_name' => (string) $theme?->get( 'Name' ),
			'version'    => $release['version'] ?? null,
			'details'    => wp_kses_post( $details ),
			'info_list'  => $this->generateInfoList( $release ),
		] );
	}

	/**
	 * @return array|null
	 */
	public function getUpdateObject(): ?array
	{
		$release = $this->getRelease();
		if ( !$release ) {
			return null;
		}

		$theme_version = $this->getThemeVersion();
		if ( $theme_version && version_compare( $release['version'], $theme_version, '<=' ) ) {
			return null;
		}

		return [
			'theme'         => $this->getThemeSlug(),
			'new_version'   => $release['version'],
			'url'           => add_query_arg( [ 'action' => static::ACTION_THEME_DETAILS ], admin_url( 'admin-post.php' ) ),
			'package'       => $release['zip_url'],
			'tested'        => $release['wp_version'] ?? '',
			'requires_php'  => $release['php_version'] ?? '',
			'compatibility' => (object) ( $release['compatibility'] ?? [] ),
		];
	}

	/**
	 * @param array $release
	 * @return array
	 */
	public function generateInfoList( array $release ): array
	{
		$list = [
			[
				'label' => _x( 'Current version', 'admin', 'wp-surf-theme' ),
				'value' => $this->getThemeVersion(),
			],
		];

		if ( !empty( $release['version'] ) ) {
			$list[] = [
				'label' => _x( 'New version', 'admin', 'wp-surf-theme' ),
				'value' => $release['version'],
			];
		}

		if ( !empty( $release['published_at'] ) ) {
			$list[] = [
				'label' => _x( 'Release date', 'admin', 'wp-surf-theme' ),
				'value' => $release['published_at'],
			];
		}

		if ( !empty( $release['wp_version'] ) ) {
			$list[] = [
				'label' => _x( 'WP requirements', 'admin', 'wp-surf-theme' ),
				'value' => sprintf( _x( 'WordPress %s', 'admin', 'wp-surf-theme' ), $release['wp_version'] ),
			];
		}

		if ( !empty( $release['php_version'] ) ) {
			$list[] = [
				'label' => _x( 'PHP requirements', 'admin', 'wp-surf-theme' ),
				'value' => sprintf( _x( 'PHP %s', 'admin', 'wp-surf-theme' ), $release['php_version'] ),
			];
		}

		return array_merge( $list, $release['info_list'] ?? [] );
	}

	/**
	 * @return array|null
	 */
	public function getRelease(): ?array
	{
		$allow_beta = $this->allowBetaUpdates();

		return $this->releaseService->getReleaseForUpdate( $this->zip_name, $allow_beta );
	}

	/**
	 * @return string
	 */
	public function getThemeSlug(): string
	{
		return $this->theme_slug;
	}

	/**
	 * @return null|WP_Theme
	 */
	public function getTheme(): ?WP_Theme
	{
		$theme = wp_get_theme( $this->getThemeSlug() );

		return $theme->exists() ? $theme : null;
	}

	/**
	 * @return string|null
	 */
	public function getThemeVersion(): ?string
	{
		$theme = $this->getTheme();
		if ( empty( $theme ) ) {
			return null;
		}

		return ltrim( (string) $theme->get( 'Version' ), 'v' );
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return get_temp_dir() . DIRECTORY_SEPARATOR . $this->zip_name;
	}

	/**
	 * @return bool
	 */
	public function allowBetaUpdates(): bool
	{
		return false; // @TODO: Implement option to allow beta updates
	}

}
