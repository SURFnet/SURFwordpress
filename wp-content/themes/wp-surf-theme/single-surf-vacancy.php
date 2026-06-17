<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\PostTypes\Vacancy;

/**
 * Class SingleSURFVacancyController
 * @package SURF
 */
class SingleSURFVacancyController extends TemplateController
{

	/**
	 * @param Vacancy $vacancy
	 * @return View
	 */
	public function handle( Vacancy $vacancy ): View
	{
		return $this->view( 'vacancy.single', compact( 'vacancy' ) );
	}

}
