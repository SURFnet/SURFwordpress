<?php

namespace SURF\Plugins\Polylang;

use SURF\Plugins\Plugin;

/**
 * Class Polylang
 * @package SURF\Plugins\Polylang
 */
class Polylang extends Plugin
{

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'polylang.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'polylang';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/Polylang/' . $this->getSlug() . '.zip' );
	}

}
