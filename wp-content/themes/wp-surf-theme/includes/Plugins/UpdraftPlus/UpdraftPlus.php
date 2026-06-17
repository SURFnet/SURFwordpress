<?php

namespace SURF\Plugins\UpdraftPlus;

use SURF\Plugins\Plugin;

/**
 * Class UpdraftPlus
 * @package SURF\Plugins\UpdraftPlus
 */
class UpdraftPlus extends Plugin
{

	/**
	 * @return string
	 */
	public function getPluginFile(): string
	{
		return 'updraftplus.php';
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return 'updraftplus';
	}

	/**
	 * @return string
	 */
	public function getZipPath(): string
	{
		return surfPath( 'includes/Plugins/UpdraftPlus/' . $this->getSlug() . '.zip' );
	}

}
