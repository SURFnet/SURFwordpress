<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\PostTypes\Page;

/**
 * Class PageController
 * @package SURF
 */
class PageController extends TemplateController
{

	/**
	 * @param Page $page
	 * @return View
	 */
	public function handle( Page $page ): View
	{
		return $this->view( 'page', compact( 'page' ) );
	}

}
