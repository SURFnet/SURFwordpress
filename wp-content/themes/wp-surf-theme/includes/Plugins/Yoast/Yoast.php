<?php

namespace SURF\Plugins\Yoast;

use SURF\Plugins\Plugin;

/**
 * Class Yoast
 * @package SURF\Plugins\Yoast
 */
class Yoast extends Plugin
{

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/Yoast/' . $this->getSlug() . '.zip' );
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'wordpress-seo';
	}

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'wp-seo.php';
	}

}
