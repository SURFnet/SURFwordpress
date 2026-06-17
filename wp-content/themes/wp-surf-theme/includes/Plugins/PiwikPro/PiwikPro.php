<?php

namespace SURF\Plugins\PiwikPro;

use SURF\Plugins\Plugin;

/**
 * Class PiwikPro
 * @package SURF\Plugins\PiwikPro
 */
class PiwikPro extends Plugin
{

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'plugin.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'piwik-pro';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/PiwikPro/' . $this->getSlug() . '.zip' );
	}

}
