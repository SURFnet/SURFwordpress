<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use SURF\Core\Controllers\TemplateController;
use SURF\View\ViewModels\ErrorPageViewModel;

/**
 * Class Error404Controller
 * @package SURF
 */
class Error404Controller extends TemplateController
{

	/**
	 * @param Request $request
	 * @return View
	 */
	public function handle( Request $request ): View
	{
		$errorView = new ErrorPageViewModel();

		return $this->view( '404', compact( 'errorView' ) );
	}

}
