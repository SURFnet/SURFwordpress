<?php

namespace SURF\Core\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Class Controller
 * @package SURF\Core\Controllers
 */
abstract class Controller
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
