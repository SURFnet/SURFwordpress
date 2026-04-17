<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;

/**
 * Class ArchiveController
 * @package SURF
 */
class ArchiveController extends TemplateController
{

	/**
	 * @param PostCollection $posts
	 * @return View
	 */
	public function handle( PostCollection $posts ): View
	{
		return $this->view( 'archive', compact( 'posts' ) );
	}

}
