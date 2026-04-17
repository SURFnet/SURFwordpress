<?php

namespace SURF\Api;

use Illuminate\Contracts\Container\BindingResolutionException;
use SURF\Services\CspService;
use WP_REST_Request;

/**
 * Class SyncCspController
 * @package SURF\Api
 */
class SyncCspController
{

	/**
	 * @return void
	 */
	public function register(): void
	{
		add_action( 'rest_api_init', [ $this, 'registerRoutes' ] );
	}

	/**
	 * @return void
	 */
	public function registerRoutes(): void
	{
		register_rest_route( 'surf/v1', 'request-csp-sync', [
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => [ $this, 'syncCsp' ],
			'args'                => [
				'username' => [
					'required'          => true,
					'validate_callback' => fn( $value ) => is_string( $value ) && !empty( $value ),
				],
				'password' => [
					'required'          => true,
					'validate_callback' => fn( $value ) => is_string( $value ) && !empty( $value ),
				],
			],
		] );
	}

	/**
	 * @param WP_REST_Request $request
	 * @return void
	 * @throws BindingResolutionException
	 */
	public function syncCsp( WP_REST_Request $request ): void
	{
		$credentials = [
			'user_login'    => $request->get_param( 'username' ),
			'user_password' => $request->get_param( 'password' ),
		];

		$user = wp_signon( $credentials );
		if ( is_wp_error( $user ) ) {
			wp_send_json_error( $user->get_error_message(), 401 );
		}

		if ( !in_array( 'administrator', $user->roles ) ) {
			wp_send_json_error( _x( 'You are not authorized to perform this action', 'rest', 'wp-surf-theme' ), 401 );
		}

		$cspService = new CspService();
		$cspService->syncCsp();
		wp_send_json_success( _x( 'CSP Synced successfully', 'rest', 'wp-surf-theme' ) );
	}

}
