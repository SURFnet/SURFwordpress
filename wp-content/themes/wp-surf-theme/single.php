<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\BasePost;

/**
 * Class SingleController
 * @package SURF
 */
class SingleController extends TemplateController
{

	/**
	 * @param BasePost $post - Post type is unknown in single.php so we fall back to injecting BasePost
	 * @return View
	 */
	public function handle( BasePost $post ): View
	{
		return $this->view( 'single', compact( 'post' ) );
	}

}
