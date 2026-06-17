<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\PostTypes\Page;

/**
 * Template Name: Home template
 */
_x( 'Home template', 'template', 'wp-surf-theme' );

/**
 * Class HomeTemplateController
 * @package SURF
 */
class HomeTemplateController extends TemplateController
{

	/**
	 * @param Page $page
	 * @return View
	 */
	public function handle( Page $page ): View
	{
		return $this->view( 'page-templates.home-template', compact( 'page' ) );
	}

}
