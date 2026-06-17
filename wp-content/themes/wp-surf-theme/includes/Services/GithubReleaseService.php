<?php

namespace SURF\Services;

/**
 * Class GithubReleaseService
 * @package SURF\Services
 */
class GithubReleaseService extends AbstractReleaseService
{

	public const API_BASE    = 'https://api.github.com';
	public const GITHUB_REPO = 'SURFnet/SURFwordpress';
	public const PHP_VERSION = '8.2';
	public const WP_VERSION  = ''; // @TODO: add tested WP version to release

	/**
	 * @param null|string $personal_access_token
	 */
	public function __construct(
		protected ?string $personal_access_token = null
	) {}

	/**
	 * @param string $zip_name
	 * @param bool $allow_beta
	 * @return null|array
	 */
	public function getReleaseForUpdate( string $zip_name, bool $allow_beta = false ): ?array
	{
		$release = $this->getLatestRelease( static::GITHUB_REPO, $allow_beta );
		if ( empty( $release ) ) {
			return null;
		}

		$asset = $this->getZipAsset( $release, $zip_name );
		if ( !$asset ) {
			return null;
		}

		$changelog = $release['body'] ?? null;
		$published = null;
		if ( !empty( $release['published_at'] ) ) {
			$timestamp = strtotime( $release['published_at'] );
			$published = wp_date( get_option( 'date_format' ), $timestamp );
		}

		$info_list = [];
		if ( !empty( $release['html_url'] ) ) {
			$info_list[] = [
				'type'  => 'html',
				'label' => _x( 'Source', 'admin', 'wp-surf-theme' ),
				'value' => sprintf( '<a href="%s" target="_blank">%s</a>',
					esc_url( $release['html_url'] ), _x( 'GitHub', 'admin', 'wp-surf-theme' ) ),
			];
		}

		return [
			'version'       => ltrim( $release['tag_name'] ?? '', 'v' ),
			'zip_url'       => $asset['url'],
			'changelog'     => $changelog ? $this->formatChangelog( $changelog ) : null,
			'published_at'  => $published,
			'php_version'   => static::PHP_VERSION,
			'wp_version'    => static::WP_VERSION,
			'compatibility' => (object) [],
			'info_list'     => $info_list,
		];
	}

	/**
	 * @param string $text
	 * @return string
	 */
	public function formatChangelog( string $text ): string
	{
		$text = static::formatMarkDown( $text );

		return preg_replace_callback(
			'/<a href="(https?:\/\/github\.com\/[^"]+\/compare\/[^"]+)".*?>.*?<\/a>/i',
			function ( $matches )
			{
				$url = $matches[1];

				return '<p><a href="' . esc_url( $url ) . '" target="_blank" class="button button-primary">'
				       . _x( 'View full changelog', 'admin', 'wp-surf-theme' ) . '</a></p>';
			},
			$text
		);
	}

	/**
	 * @param array $release
	 * @param string $zip_name
	 * @return null|array
	 */
	public function getZipAsset( array $release, string $zip_name ): ?array
	{
		foreach ( $release['assets'] ?? [] as $asset ) {
			if ( ( $asset['name'] ?? '' ) === $zip_name ) {
				return $asset;
			}
		}

		return null;
	}

	/**
	 * @param string $local_path
	 * @param string $remote_url
	 * @return string|null
	 */
	public function preDownloadRelease( string $local_path, string $remote_url ): ?string
	{
		// Remove any pre-existing file
		@unlink( $local_path );

		// Get the default headers with authentication if needed
		$headers = $this->defaultHeaders();
		if ( empty( $headers ) ) {
			return null;
		}

		// GitHub requires this header to be set for downloading release assets
		$headers['Accept'] = 'application/octet-stream';
		$args              = [
			'timeout'  => 300,
			'stream'   => true,
			'filename' => $local_path,
			'headers'  => $headers,
		];

		// Use wp_safe_remote_get to download the file directly to the specified path
		$response = wp_safe_remote_get( $remote_url, $args );
		if ( !$this->isValidResponse( $response ) ) {
			@unlink( $local_path );

			return null;
		}

		return $local_path;
	}

	/**
	 * @return array
	 */
	public function defaultHeaders(): array
	{
		$headers = [
			'Accept' => 'application/vnd.github+json',
		];

		if ( !empty( $this->personal_access_token ) ) {
			$headers['Authorization'] = 'Bearer ' . $this->personal_access_token;
		}

		return $headers;
	}

	/**
	 * Perform a GitHub API request
	 * @param string $method
	 * @param string $endpoint
	 * @param array $query
	 * @param array $extra_headers
	 * @return array|null
	 */
	private function request( string $method, string $endpoint, array $query = [], array $extra_headers = [] ): ?array
	{
		// Get the default headers with authentication if needed
		$headers = $this->defaultHeaders();
		if ( empty( $headers ) ) {
			return null;
		}

		$url  = $this->getEndpointUrl( $endpoint, $query );
		$args = [ 'headers' => array_merge( $headers, $extra_headers ) ];

		// Use the appropriate WP HTTP function based on the method
		$response = match ( strtoupper( $method ) ) {
			'POST'  => wp_remote_post( $url, $args ),
			default => wp_remote_get( $url, $args ),
		};
		if ( !$this->isValidResponse( $response ) ) {
			return null;
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * @param string $endpoint
	 * @param array $query
	 * @return string
	 */
	public function getEndpointUrl( string $endpoint, array $query = [] ): string
	{
		$url = static::API_BASE . $endpoint;
		if ( empty( $query ) ) {
			return $url;
		}

		return $url . '?' . http_build_query( $query );
	}

	/**
	 * @param $response
	 * @return bool
	 */
	public function isValidResponse( $response ): bool
	{
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $repository
	 * @param array $args
	 * @return array
	 */
	public function getReleases( string $repository, array $args = [] ): array
	{
		$endpoint = '/repos/' . $repository . '/releases';

		return $this->request( 'GET', $endpoint, $args ) ?? [];
	}

	/**
	 * @param string $repository
	 * @param bool $allow_beta
	 * @return array|null
	 */
	public function getLatestRelease( string $repository, bool $allow_beta = false ): ?array
	{
		if ( !$allow_beta ) {
			$endpoint = '/repos/' . $repository . '/releases/latest';

			return $this->request( 'GET', $endpoint );
		}

		return $this->getReleases( $repository, [ 'per_page' => 1 ] )[0] ?? null;
	}

}
