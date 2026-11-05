<?php

namespace SURF\Services;

use SURF\Admin\MetaBoxes\CspSettings;
use SURF\Helpers\CspHelper;

/**
 * Class CspService
 * Builds and sends the Content-Security-Policy header. The policy is
 * assembled from config/csp.php, the site rules from the CSP settings page and
 * finally the `surf_csp_directives` filter.
 * Settings of the abandoned CSP Manager plugin are migrated once into the
 * site rules, as additions only.
 * @package SURF\Services
 */
class CspService
{

	public const OPTION_MIGRATED = 'surf_csp_legacy_migrated';

	public const LEGACY_PLUGIN  = 'csp-manager/csp-manager.php';
	public const LEGACY_OPTIONS = [
		'csp_manager_admin'    => CspHelper::CONTEXT_ADMIN,
		'csp_manager_loggedin' => CspHelper::CONTEXT_FRONT,
		'csp_manager_frontend' => CspHelper::CONTEXT_FRONT,
	];

	/**
	 * Never carried over from legacy settings: polyfill.io was taken over in 2024
	 * and served malware, and it was part of the plugin's old default policy.
	 */
	public const LEGACY_BLOCKED_SOURCES = [
		'polyfill.io',
		'cdn.polyfill.io',
	];

	/**
	 * @return void
	 */
	public static function register(): void
	{
		add_action( 'send_headers', [ static::class, 'sendFrontHeader' ] );
		add_action( 'login_init', [ static::class, 'sendFrontHeader' ] );
		add_action( 'admin_init', [ static::class, 'sendAdminHeader' ] );
		add_action( 'admin_init', [ static::class, 'migrateLegacySettings' ] );
		add_action( 'surf_deploy', [ static::class, 'migrateLegacySettings' ] );
	}

	/**
	 * @return void
	 */
	public static function sendFrontHeader(): void
	{
		static::sendHeader( CspHelper::CONTEXT_FRONT );
	}

	/**
	 * @return void
	 */
	public static function sendAdminHeader(): void
	{
		static::sendHeader( CspHelper::CONTEXT_ADMIN );
	}

	/**
	 * @param string $context
	 * @return void
	 */
	public static function sendHeader( string $context ): void
	{
		if ( headers_sent() || static::isLegacyPluginActive() ) {
			return;
		}

		$name = CspHelper::getHeaderName( $context );
		if ( $name === null ) {
			return;
		}

		$value = static::getHeaderValue( $context );
		if ( $value === '' ) {
			return;
		}

		header( $name . ': ' . $value );
	}

	/**
	 * @param string $context
	 * @return string
	 */
	public static function getHeaderValue( string $context ): string
	{
		return CspHelper::formatDirectives( static::getDirectives( $context ) );
	}

	/**
	 * The complete policy: theme, site rules and the `surf_csp_directives` filter.
	 * @param string $context
	 * @return array<string, string[]>
	 */
	public static function getDirectives( string $context ): array
	{
		$directives = static::getThemeDirectives( $context );
		$directives = CspHelper::applyChanges( $directives, CspSettings::getRules( $context ) );
		$directives = apply_filters( 'surf_csp_directives', $directives, $context );

		return CspHelper::normalizeDirectives( $directives );
	}

	/**
	 * The policy from config/csp.php only, without site rules and filters.
	 * @param string $context
	 * @return array<string, string[]>
	 */
	public static function getThemeDirectives( string $context ): array
	{
		$directives = surfConfig( 'csp.directives', [] );
		$directives = CspHelper::applyChanges( $directives, surfConfig( 'csp.contexts.' . $context, [] ) );

		return CspHelper::normalizeDirectives( $directives );
	}

	/**
	 * Whether the abandoned CSP Manager plugin is still active. It sends its own
	 * header based on its own (possibly customised) settings, and two policies
	 * would both be enforced by the browser.
	 * @return bool
	 */
	public static function isLegacyPluginActive(): bool
	{
		if ( !function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( static::LEGACY_PLUGIN );
	}

	/**
	 * Copies sources from the CSP Manager plugin settings that the theme policy
	 * does not already contain into the site rules, so a site keeps working
	 * when it switches to the theme policy. Only additions are copied:
	 * replacing whole directives would freeze the site on its old policy and
	 * stop it from receiving theme updates. Runs once per site.
	 * @return void
	 */
	public static function migrateLegacySettings(): void
	{
		if ( get_option( static::OPTION_MIGRATED ) ) {
			return;
		}

		$missing = [];
		foreach ( static::LEGACY_OPTIONS as $option => $context ) {
			$legacy = get_option( $option );
			if ( !is_array( $legacy ) ) {
				continue;
			}

			$theme = static::getThemeDirectives( $context );
			foreach ( static::parseLegacyDirectives( $legacy ) as $directive => $sources ) {
				$sources = array_diff( $sources, $theme[ $directive ] ?? [] );
				if ( empty( $sources ) || !in_array( $directive, CspHelper::DIRECTIVES, true ) ) {
					continue;
				}

				$current                           = $missing[ $context ][ $directive ] ?? [];
				$missing[ $context ][ $directive ] = array_values( array_unique( array_merge( $current, $sources ) ) );
			}
		}

		foreach ( $missing as $context => $directives ) {
			CspSettings::appendRules( $context, CspHelper::ACTION_ADD, $directives );
		}

		// Autoloaded, because it is checked on every admin request
		update_option( static::OPTION_MIGRATED, time(), true );
	}

	/**
	 * The plugin stores each directive as a space separated string, with a
	 * separate `enable_<directive>` flag.
	 * @param array $legacy
	 * @return array<string, string[]>
	 */
	public static function parseLegacyDirectives( array $legacy ): array
	{
		$directives = [];
		foreach ( $legacy as $key => $value ) {
			if ( !is_string( $key ) || !str_starts_with( $key, 'enable_' ) || (string) $value !== '1' ) {
				continue;
			}

			$directive = substr( $key, strlen( 'enable_' ) );
			$sources   = CspHelper::splitSources( (string) ( $legacy[ $directive ] ?? '' ) );

			$directives[ $directive ] = array_diff( $sources, static::LEGACY_BLOCKED_SOURCES );
		}

		return CspHelper::normalizeDirectives( $directives );
	}

}
