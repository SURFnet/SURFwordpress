<?php

namespace SURF;

use Illuminate\Contracts\View\View;
use SURF\Core\Controllers\TemplateController;
use SURF\Core\PostTypes\PostCollection;

/**
 * Class TaxonomySURFAgendaCategoryController
 * @package SURF
 */
class TaxonomySURFAgendaCategoryController extends TemplateController
{

	/**
	 * @param PostCollection $events
	 * @return View
	 */
	public function handle( PostCollection $events ): View
	{
		return $this->view( 'agenda.archive-category', compact( 'events' ) );
	}

}
