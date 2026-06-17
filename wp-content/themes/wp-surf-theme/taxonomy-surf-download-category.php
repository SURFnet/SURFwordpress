<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;

/**
 * Class TaxonomySURFDownloadCategoryController
 * @package SURF
 */
class TaxonomySURFDownloadCategoryController extends TemplateController
{

	/**
	 * @param PostCollection $downloads
	 * @return View
	 */
	public function handle( PostCollection $downloads ): View
	{
		return $this->view( 'download.archive-taxonomy', compact( 'downloads' ) );
	}

}
