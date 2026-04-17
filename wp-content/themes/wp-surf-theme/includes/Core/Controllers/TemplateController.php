<?php

namespace SURF\Core\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Class TemplateController
 * @package SURF\Core\Controllers
 */
abstract class TemplateController
{

	/**
	 * @param string $view
	 * @param array $data
	 * @return View
	 */
	public function view( string $view, array $data = [] ): View
	{
		return surfView( $view, $data );
	}

}
