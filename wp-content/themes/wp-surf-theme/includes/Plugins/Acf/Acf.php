<?php

namespace SURF\Plugins\Acf;

use Exception;
use SURF\Plugins\Plugin;

/**
 * Class Acf
 * @package SURF\Plugins\Acf
 */
class Acf extends Plugin
{

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return _x( 'ACF', 'plugin', 'wp-surf-theme' );
	}

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'acf.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'advanced-custom-fields';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/Acf/' . $this->getSlug() . '.zip' );
	}

}
