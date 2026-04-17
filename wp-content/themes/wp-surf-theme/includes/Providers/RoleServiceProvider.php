<?php

namespace SURF\Providers;

use SURF\Core\Contracts\ServiceProvider;

/**
 * Class RoleServiceProvider
 * @package SURF\Providers
 */
class RoleServiceProvider extends ServiceProvider
{

	/**
	 * @return void
	 */
	public function boot(): void
	{
		add_action( 'init', [ $this, 'registerRoles' ], 10 );
		add_action( 'init', [ $this, 'addCapabilities' ], 11 );
		add_filter( 'editable_roles', [ $this, 'editableRoles' ] );

		add_filter( 'map_meta_cap', function ( $caps, $cap, $userId, $args )
		{
			if ( $cap === 'edit_user' && !current_user_can( 'promote_admins' ) && user_can( $args[0], 'administrator' ) ) {
				return false;
			}

			return $caps;
		}, 10, 4 );
	}

	/**
	 * @return void
	 */
	public function addCapabilities(): void
	{
		$admin = get_role( 'administrator' );
		$admin->add_cap( 'edit_theme_settings' );
		$admin->add_cap( 'promote_admins' );

		// Add site admin capabilities
		$siteAdmin = get_role( 'site_administrator' );
		foreach ( $admin->capabilities as $key => $value ) {
			$siteAdmin->add_cap( $key, $value );
		}
		$siteAdmin->add_cap( 'gform_full_access' );
		$siteAdmin->add_cap( 'install_themes', false );
		$siteAdmin->add_cap( 'switch_themes', false );
		$siteAdmin->add_cap( 'edit_themes', false );
		$siteAdmin->add_cap( 'edit_theme_settings', false );

		$siteAdmin->add_cap( 'activate_plugins', false );
		$siteAdmin->add_cap( 'edit_plugins', false );
		$siteAdmin->add_cap( 'install_plugins', false );

		$siteAdmin->add_cap( 'promote_admins', false );
	}

	/**
	 * @return void
	 */
	public function registerRoles(): void
	{
		add_role( 'site_administrator', 'Site Administrator', [] );
	}

	/**
	 * @param array $roles
	 * @return array
	 */
	public function editableRoles( array $roles ): array
	{
		if ( !current_user_can( 'promote_admins' ) ) {
			unset( $roles['administrator'] );
		}

		return $roles;
	}

}
