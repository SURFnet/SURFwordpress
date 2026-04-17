<?php

namespace SURF\Hooks;

use SURF\Helpers\Helper;
use SURF\PostTypes\Agenda;
use WP_Query;

/**
 * Class AgendaHooks
 * @package SURF\Hooks
 */
class AgendaHooks
{

	/**
	 * @return void
	 */
	public static function register(): void
	{
		add_action( 'pre_get_posts', [ static::class, 'orderAgendaByDate' ] );
	}

	/**
	 * @param WP_Query $query
	 * @return void
	 */
	public static function orderAgendaByDate( WP_Query $query ): void
	{
		if ( is_admin() || $query->get( 'surf_skip_hooks', false ) || !$query->is_post_type_archive( Agenda::getName() ) ) {
			return;
		}

		$hideExpired = false;
		if ( Agenda::hideExpired() ) {
			$extra = Helper::getSanitizedGet( 'past-events', [] );
			if ( is_string( $extra ) ) {
				$extra = explode( ',', $extra );
			}

			$hideExpired = !in_array( 'show', $extra );
		}

		$ids = Agenda::orderedByStartDate( $hideExpired );

		// Respect existing exclusions
		$exclude = (array) $query->get( 'post__not_in', [] );
		if ( !empty( $exclude ) ) {
			$ids = array_values( array_diff( $ids, $exclude ) );
		}

		// if an empty is used for post__in, it will return all posts.
		$ids = !empty( $ids ) ? $ids : [ 0 ];

		$query->set( 'post__in', $ids );
		$query->set( 'orderby', 'post__in' );
		$query->set( 'order', 'ASC' );
	}

}
