<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;

/**
 * Class TaxonomySURFVacancyCategoryController
 * @package SURF
 */
class TaxonomySURFVacancyCategoryController extends TemplateController
{

	/**
	 * @param PostCollection $vacancies
	 * @return View
	 */
	public function handle( PostCollection $vacancies ): View
	{
		return $this->view( 'vacancy.archive-taxonomy', compact( 'vacancies' ) );
	}

}
