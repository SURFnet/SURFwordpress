<?php

namespace SURF\Helpers;

/**
 * Class CspHelper
 * Stateless building blocks of the theme's Content-Security-Policy: contexts,
 * modes, directives and the array operations on a policy.
 * @package SURF\Helpers
 */
class CspHelper
{

	public const CONTEXT_ADMIN = 'admin';
	public const CONTEXT_FRONT = 'front';

	public const MODE_ENFORCE     = 'enforce';
	public const MODE_REPORT_ONLY = 'report-only';
	public const MODE_DISABLED    = 'disabled';

	public const ACTION_ADD     = 'add';
	public const ACTION_REPLACE = 'replace';

	public const DIRECTIVES = [
		'base-uri',
		'child-src',
		'connect-src',
		'default-src',
		'font-src',
		'form-action',
		'frame-ancestors',
		'frame-src',
		'img-src',
		'manifest-src',
		'media-src',
		'object-src',
		'script-src',
		'script-src-attr',
		'script-src-elem',
		'style-src',
		'style-src-attr',
		'style-src-elem',
		'worker-src',
	];

	/**
	 * @return string[]
	 */
	public static function getContexts(): array
	{
		return [ static::CONTEXT_FRONT, static::CONTEXT_ADMIN ];
	}

	/**
	 * @param string $context
	 * @return string
	 */
	public static function getMode( string $context ): string
	{
		$mode = surfConfig( 'csp.mode', static::MODE_ENFORCE );
		if ( defined( 'SURF_CSP_MODE' ) ) {
			$mode = SURF_CSP_MODE;
		}

		return (string) apply_filters( 'surf_csp_mode', $mode, $context );
	}

	/**
	 * Only an explicit "disabled" turns the policy off: an unknown mode, such as
	 * a typo in SURF_CSP_MODE, falls back to enforcing it.
	 * @param string $context
	 * @return null|string
	 */
	public static function getHeaderName( string $context ): ?string
	{
		$mode = static::getMode( $context );
		if ( $mode === static::MODE_DISABLED ) {
			return null;
		}

		if ( $mode === static::MODE_REPORT_ONLY ) {
			return 'Content-Security-Policy-Report-Only';
		}

		return 'Content-Security-Policy';
	}

	/**
	 * @param string $sources
	 * @return string[]
	 */
	public static function splitSources( string $sources ): array
	{
		return preg_split( '/\s+/', trim( $sources ), -1, PREG_SPLIT_NO_EMPTY );
	}

	/**
	 * A single source expression: no whitespace, and no characters that would
	 * end the directive or the header.
	 * @param string $source
	 * @return bool
	 */
	public static function isValidSource( string $source ): bool
	{
		return $source !== '' && !preg_match( '/[;,\s]/', $source );
	}

	/**
	 * Checks the format only, so config/csp.php and the filter can also use
	 * directives that are not in DIRECTIVES.
	 * @param string $directive
	 * @return bool
	 */
	public static function isValidDirective( string $directive ): bool
	{
		return (bool) preg_match( '/^[a-z]+(-[a-z]+)*$/', $directive );
	}

	/**
	 * Applies `add` and `replace` changes, as used in config/csp.php and the
	 * site overrides, to a set of directives.
	 * @param array $directives
	 * @param array $changes
	 * @return array
	 */
	public static function applyChanges( array $directives, array $changes ): array
	{
		$additions = $changes[ static::ACTION_ADD ] ?? [];
		if ( is_array( $additions ) ) {
			foreach ( $additions as $directive => $sources ) {
				$current                  = $directives[ $directive ] ?? [];
				$directives[ $directive ] = array_merge( (array) $current, (array) $sources );
			}
		}

		$replacements = $changes[ static::ACTION_REPLACE ] ?? [];
		if ( is_array( $replacements ) ) {
			foreach ( $replacements as $directive => $sources ) {
				$directives[ $directive ] = (array) $sources;
			}
		}

		return $directives;
	}

	/**
	 * @param mixed $directives
	 * @return array<string, string[]>
	 */
	public static function normalizeDirectives( mixed $directives ): array
	{
		if ( !is_array( $directives ) ) {
			return [];
		}

		$normalized = [];
		foreach ( $directives as $directive => $sources ) {
			$directive = strtolower( trim( (string) $directive ) );
			if ( !static::isValidDirective( $directive ) ) {
				continue;
			}

			$sources = array_filter( array_map( 'trim', array_map( 'strval', (array) $sources ) ) );

			// Header injection guard: sources may come from the database or a filter
			$sources = array_filter( $sources, [ static::class, 'isValidSource' ] );

			$normalized[ $directive ] = array_values( array_unique( $sources ) );
		}

		ksort( $normalized );

		return $normalized;
	}

	/**
	 * Directives without sources are left out.
	 * @param array<string, string[]> $directives
	 * @param string $separator
	 * @return string
	 */
	public static function formatDirectives( array $directives, string $separator = '; ' ): string
	{
		$parts = [];
		foreach ( $directives as $directive => $sources ) {
			if ( empty( $sources ) ) {
				continue;
			}

			$parts[] = $directive . ' ' . implode( ' ', $sources );
		}

		return implode( $separator, $parts );
	}

}
