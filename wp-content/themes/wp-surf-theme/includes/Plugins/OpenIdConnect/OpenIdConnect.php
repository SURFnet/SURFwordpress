<?php

namespace SURF\Plugins\OpenIdConnect;

use SURF\Plugins\Plugin;

/**
 * Class OpenIdConnect
 * @package SURF\Plugins\OpenIdConnect
 */
class OpenIdConnect extends Plugin
{

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return _x( 'OpenID Connect', 'plugin', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'openid-connect-generic.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'daggerhart-openid-connect-generic';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/OpenIdConnect/openid-connect-generic-3.9.1.zip' );
	}

	/**
	 * @return void
	 */
	public function afterInstallation(): void
	{
		update_option( 'openid_connect_generic_settings', [
			'login_type'               => 'button',
			'client_id'                => str_replace( 'www.', '', parse_url( get_home_url(), PHP_URL_HOST ) ),
			'client_secret'            => '',
			'scope'                    => 'openid',
			'endpoint_login'           => 'https://connect.surfconext.nl/oidc/authorize',
			'endpoint_userinfo'        => 'https://connect.surfconext.nl/oidc/userinfo',
			'endpoint_token'           => 'https://connect.surfconext.nl/oidc/token',
			'endpoint_end_session'     => '',
			'acr_values'               => '',
			'identity_key'             => 'email',
			'no_sslverify'             => '0',
			'http_request_timeout'     => '5',
			'enforce_privacy'          => '0',
			'alternate_redirect_uri'   => '0',
			'nickname_key'             => 'sub',
			'email_format'             => '{email}',
			'displayname_format'       => '',
			'identify_with_username'   => '0',
			'state_time_limit'         => '',
			'token_refresh_enable'     => '1',
			'link_existing_users'      => '1',
			'create_if_does_not_exist' => '0',
			'redirect_user_back'       => '0',
			'redirect_on_logout'       => '1',
			'enable_logging'           => '0',
			'log_limit'                => '1000',
		] );
	}

}
