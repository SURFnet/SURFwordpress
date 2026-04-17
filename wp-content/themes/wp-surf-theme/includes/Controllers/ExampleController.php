<?php

namespace SURF\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use SURF\Core\Controllers\Controller;

/**
 * Class ExampleController
 * @package SURF\Controllers
 */
class ExampleController extends Controller
{

	/**
	 * @param Request $request
	 * @return View
	 */
	public function show( Request $request ): View
	{
		return $this->view( 'example', compact( 'request' ) );
	}

}
