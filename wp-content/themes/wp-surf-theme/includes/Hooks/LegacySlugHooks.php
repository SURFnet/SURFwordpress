<?php

namespace SURF\Hooks;

use SURF\PostTypes\Agenda;
use SURF\PostTypes\Vacancy;
use SURF\Taxonomies\AgendaLocation;

/**
 * Class LegacySlugHooks
 * Redirects URLs with the slugs of the VO Theme, where the WP SURF Theme uses a translated slug
 * (like "/vacancy/" → "/vacature/" and "/agenda/location/" → "/agenda/locatie/" on Dutch sites).
 * @package SURF\Hooks
 */
class LegacySlugHooks
{

	/**
	 * @return void
	 */
	public static function register(): void
	{
		// Before redirect_canonical(), which runs on priority 10
		add_action( 'template_redirect', [ static::class, 'redirectLegacySlugs' ], 5 );
	}

	/**
	 * Lists the URL prefixes as the VO Theme used them, with the current prefix of the WP SURF Theme
	 * Only prefixes that actually changed are listed.
	 * @return string[] [ old prefix => new prefix ]
	 */
	public static function listLegacyPrefixes(): array
	{
		$list = [];

		// Vacancies: the VO Theme did not translate the slug "vacancy"
		$vacancy = get_post_type_object( Vacancy::getName() );
		if ( !empty( $vacancy->rewrite['slug'] ) ) {
			$list['vacancy'] = $vacancy->rewrite['slug'];
		}

		// Agenda locations: the VO Theme did not translate the slug "location"
		$agenda   = get_post_type_object( Agenda::getName() );
		$location = get_taxonomy( AgendaLocation::getName() );
		if ( !empty( $agenda->rewrite['slug'] ) && !empty( $location->rewrite['slug'] ) ) {
			$list[ $agenda->rewrite['slug'] . '/location' ] = $location->rewrite['slug'];
		}

		$result = [];
		foreach ( $list as $old => $new ) {
			$old = trim( $old, '/' );
			$new = trim( $new, '/' );
			if ( $old !== $new ) {
				$result[ $old ] = $new;
			}
		}

		return $result;
	}

	/**
	 * Redirects a 404 with an old slug to the same URL with the current slug (301)
	 * @return void
	 */
	public static function redirectLegacySlugs(): void
	{
		if ( !is_404() ) {
			return;
		}

		$prefixes = static::listLegacyPrefixes();
		if ( empty( $prefixes ) ) {
			return;
		}

		$path = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );

		// Leave out the folder of a WordPress installation in a subfolder
		$home_path = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
		if ( $home_path !== '' && str_starts_with( $path, $home_path . '/' ) ) {
			$path = substr( $path, strlen( $home_path ) + 1 );
		}

		foreach ( $prefixes as $old => $new ) {
			if ( $path !== $old && !str_starts_with( $path, $old . '/' ) ) {
				continue;
			}

			$target = home_url( user_trailingslashit( $new . substr( $path, strlen( $old ) ) ) );

			$query = $_SERVER['QUERY_STRING'] ?? '';
			if ( $query !== '' ) {
				$target .= '?' . $query;
			}

			wp_safe_redirect( $target, 301, 'WP SURF Theme' );
			exit;
		}
	}

}
