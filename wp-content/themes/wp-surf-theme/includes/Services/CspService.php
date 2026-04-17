<?php

namespace SURF\Services;

use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class CspService
 * @package SURF\Services
 */
class CspService
{

	/**
	 * @return string[]
	 */
	protected function getOptions(): array
	{
		return [
			'csp_manager_admin',
			'csp_manager_loggedin',
			'csp_manager_frontend',
		];
	}

	/**
	 * @return array
	 */
	public function getCurrentCspConfig(): array
	{
		$options = $this->getOptions();

		return array_combine( $options, array_map( 'get_option', $options ) );
	}

	/**
	 * @param array $config
	 * @return void
	 */
	public function importCspConfig( array $config ): void
	{
		$options = $this->getOptions();
		foreach ( $config as $key => $value ) {
			if ( in_array( $key, $options ) ) {
				update_option( $key, $value );
			}
		}
	}

	/**
	 * @return bool
	 * @throws BindingResolutionException
	 */
	public function syncCsp(): bool
	{
		$releaseService = surfApp()->make( GithubAbstractReleaseService::class );
		$latestRelease  = $releaseService->getLatestRelease( 'VanOns/surf-csp-config' );

		if ( is_null( $latestRelease ) ) {
			return false;
		}

		$version = $latestRelease['name'] ?? 0;

		if ( $version <= $this->getCspVersion() ) {
			return true;
		}

		$config = $this->searchAsset( 'config.json', $latestRelease );

		if ( is_null( $config ) ) {
			return false;
		}

		$file = $this->getFileContent( $config['url'], $config['name'], $releaseService );

		if ( empty( $file ) || empty( $content = file_get_contents( $file ) ) ) {
			return false;
		}

		$json = json_decode( $content, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return false;
		}

		$this->importCspConfig( $json );
		$this->setCspVersion( $version );

		unlink( $file );

		return true;
	}

	/**
	 * @param string $name
	 * @param array $release
	 * @return false|mixed|null
	 */
	public function searchAsset( string $name, array $release )
	{
		$asset = current( array_filter( $release['assets'] ?? [], function ( $a ) use ( $name )
		{
			return $a['name'] === $name;
		} ) );

		return $asset ?: null;
	}

	/**
	 * @param string $url
	 * @param string $name
	 * @param GithubAbstractReleaseService $releaseService
	 * @return string
	 */
	public function getFileContent( string $url, string $name, GithubAbstractReleaseService $releaseService ): string
	{
		$temp = get_temp_dir() . '/' . $name;

		// Remove any preexisting file
		@unlink( $temp );

		$response = wp_safe_remote_get(
			$url,
			[
				'timeout'  => 300,
				'stream'   => true,
				'filename' => $temp,
				'headers'  => [
					'Authorization' => "Bearer {$releaseService->getAccessToken()}",
					'Accept'        => 'application/octet-stream',
				],
			]
		);

		if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
			unlink( $temp );

			return false;
		}

		return $temp;
	}

	/**
	 * @return string
	 */
	public function getCspVersion(): string
	{
		return get_option( 'csp_version' );
	}

	/**
	 * @param string $version
	 * @return void
	 */
	public function setCspVersion( string $version ): void
	{
		update_option( 'csp_version', $version );
	}

}
