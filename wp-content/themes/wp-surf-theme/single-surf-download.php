<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\PostTypes\Download;

/**
 * Class SingleSURFDownloadController
 * @package SURF
 */
class SingleSURFDownloadController extends TemplateController
{

	/**
	 * @param Download $download
	 * @return View
	 */
	public function handle( Download $download ): View
	{
		return $this->view( 'download.single', compact( 'download' ) );
	}

}
