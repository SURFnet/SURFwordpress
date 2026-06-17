<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;

/**
 * Class PublicationServiceProvider
 * @package SURF\Providers
 */
class PublicationServiceProvider extends ServiceProvider
{

	public const META_KEY = 'depublication_date';

	/**
	 * @return void
	 */
	public function register(): void
	{
		add_action( 'surf_depublish_post', [ $this, 'depublishPost' ] );
		add_filter( 'acf/update_value/name=' . static::META_KEY, [ $this, 'scheduleDepublication' ], 10, 2 );
	}

	/**
	 * @param mixed $value
	 * @param int $postId
	 * @return mixed
	 */
	public function scheduleDepublication( mixed $value, int $postId )
	{
		wp_clear_scheduled_hook( 'surf_depublish_post', [ $postId ] );

		if ( $value ) {
			wp_schedule_single_event( strtotime( $value ), 'surf_depublish_post', [ $postId ] );
		}

		return $value;
	}

	/**
	 * @param int $postId
	 * @return void
	 */
	public function depublishPost( int $postId ): void
	{
		wp_update_post( [ 'ID' => $postId, 'post_status' => 'draft' ] );
		update_post_meta( $postId, static::META_KEY, null );
	}

}
