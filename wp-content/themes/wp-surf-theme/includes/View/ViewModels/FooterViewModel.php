<?php

namespace SURF\View\ViewModels;

use SURF\Helpers\PolylangHelper;

/**
 * Class FooterView
 * Example loading Polylang theme option
 * @package SURF\View\ViewModels
 */
class FooterViewModel
{

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return PolylangHelper::getThemeOption( 'footer_title' );
	}

}
