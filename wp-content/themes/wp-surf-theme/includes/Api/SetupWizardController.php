<?php

namespace SURF\Api;

use Exception;
use SURF\Core\PostTypes\PostTypeRepository;
use SURF\Core\PostTypes\Registers;
use SURF\Enums\Theme;
use SURF\Helpers\ACFHelper;
use SURF\Plugins\Plugin;
use SURF\Services\PluginService;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class SetupWizardController
 * @package SURF\Api
 */
class SetupWizardController
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
		// Theme setup
		register_rest_route( 'surf/v1', 'setup-wizard/theme', [
			'methods'             => 'GET',
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'callback'            => [ $this, 'theme' ],
		] );

		// Site info setup
		register_rest_route( 'surf/v1', 'setup-wizard/site-info', [
			'methods'             => 'GET',
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'callback'            => [ $this, 'getSiteInfo' ],
		] );

		register_rest_route( 'surf/v1', 'setup-wizard/site-info', [
			'methods'             => 'POST',
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'callback'            => [ $this, 'updateSiteInfo' ],
			'args'                => [
				'blogname'               => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => fn( $value ) => is_string( $value ) && !empty( $value ),
				],
				'blogdescription'        => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => fn( $value ) => is_string( $value ) && !empty( $value ),
				],
				'comment_moderation'     => [
					'required'          => true,
					'sanitize_callback' => 'rest_sanitize_boolean',
					'validate_callback' => fn( $value ) => is_bool( $value ),
				],
				'default_comment_status' => [
					'required'          => true,
					'sanitize_callback' => 'rest_sanitize_boolean',
					'validate_callback' => fn( $value ) => is_bool( $value ),
				],
			],
		] );

		// PostTypes setup
		register_rest_route( 'surf/v1', 'setup-wizard/post-types', [
			'methods'             => 'GET',
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'callback'            => [ $this, 'postTypes' ],
		] );

		register_rest_route( 'surf/v1', 'setup-wizard/update-post-type', [
			'methods'             => 'POST',
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'callback'            => [ $this, 'updatePostType' ],
			'args'                => [
				'post_type' => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => fn( $value ) => is_string( $value ) && !empty( $value ),
				],
				'enabled'   => [
					'required'          => true,
					'sanitize_callback' => 'rest_sanitize_boolean',
					'validate_callback' => fn( $value ) => is_bool( $value ),
				],
			],
		] );

		// Plugins setup
		register_rest_route( 'surf/v1', 'setup-wizard/plugins', [
			'methods'             => 'GET',
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'callback'            => [ $this, 'plugins' ],
		] );

		register_rest_route( 'surf/v1', 'setup-wizard/install-plugin', [
			'methods'             => 'POST',
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'callback'            => [ $this, 'installPlugin' ],
			'args'                => [
				'slug' => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => fn( $value ) => is_string( $value ),
				],
			],
		] );

		// Tracking info setup
		register_rest_route( 'surf/v1', 'setup-wizard/tracking-info', [
			'methods'             => 'GET',
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'callback'            => [ $this, 'getTrackingInfo' ],
		] );

		register_rest_route( 'surf/v1', 'setup-wizard/tracking-info', [
			'methods'             => 'POST',
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'callback'            => [ $this, 'updateTrackingInfo' ],
			'args'                => [
				'piwik_url' => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => fn( $value ) => wp_http_validate_url( $value ),
				],
				'piwik_id'  => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => fn( $value ) => is_string( $value ) && !empty( $value ),
				],
			],
		] );
	}

	/**
	 * @return WP_REST_Response
	 */
	public function theme(): WP_REST_Response
	{
		return new WP_REST_Response( [
			'completed'    => Theme::isSetup(),
			'has_settings' => ACFHelper::usesPro(),
		] );
	}

	/**
	 * @return WP_REST_Response
	 */
	public function getSiteInfo(): WP_REST_Response
	{
		return new WP_REST_Response( [
			'blogname'               => get_option( 'blogname' ),
			'blogdescription'        => get_option( 'blogdescription' ),
			'default_comment_status' => get_option( 'default_comment_status' ) === 'open',
			'comment_moderation'     => (bool) get_option( 'comment_moderation' ),
			'completed'              => (bool) get_option( 'surf_setup_site_info_completed' ),
		] );
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function updateSiteInfo( WP_REST_Request $request ): WP_REST_Response
	{
		update_option( 'blogname', $request->get_param( 'blogname' ) );
		update_option( 'blogdescription', $request->get_param( 'blogdescription' ) );
		update_option( 'default_comment_status', $request->get_param( 'default_comment_status' ) ? 'open' : null );
		update_option( 'comment_moderation', $request->get_param( 'comment_moderation' ) );
		update_option( 'surf_setup_site_info_completed', true );

		return new WP_REST_Response();
	}

	/**
	 * @return WP_REST_Response
	 */
	public function postTypes(): WP_REST_Response
	{
		/** @var PostTypeRepository $repo */
		$repo = surfApp( PostTypeRepository::class );
		$data = collect( $repo->all() )->filter(
			fn( $value, $key ) => in_array( Registers::class, class_uses_recursive( $value ) )
		)->map(
			fn( $value, $key ) => [
				'name'    => $value::getName(),
				'label'   => $value::getPluralLabel(),
				'enabled' => $value::isEnabled(),
			]
		)->values();

		return new WP_REST_Response( $data );
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function updatePostType( WP_REST_Request $request ): WP_REST_Response
	{
		/** @var PostTypeRepository $repo */
		$repo = surfApp( PostTypeRepository::class );
		$cpt  = $repo->find( $request->get_param( 'post_type' ) );

		if ( in_array( Registers::class, class_uses_recursive( $cpt ) ) ) {
			$cpt::enable( $request->get_param( 'enabled' ) );
		}

		return new WP_REST_Response();
	}

	/**
	 * @return WP_REST_Response
	 */
	public function plugins(): WP_REST_Response
	{
		/** @var PluginService $service */
		$service = surfApp( PluginService::class );

		return new WP_REST_Response(
			array_map( function ( $plugin )
			{
				return $plugin->toArray();
			}, $service->plugins() )
		);
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function installPlugin( WP_REST_Request $request ): WP_REST_Response
	{
		/** @var PluginService $service */
		$service = surfApp( PluginService::class );

		try {
			// Collect license, but not sanitized because it's a license key and that can contain anything
			$license = $request->get_param( 'license' );
			$plugin  = sanitize_text_field( $request->get_param( 'slug' ) );
			$service->installPlugin( $plugin, $license );

		} catch ( Exception $exception ) {
			return new WP_REST_Response( [
				'message' => $exception->getMessage(),
				'success' => false,
			], 400 );
		}

		return new WP_REST_Response( [
			'success' => true,
		] );
	}

	/**
	 * @return WP_REST_Response
	 */
	public function getTrackingInfo(): WP_REST_Response
	{
		$value = get_option( 'piwik_pro', [] );
		$data  = [
			'piwik_url' => $value['url'] ?? '',
			'piwik_id'  => $value['id'] ?? '',
			'completed' => ( $value['url'] ?? null ) && ( $value['id'] ?? null ),
		];

		return new WP_REST_Response( $data );
	}

	/**
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function updateTrackingInfo( WP_REST_Request $request ): WP_REST_Response
	{
		$option_key   = 'piwik_pro';
		$value        = get_option( $option_key, [] );
		$value['url'] = $request->get_param( 'piwik_url' );
		$value['id']  = $request->get_param( 'piwik_id' );
		update_option( $option_key, $value );

		return new WP_REST_Response();
	}

}
