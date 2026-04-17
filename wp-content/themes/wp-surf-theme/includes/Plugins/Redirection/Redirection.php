<?php

namespace SURF\Plugins\Redirection;

use SURF\Plugins\Plugin;

/**
 * Class Redirection
 * @package SURF\Plugins\Redirection
 */
class Redirection extends Plugin
{

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'redirection.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'redirection';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/Redirection/' . $this->getSlug() . '.zip' );
	}

	/**
	 * @return string
	 */
	public function getInstructions(): string
	{
		return sprintf(
			_x(
				'This plugin needs to be configured after activating. <a href="%s" target="_blank">Click here to open the setup in a new tab</a>.',
				'admin',
				'wp-surf-theme'
			),
			add_query_arg( [ 'page' => $this->getPluginFile() ], admin_url( 'tools.php' ) )
		);
	}

}
