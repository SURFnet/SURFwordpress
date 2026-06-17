<?php

namespace SURF\Services;

use Exception;
use SURF\Plugins\Plugin;

/**
 * Class PluginService
 * @package SURF\Services
 */
class PluginService
{

	/**
	 * @return Plugin[]
	 */
	public function plugins(): array
	{
		return array_map( function ( $class )
		{
			return new $class();
		}, surfConfig( 'plugins.installable', [] ) );
	}

	/**
	 * @return array
	 */
	public function installed(): array
	{
		return array_map( function ( $plugin )
		{
			return $plugin->isInstalled();
		}, $this->plugins() );
	}

	/**
	 * @return array
	 */
	public function installable(): array
	{
		return array_map( function ( $plugin )
		{
			return !$plugin->isInstalled();
		}, $this->plugins() );
	}

	/**
	 * @param string $slug
	 * @return Plugin|null
	 */
	public function getPlugin( string $slug ): ?Plugin
	{
		return collect( $this->plugins() )->first( fn( Plugin $plugin ) => $plugin->getSlug() === $slug );
	}

	/**
	 * @param string $slug
	 * @param string $license
	 * @return void
	 * @throws Exception
	 */
	public function installPlugin( string $slug, string $license = '' ): void
	{
		$plugin = $this->getPlugin( $slug );
		if ( empty( $plugin ) ) {
			throw new Exception( _x( 'Plugin was not found.', 'admin', 'wp-surf-theme' ) );
		}

		if ( $plugin->requiresLicense() && empty( $license ) ) {
			throw new Exception( _x( 'Plugin requires a license.', 'admin', 'wp-surf-theme' ) );
		}

		$plugin->setLicenseKey( $license );
		$plugin->install();
	}

}
