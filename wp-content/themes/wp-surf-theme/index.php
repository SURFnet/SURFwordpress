<?php

namespace SURF;

use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;
use Illuminate\Contracts\View\View;

/**
 * Class IndexController
 * @package SURF
 */
class IndexController extends TemplateController
{

	/**
	 * @param Application $app
	 * @param PostCollection $posts
	 * @return View
	 */
	public function handle( Application $app, PostCollection $posts ): View
	{
		return $this->view( 'index', compact( 'app', 'posts' ) );
	}

}
