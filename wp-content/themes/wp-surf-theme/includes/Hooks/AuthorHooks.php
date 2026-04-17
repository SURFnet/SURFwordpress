<?php

namespace SURF\Hooks;

/**
 * Class AuthorHooks
 * @package SURF\Hooks
 */
class AuthorHooks
{

	/**
	 * @return void
	 */
	public static function register(): void
	{
		add_action( 'template_redirect', [ static::class, 'maybeRedirectPage' ] );
	}

	/**
	 * @return void
	 */
	public static function maybeRedirectPage(): void
	{
		if ( !is_author() ) {
			return;
		}

		$user_id  = (int) get_query_var( 'author' );
		$disabled = (bool) get_user_meta( $user_id, 'disable_archive', true );
		if ( $disabled ) {
			global $wp_query;
			// Redirect to 404 error page
			$wp_query->set_404();
			status_header( 404 );
		}
	}

}
